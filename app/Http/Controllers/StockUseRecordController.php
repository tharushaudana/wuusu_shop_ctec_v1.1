<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockUseRecordRequest;
use App\Http\Resources\PaginatedResource;
use App\Models\Product;
use App\Models\StockUseRecord;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockUseRecordController extends Controller
{
    public function index(Request $request) 
    {
        $addfilter = $request->addfilter;

        $paginate = StockUseRecord::where(function (Builder $query) use ($addfilter) {
            $addfilter($query);
        });

        $paginate = $paginate->where('type', config('stockusetypes.MANUAL'))->orderBy('id', 'desc')->paginate(50);

        $paginate->getCollection()->each(function ($item) {
            $item->makeHidden('type');
        });

        return response()->success(new PaginatedResource($paginate));
    }

    public function store(StoreStockUseRecordRequest $request) 
    {
        $availableqty = Product::find($request->product_id)->qty;

        if ($availableqty < $request->qty) {
            return response()->error('Only '.$availableqty.' available. There aren\'t enough of quatity to do this!', 200);
        }

        $record = StockUseRecord::doUse(
            Auth::user()->id, 
            $request->product_id, 
            config('stockusetypes.MANUAL'), 
            $request->qty, 
            $request->description
        );

        return response()->success(['record' => $record]);
    }
}
