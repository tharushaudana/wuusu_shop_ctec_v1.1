<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManageCustomerRequest;
use App\Http\Resources\PaginatedResource;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\Sale;

class CustomerController extends Controller
{
    public function index()
    {
        /*$paginate = Customer::orderBy('id', 'desc')->paginate(2);

        return response()->success(new PaginatedResource($paginate));*/

        $customers = Customer::orderBy('id', 'desc')->get();

        return response()->success(['customers' => $customers]);
    }

    public function store(ManageCustomerRequest $request)
    {
        $customer = Customer::create($request->all());

        return response()->success(['customer' => $customer]);
    }

    public function show(Customer $customer)
    {
        return response()->success(['customer' => $customer]);
    }

    public function update(ManageCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->all());

        return response()->success(['customer' => $customer]);
    }

    public function destroy(Customer $customer)
    {
        $sale_count = Sale::where('customer_id', $customer->id)->count();
        $quotation_count = Quotation::where('customer_id', $customer->id)->count();
        
        if ($sale_count == 0 && $quotation_count == 0) {
            $customer->delete();
            return response()->success();
        } else {
            return response()->error('Unable to delete. Because the Customer has relations.', 301);
        }
    }
}
