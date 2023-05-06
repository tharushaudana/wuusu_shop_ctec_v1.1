<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManageProductRequest;
use App\Http\Resources\PaginatedResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        /*$paginate = Product::orderBy('id', 'desc')->paginate(2);

        return response()->success(new PaginatedResource($paginate));*/

        $products = Product::orderBy('id', 'desc')->get();

        return response()->success(['products' => $products]);
    }

    public function store(ManageProductRequest $request)
    {        
        $product = Product::create($request->all());

        return response()->success(['product' => $product]);
    }

    public function show(Product $product)
    {
        return response()->success(['product' => $product]);
    }

    public function update(ManageProductRequest $request, Product $product)
    {
        $product->update($request->all());

        return response()->success(['product' => $product]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->success();
    }
}
