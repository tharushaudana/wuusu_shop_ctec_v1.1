<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManageQuotationRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\QuotationResource;
use App\Models\Quotation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        //$paginate = Quotation::orderBy('id', 'desc')->paginate(2);

        //return response()->success(new PaginatedResource($paginate));

        $addfilter = $request->addfilter;

        $paginate = Quotation::where(function (Builder $query) use ($addfilter) {
            $addfilter($query);
        });

        $paginate = $paginate->orderBy('id', 'desc')->paginate(50);

        return response()->success(new PaginatedResource($paginate));
    }

    public function store(ManageQuotationRequest $request)
    {
        $quotation = Quotation::create([
            'invoice_id' => Quotation::generateInvoiceId(),
            'customer_id' => $request->customer_id,
            'user_id' => Auth::user()->id,
            'title' => $request->title,
            'validuntil' => $request->validuntil
        ]);

        $quotation->quotation_data()->set($request->quotation_data);

        return response()->success(['quotation' => $quotation]);
    }

    public function show(Quotation $quotation)
    {
        return response()->success(['quotation' => new QuotationResource($quotation)]);
    }

    public function update(ManageQuotationRequest $request, Quotation $quotation)
    {
        $quotation->quotation_data()->update($request->quotation_data);

        //### for update 'updated_at' attribute.
        $quotation->touch(); 

        return response()->success();
    }

    public function destroy(Quotation $quotation)
    {
        //...
    }
}
