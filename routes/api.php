<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSerialController;
use App\Http\Controllers\ProductTagController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\SubMenuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\SupportTypeController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SupportAccountController;
use Illuminate\Support\Facades\Route;

// Protected Routes
Route::group(['middleware' => ['auth:api']], function () {

    Route::post('import-product-serials', [ProductSerialController::class, 'importProductSerials']);
    Route::get('inventories', [InventoryController::class, 'inventory']);
    Route::get('unsold-product-serials/{id}', [ProductSerialController::class, 'unsoldProductSerials']);
    Route::get('related-products', [ProductController::class, 'relatedProducts']);
    Route::get('stock-products/{id}', [StockEntryController::class, 'stockProducts']);
    Route::get('support-list', [SupportController::class, 'supportList']);
    Route::post('task-accept/{id}', [SupportController::class, 'taskAccept']);
    Route::post('task-complete/{id}', [SupportController::class, 'taskComplete']);
    Route::get('product-by-sale/{id}', [SaleController::class, 'productBySale']);
    Route::get('inventory-check/{id}', [InventoryController::class, 'inventoryCheck']);
    Route::get('user-list', [UserController::class, 'userList']);

    Route::post('approve-expense/{id}', [ExpenseController::class, 'approve']);

    Route::post('support-payment/{id}', [SupportController::class, 'supportPayment']);
    Route::post('support-complete/{id}', [SupportAccountController::class, 'paymentComplete']);
 
    Route::apiResource('accounts', AccountController::class);
    Route::apiResource('stocks', StockEntryController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('product-tags', ProductTagController::class); 
    Route::apiResource('vendors', VendorController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('product-serials', ProductSerialController::class);
    Route::apiResource('sales', SaleController::class);
    Route::apiResource('support-types', SupportTypeController::class);
    Route::apiResource('supports', SupportController::class);
    Route::apiResource('expenses', ExpenseController::class);
    Route::apiResource('support-accounts', SupportAccountController::class);

    Route::group(['middleware' => ['role:system-admin,super-admin,admin']], function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('menus', MenuController::class);
        Route::apiResource('sub-menus', SubMenuController::class);

    });

    Route::group(['middleware' => ['role:user']], function () {
        Route::get('support-list', [SupportController::class, 'supportList']);
        Route::post('task-accept/{id}', [SupportController::class, 'taskAccept']);
        Route::post('task-complete/{id}', [SupportController::class, 'taskComplete']);
    });

    Route::post('file-upload', [CommonController::class, 'fileUpload']);

});
