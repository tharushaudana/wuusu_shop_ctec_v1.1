<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManageSupplierRequest;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        return response()->success(['suppliers' => Supplier::all()]);
    }

    public function store(ManageSupplierRequest $request)
    {
        $supplier = Supplier::create($request->all());

        return response()->success(['supplier' => $supplier]);
    }

    public function show(Supplier $supplier)
    {
        return response()->success(['supplier' => $supplier]);
    }

    public function update(ManageSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->all());

        return response()->success(['supplier' => $supplier]);
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return response()->success();
    }
}
