<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManageSaleRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        //$paginate = Sale::orderBy('id', 'desc')->paginate(2);

        //return response()->success(new PaginatedResource($paginate));

        $addfilter = $request->addfilter;

        $paginate = Sale::where(function (Builder $query) use ($addfilter) {
            $addfilter($query);
        });

        $paginate = $paginate->orderBy('id', 'desc')->paginate(50);

        return response()->success(new PaginatedResource($paginate));
    }

    public function store(ManageSaleRequest $request)
    {
        $sale = Sale::create([
            'invoice_id' => Sale::generateInvoiceId(),
            'customer_id' => $request->customer_id,
            'user_id' => Auth::user()->id,
            'title' => $request->title,
        ]);

        $sale->sale_data()->set($request->sale_data);

        return response()->success(['sale' => $sale]);
    }

    public function show(Sale $sale)
    {
        return response()->success(['sale' => new SaleResource($sale)]);
    }

    public function update(ManageSaleRequest $request, Sale $sale)
    {
        $sale->sale_data()->update($request->sale_data);

        //### for update 'updated_at' attribute.
        $sale->touch(); 

        return response()->success();
    }

    public function destroy(Sale $sale)
    {
        //...
    }
}
