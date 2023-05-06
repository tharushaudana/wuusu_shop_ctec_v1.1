<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockRecordRequest;
use App\Http\Resources\PaginatedResource;
use App\Models\StockRecord;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;

class StockRecordController extends Controller
{
    public function index(Request $request) 
    {
        $addfilter = $request->addfilter;

        $paginate = StockRecord::where(function (Builder $query) use ($addfilter) {
            $addfilter($query);
        });

        $paginate = $paginate->where('supplier_id', '!=', null)->orderBy('id', 'desc')->paginate(50);

        return response()->success(new PaginatedResource($paginate));
    }

    public function store(StoreStockRecordRequest $request) 
    {
        $record = StockRecord::create([
            'product_id' => $request->product_id,
            'supplier_id' => $request->supplier_id,
            'qty' => $request->qty,
            'availableqty' => $request->qty
        ]);

        return response()->success(['record' => $record]);
    }
}
