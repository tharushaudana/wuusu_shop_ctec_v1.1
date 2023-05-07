<?php

namespace App\Http\Controllers\web;

use App\Exports\ActivitiesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityFilterRequest;
use App\Models\User;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ActivityController extends Controller
{
    public function index(ActivityFilterRequest $request, User $user = null)
    {
        if (!is_null($user) && $user->id == Auth::user()->id) {
            $audits = $this->retriveAudits($user);
            
            return response()->success([
                'user' => $user,
                'audits' => $audits->orderBy('id', 'desc')->paginate(13),
            ]);
        } else {
            $audits = $this->retriveAudits($user);

            return response()->success([
                'user' => $user,
                'audits' => $audits->orderBy('id', 'desc')->paginate(13),
            ]);
        }
    }

    public function show(User $user, Audit $audit)
    {
        if ($user->id == Auth::user()->id) {
            return redirect(route('web.pages.my.activity', $audit->id));
        }

        if (!is_null($user->id)) {
            //### for routes like: /users/4/activities/20  (activity of other user)
            if ($audit->user_id != $user->id) abort(404);
        } else {
            //### for routes like: /my/activities/34       (self activity)
            if ($audit->user_id != Auth::user()->id) abort(404);
        }

        return view('dashboard.pages.activity', [
            'audit' => $audit
        ]);
    }

    public function filter(ActivityFilterRequest $request) {
        $url = $request->fullUrlWithQuery($request->only('filter_type', 'date', 'date_start', 'date_end'));

        $url = str_replace('/filter', '', $url);

        return redirect($url);
    }

    public function download(ActivityFilterRequest $request, User $user = null) 
    {
        $audits = $this->retriveAudits($user)->orderBy('id', 'desc')->get();
        
        $title = $this->generateFileTitle();
        $filename = $this->generateFileName($user);

        return Excel::download(new ActivitiesExport($audits, $title), $filename);
    }

    //### Private functions

    private function retriveAudits($user) {
        $audits = is_null($user) ? Auth::user()->audits() : $user->audits();

        //### do filtering
        $filter_type = $this->getParam('filter_type');
        $date        = $this->getParam('date');
        $date_start  = $this->getParam('date_start');
        $date_end    = $this->getParam('date_end');

        if ($filter_type == 'single' && !is_null($date)) {
            $audits->whereDate('created_at', '=', $date);
        } else if ($filter_type == 'range') {
            if (!is_null($date_start)) $audits->whereDate('created_at', '>=', $date_start);
            if (!is_null($date_end)) $audits->whereDate('created_at', '<=', $date_end);
        }

        return $audits;
    }

    private function generateFileTitle() {
        $title = 'Activities';

        if (!is_null($this->getParam('date'))) {
            $title .= ' on '.$this->getParam('date');
        } else {
            if (!is_null($this->getParam('date_start')) && is_null($this->getParam('date_end'))) {
                $title .= ' from '.$this->getParam('date_start');
            } else if (!is_null($this->getParam('date_end')) && is_null($this->getParam('date_start'))) {
                $title .= ' till '.$this->getParam('date_end');
            } else if (!is_null($this->getParam('date_start')) && !is_null($this->getParam('date_end'))) {
                $title .= ' from '.$this->getParam('date_start').' till '.$this->getParam('date_end');
            }
        }

        if (is_null($this->getParam('date')) && is_null($this->getParam('date_start')) && is_null($this->getParam('date_end'))) {
            $title .= ' till '.date('Y-m-d');
        }

        return $title;
    }

    private function generateFileName($user) {
        if (is_null($user)) $name = 'my-activities-';
        else $name = str_replace(' ', '-', strtolower($user->name)).'-activities-';
        
        $name .= date('Y-m-d');
        $name .= '.xlsx';

        return $name;
    }

    private function getParam($name) {
        if (request()->isMethod('GET')) {
            return request()->query($name);
        }

        return request()->input($name);
    }
}
