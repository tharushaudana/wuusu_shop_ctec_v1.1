<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockRecordController;
use App\Http\Controllers\StockUseRecordController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Models\Customer;
use App\Models\Material;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Bind values with route parameters
|--------------------------------------------------------------------------
*/

Route::bind('_user', function ($id) {
    return User::find($id);
});

Route::bind('_product', function ($id) {
    return Product::find($id);
});

Route::bind('_material', function ($id) {
    return Material::find($id);
});

Route::bind('_supplier', function ($id) {
    return Supplier::find($id);
});

Route::bind('_customer', function ($id) {
    return Customer::find($id);
});

Route::bind('_sale', function ($id) {
    return Sale::where('id', $id)->orWhere('invoice_id', $id)->first();
});

Route::bind('_quotation', function ($id) {
    return Quotation::where('id', $id)->orWhere('invoice_id', $id)->first();
});


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/**** Public routes ****/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/**** Protected routes (Authenticated) ****/
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/tt', function () {
        return Auth::user()->currentAccessToken()->id;
    });

    //### User
    Route::get('/me', function (Request $request) {
        return response()->success(['user' => Auth::user()]);
    });

    //### Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    //### Users
    Route::prefix('/users')->group(function () {
        Route::get('/', [UserController::class, 'index']);

        Route::group(['prefix' => '/{_user}', 'middleware' => ['checkIsNull:_user']], function() {
            Route::get('/', [UserController::class, 'show']);
            Route::get('/privileges', [UserController::class, 'showPrivileges']);
            Route::post('/privileges', [UserController::class, 'setPrivileges']);
            Route::delete('/', [UserController::class, 'destroy']);
        });
    });

    //### Products
    Route::prefix('/products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->middleware('checkUserPrivilege:SHOW_PRODUCTS');
        Route::post('/', [ProductController::class, 'store'])->middleware('checkUserPrivilege:ADD_PRODUCTS');

        Route::group(['prefix' => '/{_product}', 'middleware' => ['checkIsNull:_product']], function() {
            Route::get('/', [ProductController::class, 'show'])->middleware('checkUserPrivilege:SHOW_PRODUCTS');
            Route::patch('/', [ProductController::class, 'update'])->middleware('checkUserPrivilege:UPDATE_PRODUCTS');
            Route::delete('/', [ProductController::class, 'destroy'])->middleware('checkUserPrivilege:DELETE_PRODUCTS');
        });
    });

    //### Materials
    Route::prefix('/materials')->group(function () {
        Route::get('/', [MaterialController::class, 'index'])->middleware('checkUserPrivilege:SHOW_MATERIALS');
        Route::post('/', [MaterialController::class, 'store'])->middleware('checkUserPrivilege:ADD_MATERIALS');

        Route::group(['prefix' => '/{_material}', 'middleware' => ['checkIsNull:_material']], function() {
            Route::get('/', [MaterialController::class, 'show'])->middleware('checkUserPrivilege:SHOW_MATERIALS');
            Route::patch('/', [MaterialController::class, 'update'])->middleware('checkUserPrivilege:UPDATE_MATERIALS');
            Route::delete('/', [MaterialController::class, 'destroy'])->middleware('checkUserPrivilege:DELETE_MATERIALS');
        });
    });

    //### Suppliers
    Route::prefix('/suppliers')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->middleware('checkUserPrivilege:SHOW_SUPPLIERS');
        Route::post('/', [SupplierController::class, 'store'])->middleware('checkUserPrivilege:ADD_SUPPLIERS');

        Route::group(['prefix' => '/{_supplier}', 'middleware' => ['checkIsNull:_supplier']], function() {
            Route::get('/', [SupplierController::class, 'show'])->middleware('checkUserPrivilege:SHOW_SUPPLIERS');
            Route::patch('/', [SupplierController::class, 'update'])->middleware('checkUserPrivilege:UPDATE_SUPPLIERS');
            Route::delete('/', [SupplierController::class, 'destroy'])->middleware('checkUserPrivilege:DELETE_SUPPLIERS');
        });
    });

    //### Stock
    Route::prefix('/stock')->group(function () {
        Route::prefix('/records')->group(function () {
            Route::get('/', [StockRecordController::class, 'index'])->middleware('checkUserPrivilege:SHOW_STOCKS')->middleware('checkFilter:stock_records');
            Route::post('/', [StockRecordController::class, 'store'])->middleware('checkUserPrivilege:ADD_STOCKS');
        });
        Route::prefix('/manualuses')->group(function () {
            Route::get('/', [StockUseRecordController::class, 'index'])->middleware('checkUserPrivilege:SHOW_STOCKS')->middleware('checkFilter:stock_use_records');
            Route::post('/', [StockUseRecordController::class, 'store'])->middleware('checkUserPrivilege:USE_STOCKS_MANUALLY');
        });
    });

    //### Customers
    Route::prefix('/customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::post('/', [CustomerController::class, 'store']);

        Route::group(['prefix' => '/{_customer}', 'middleware' => ['checkIsNull:_customer']], function() {
            Route::get('/', [CustomerController::class, 'show']);
            Route::patch('/', [CustomerController::class, 'update']);
            Route::delete('/', [CustomerController::class, 'destroy']);
        });
    });

    //### Sales
    Route::prefix('/sales')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->middleware('checkUserPrivilege:SHOW_SALES')->middleware('checkFilter:sales');
        Route::post('/', [SaleController::class, 'store'])->middleware('checkUserPrivilege:MAKE_SALE');

        Route::group(['prefix' => '/{_sale}', 'middleware' => ['checkIsNull:_sale']], function() {
            Route::get('/', [SaleController::class, 'show'])->middleware('checkUserPrivilege:SHOW_SALES');
            Route::patch('/', [SaleController::class, 'update'])->middleware('checkUserPrivilege:MAKE_SALE');
            Route::delete('/', [SaleController::class, 'destroy'])->middleware('checkUserPrivilege:DELETE_SALE');
        });
    });

    //### Quotations
    Route::prefix('/quotations')->group(function () {
        Route::get('/', [QuotationController::class, 'index'])->middleware('checkUserPrivilege:SHOW_QUOTATIONS')->middleware('checkFilter:quotations');
        Route::post('/', [QuotationController::class, 'store'])->middleware('checkUserPrivilege:MAKE_QUOTATION');

        Route::group(['prefix' => '/{_quotation}', 'middleware' => ['checkIsNull:_quotation']], function() {
            Route::get('/', [QuotationController::class, 'show'])->middleware('checkUserPrivilege:SHOW_QUOTATIONS');
            Route::patch('/', [QuotationController::class, 'update'])->middleware('checkUserPrivilege:MAKE_QUOTATION');
            Route::delete('/', [QuotationController::class, 'destroy'])->middleware('checkUserPrivilege:DELETE_QUOTATION');
        });
    });

    //### PDF
    Route::prefix('/pdf')->group(function () {
        Route::get('/invoice/{id}', [PdfController::class, 'showInvoice'])->middleware('checkUserPrivilege:VIEW_PDFS');
    });
});

/**** Fallback (for invalid routes) ****/
Route::fallback(function() {
    return response()->error('Route not found!', 404);
});