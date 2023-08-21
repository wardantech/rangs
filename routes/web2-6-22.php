<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Inventory\StoreController;
use App\Http\Controllers\Inventory\PartsController;
use App\Http\Controllers\Inventory\RackController;
use App\Http\Controllers\Inventory\BinController;
use App\Http\Controllers\Inventory\ProductSourcingVendorController;
use App\Http\Controllers\Inventory\ServiceSourcingVendorController;
use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Inventory\BrandController;
use App\Http\Controllers\Inventory\FaultController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Inventory\ProductCategoryController;
use App\Http\Controllers\Inventory\RegionController;
use App\Http\Controllers\Inventory\DirectPartsSellController;
use App\Http\Controllers\Inventory\PartSellController;
use App\Http\Controllers\Inventory\ModelController;
use App\Http\Controllers\Inventory\PriceManagementController;
use App\Http\Controllers\Inventory\CentralPartsReturnController;
use App\Http\Controllers\Inventory\PartsReturnController;
use App\Http\Controllers\Ticket\WarrantyTypeController;
use App\Http\Controllers\Ticket\ServiceTypeController;
use App\Http\Controllers\Ticket\PurchaseHistoryController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Ticket\JobPriorityController;
use App\Http\Controllers\Ticket\ProductConditionController;
use App\Http\Controllers\Ticket\AccessoriesController;
use App\Http\Controllers\Ticket\ReceiveModeController;
use App\Http\Controllers\Ticket\DeliveryModeController;
use App\Http\Controllers\Outlet\OutletController;
use App\Http\Controllers\Customer\CustomerGradeController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Account\ExpenseController;
use App\Http\Controllers\Account\RevenueController;
use App\Http\Controllers\Account\BankAccountController;
use App\Http\Controllers\Account\DepositController;
use App\Http\Controllers\Employee\DesignationController;
use App\Http\Controllers\Requisition\RequisitionController;
use App\Http\Controllers\Requisition\PendingRequisitonController;
use App\Http\Controllers\Requisition\TechnicianRequisitionController;
use App\Http\Controllers\ProductPurchase\ProductPurchaseController;
use App\Http\Controllers\Job\JobController;
use App\Http\Controllers\Job\JobSubmissionController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\PO\PurchaseOrderController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () { return view('auth.login'); });


Route::get('login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('login', [LoginController::class,'login']);
Route::post('register', [RegisterController::class,'register']);

Route::get('password/forget',  function () {
	return view('pages.forgot-password');
})->name('password.forget');
Route::post('password/email', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class,'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class,'reset'])->name('password.update');


Route::group(['middleware' => 'auth'], function(){
	// logout route
	Route::get('/logout', [LoginController::class,'logout']);
	Route::get('/clear-cache', [HomeController::class,'clearCache']);
	// dashboard route
	Route::get('/dashboard', function () {
		return view('pages.dashboard');
	})->name('dashboard');

    // get permissions
	Route::get('get-role-permissions-badge', [PermissionController::class,'getPermissionBadgeByRole']);
	// permission examples
    Route::get('/permission-example', function () {
    	return view('permission-example');
    });
    // API Documentation
    Route::get('/rest-api', function () { return view('api'); });
    // Editable Datatable
	Route::get('/table-datatable-edit', function () {
		return view('pages.datatable-editable');
	});

    Route::group(['namespace' => 'Inventory'], function () {
        //only those have manage_user permission will get access
        Route::group(['middleware' => 'can:manage_user'], function(){
        Route::get('/users', [UserController::class,'index']);
        Route::get('/user/get-list', [UserController::class,'getUserList']);
        Route::get('/user/create', [UserController::class,'create']);
        Route::post('/user/create', [UserController::class,'store'])->name('create-user');
        Route::get('/user/{id}', [UserController::class,'edit']);
        Route::post('/user/update', [UserController::class,'update']);
        Route::get('/user/delete/{id}', [UserController::class,'delete']);
        });

        //only those have manage_role permission will get access
        Route::group(['middleware' => 'can:manage_role|manage_user'], function(){
            Route::get('/roles', [RolesController::class,'index']);
            Route::get('/role/get-list', [RolesController::class,'getRoleList']);
            Route::post('/role/create', [RolesController::class,'create']);
            Route::get('/role/edit/{id}', [RolesController::class,'edit']);
            Route::post('/role/update', [RolesController::class,'update']);
            Route::get('/role/delete/{id}', [RolesController::class,'delete']);
        });

        //only those have manage_permission permission will get access
        Route::group(['middleware' => 'can:manage_permission|manage_user'], function(){
            Route::get('/permission', [PermissionController::class,'index']);
            Route::get('/permission/get-list', [PermissionController::class,'getPermissionList']);
            Route::post('/permission/create', [PermissionController::class,'create']);
            Route::get('/permission/update', [PermissionController::class,'update']);
            Route::get('/permission/delete/{id}', [PermissionController::class,'delete']);
        });

        // Ticket Management
        Route::get('/customer-info', function () { return view('ticket-management.customer_info'); });
        Route::get('/create-ticket', function () { return view('ticket-management.create'); });
        Route::get('/ticket-closing', function () { return view('ticket-management.ticket-closing'); });

        // Inventory
        Route::get('inventory', [InventoryController::class,'index']);
        Route::get('inventory/create',[InventoryController::class,'create']);
        Route::post('inventory/store',[InventoryController::class,'store'])->name('create-inventory');
        Route::get('inventory/edit', [InventoryController::class,'edit'])->name('edit-inventory')->middleware('signed');
        Route::patch('inventory/{id}', [InventoryController::class,'update']);
        Route::delete('inventory/{id}', [InventoryController::class,'destroy']);
        Route::get('inventory/show/{id}', [InventoryController::class,'show'])->name('show-inventory');
        Route::get('/stock-list', function () { return view('inventory.stock-list'); });
        Route::get('get/price/{part_id}/{model_id}', [PriceManagementController::class, 'getPrice'])->name('get-price');
        Route::get('inventory/parts-receive/rows', [InventoryController::class, 'getPartReceiveRow'])->name('part-receive-row');

    });

    // Stock Report
    Route::group(['prefix' => 'inventory', 'as' => 'inventory.', 'middleware' => 'can:manage_permission|manage_user'], function () {
        Route::resource('parts_model', 'Inventory\ModelController');
        Route::get('stock', [InventoryController::class,'stock']);
        Route::get('stock-in-hand', [InventoryController::class,'stockInHandGet']);
        Route::post('stock-in-hand', [InventoryController::class,'stockInHandPost'])->name('stock-in-hand');
        Route::post('stock-in-hand-all', [InventoryController::class,'stockInHandPostAll'])->name('stock-in-hand-all');
        Route::get('stock-in-hand_by_part_model', [InventoryController::class,'stockInHandByPartModel']);
        Route::get('stock/outlet', [InventoryController::class,'stockOutlet'])->name('stock.outlet');
        Route::get('stock/outlet-inventory-details/{id}', [InventoryController::class,'outletInventoryDetails'])->name('stock.outlet.details');
        Route::get('technician/stock', [InventoryController::class,'stockForTechnician'])->name('technician.stock');
        Route::get('technician/stock_details/{id}', [InventoryController::class,'stockForTechnicianDetails'])->name('technician.stock_details');
        Route::get('stock_details', [InventoryController::class,'stockDetails']);
        Route::get('show-inventory-details/{id}', [InventoryController::class, 'inventoryDetails'])->name('show-inventory-details');
        Route::get('model', [ModelController::class,'getModel']);
        Route::get('parts/model', [ModelController::class,'getPartsModel']);

        Route::get('parts/stock', [ModelController::class,'getPartsStock']);
        Route::get('outlet/parts/stock', [ModelController::class,'getPartsStockForOutlet']);
        Route::get('parts/outlet/stock', [ModelController::class,'getPartsStock']);
        Route::get('parts/technician/stock', [TechnicianRequisitionController::class,'getPartsStock']);
        Route::get('parts/technician/stock-for-job', [TechnicianRequisitionController::class,'getPartsStockForJob']);

        Route::get('getStockData', [ModelController::class,'getStockDetails']);
        Route::get('getStockInfo', [ModelController::class,'getStocInfo']);
        //store
        Route::get('/store', [StoreController::class, 'index'])->name('store-index');
        Route::get('store/create', [StoreController::class, 'create'])->name('store-create');
        Route::post('/newstore', [StoreController::class, 'newStore'])->name('add-new-store');
        //Route::get('/show/stores', [StoreController::class, 'showStores'])->name('show-stores');
        Route::get('store/{id}', [StoreController::class, 'edit']);
        Route::post('/update/store', [StoreController::class, 'updateStore'])->name('update-store-details');
        Route::get('store/delete/{id}', [StoreController::class, 'deleteStore'])->name('delete-store');
        //parts
        Route::get('parts', [PartsController::class, 'index'])->name('parts-index');
        Route::get('/parts/create', [PartsController::class, 'create'])->name('parts-create');
        Route::post('/newparts', [PartsController::class, 'newParts'])->name('add-new-parts');
        Route::get('parts/edit/{id}', [PartsController::class, 'edit']);
        Route::post('/update/parts', [PartsController::class, 'updateParts'])->name('update-parts-details');
        Route::post('parts/category', [PartsController::class, 'partsCategory'])->name('parts.category');
        Route::delete('parts/destroy/{id}', [PartsController::class, 'deleteParts'])->name('delete-parts');
        Route::get('parts/change-status/{id}', [PartsController::class, 'changePartsStatus'])->name('parts.status.change');
        //Rack
        Route::get('/rack', [RackController::class, 'index'])->name('rack-index');
        Route::get('rack/create', [RackController::class, 'create'])->name('rack-create');
        Route::post('/newrack', [RackController::class, 'newRack'])->name('add-new-rack');
        Route::get('rack/{id}', [RackController::class, 'edit']);
        Route::post('/update/rack', [RackController::class, 'updateRack'])->name('update-rack-details');
        Route::get('rack/delete/{id}', [RackController::class, 'deleteRack'])->name('delete-rack');
        //Bin
        Route::get('bin', [BinController::class, 'index'])->name('bin-index');
        Route::get('bin/create', [BinController::class, 'create'])->name('bin-create');
        Route::post('/newbin', [BinController::class, 'newBin'])->name('add-new-bin');
        Route::get('bin/{id}', [BinController::class, 'edit']);
        Route::post('/update/bin', [BinController::class, 'updateBin'])->name('update-bin-details');
        Route::get('bin/delete/{id}', [BinController::class, 'deleteBin'])->name('delete-bin');
        Route::get('get/rack/{id}', [BinController::class, 'getRack']);
        Route::get('get/bin/{id}', [BinController::class, 'getBin']);
        //product category
        Route::get('/product-category', [ProductCategoryController::class, 'index'])->name('product-category-index');
        Route::post('/product-category/store', [ProductCategoryController::class, 'store'])->name('create.product-category');
        Route::get('/product-category/edit/{id}', [ProductCategoryController::class, 'edit'])->name('edit.product-category');
        Route::post('/product-category/update/{id}', [ProductCategoryController::class, 'update'])->name('update.product-category');
        Route::delete('/product-category/delete/{id}', [ProductCategoryController::class, 'destroy'])->name('destroy.product-category');
        //fault
        Route::get('/fault', [FaultController::class, 'index'])->name('fault-index');
        Route::post('/fault/store', [FaultController::class, 'store'])->name('fault-store');
        Route::post('/fault/update/{id}', [FaultController::class, 'update'])->name('fault.update');
        Route::get('/fault/delete/{id}', [FaultController::class, 'destroy'])->name('fault.delete');

        //Return Parts Management
        // Route::get('parts-return', [PartsReturnController::class,'index']);
        Route::get('return-parts-stock', [PartsReturnController::class,'getPartsStock']);
        Route::get('/return-parts/central/receive/{id}', [PartsReturnController::class, 'returnPartsCentralReceive'])->name('central.returnReceive');
        // Route::get('return-parts/store', [PartsReturnController::class,'outletRequisitionStore']);
        Route::get('return/parts', [CentralPartsReturnController::class, 'index']);
        Route::get('return/parts/show/{id}', [CentralPartsReturnController::class, 'show']);
        Route::get('return/parts/receive/{id}', [CentralPartsReturnController::class, 'receive']);
        Route::get('return/parts/store', [CentralPartsReturnController::class, 'store']);

        Route::resource('price-management', 'Inventory\PriceManagementController');
        Route::get('parts/get-price', [PriceManagementController::class, 'getPrice'])->name('get-part-price');
        Route::resource('parts-return', 'Inventory\PartsReturnController');
    });

    //Account Management
    Route::group(['namespace' => 'Account'], function () {
        //expense
        Route::get('/expense', [ExpenseController::class, 'index'])->name('expense-index');
        Route::post('/expense/store', [ExpenseController::class, 'store'])->name('create.expense');
        Route::get('/expense/edit/{id}', [ExpenseController::class, 'edit'])->name('edit.expense');
        Route::post('/expense/update/{id}', [ExpenseController::class, 'update'])->name('update.expense');
        Route::delete('/expense/delete/{id}', [ExpenseController::class, 'destroy'])->name('destroy.expense');
        //Revenue
        Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue-index');
        Route::post('/revenue/store', [RevenueController::class, 'store'])->name('create.revenue');
        Route::get('/revenue/edit/{id}', [RevenueController::class, 'edit'])->name('edit.revenue');
        Route::post('/revenue/update/{id}', [RevenueController::class, 'update'])->name('update.revenue');
        Route::delete('/revenue/delete/{id}', [RevenueController::class, 'destroy'])->name('destroy.revenue');
        //Bank Account
        Route::get('/bank-account', [BankAccountController::class, 'index'])->name('bank-account-index');
        Route::post('/bank-account/store', [BankAccountController::class, 'store'])->name('create.bank-account');
        Route::get('/bank-account/edit/{id}', [BankAccountController::class, 'edit'])->name('edit.bank-account');
        Route::post('/bank-account/update/{id}', [BankAccountController::class, 'update'])->name('update.bank-account');
        Route::delete('/bank-account/delete/{id}', [BankAccountController::class, 'destroy'])->name('destroy.bank-account');
        Route::get('/deposit', [DepositController::class, 'index'])->name('deposit-index');
        Route::post('/deposit/store', [DepositController::class, 'store'])->name('deposit.store');
        Route::get('/deposit/edit/{id}', [DepositController::class, 'edit'])->name('edit.deposit');
        Route::post('/deposit/update', [DepositController::class, 'update'])->name('update.deposit');
        Route::delete('/deposit/delete/{id}', [DepositController::class, 'destroy'])->name('destroy.deposit');
    });

    //Requisition
    Route::group(['namespace' => 'Requisition'], function () {
        Route::post('/requisitions/allocate', [RequisitionController::class, 'requisitionAllocate'])->name('requisition.allocate');
        Route::get('/outlet/requisitions', [RequisitionController::class, 'outletRequisitionList'])->name('outlet.requisitionList');
        Route::get('/outlet/requisitions/create', [RequisitionController::class, 'outletRequisitionCreate'])->name('outlet.requisitionCreate');
        Route::post('/outlet/requisitions/store', [RequisitionController::class, 'outletRequisitionStore'])->name('outlet.requisitionStore');
        Route::delete('/outlet/requisitions/destroy/{id}', [RequisitionController::class, 'outletRequisitionDestroy'])->name('outlet.requisitionDestroy');
        Route::get('/central/requisitions', [RequisitionController::class, 'centralRequisitionList'])->name('central.requisitionList');
        Route::get('/requisition/allocate/{id}', [RequisitionController::class, 'requisitationAllocate'])->name('requisitation.allocate');
        Route::get('/requisition/decline/{id}', [RequisitionController::class, 'requisitationDecline'])->name('requisitation.decline');
        Route::post('/requisition/allocate/store', [RequisitionController::class, 'requisitationAllocateStore'])->name('requisitation.allocateStore');
        Route::get('/requisition/details', [RequisitionController::class, 'requisitationDetaails'])->name('requisition.details');
        Route::get('/form/requisition/outlet/receive/{id}', [RequisitionController::class, 'requisitionOutletReceiveForm'])->name('outlet.requisitionReceive-form');
        Route::post('/requisition/outlet/receive/store', [RequisitionController::class, 'requisitionOutletReceiveStore'])->name('outlet.requisitionReceiveStore');
        Route::get('/requisition/outlet/receive/{id}', [RequisitionController::class, 'requisitionOutletReceive'])->name('outlet.requisitionReceive');
        Route::resource('requisitions', 'RequisitionController');
        Route::resource('pending-requisitions', 'PendingRequisitonController');
        //Technician Requisition Route
        Route::group(['prefix' => 'technician', 'as' => 'technician.', 'middleware' => 'can:manage_permission|manage_user'], function () {
            Route::resource('requisition', 'TechnicianRequisitionController');
            Route::get('requisitions', [TechnicianRequisitionController::class,'indexForStores']);
            Route::get('requisition-by-job/{id}', [TechnicianRequisitionController::class,'requisitionCreateByJob']);
            Route::get('requisition/allocate/{id}', [TechnicianRequisitionController::class, 'requisitationAllocate']);
            Route::post('requisition/allocate/store', [TechnicianRequisitionController::class, 'requisitationAllocateStore']);
            Route::get('/form/requisition/receive/{id}', [TechnicianRequisitionController::class, 'requisitionTechnicianReceiveform'])->name('requisitionReceive-form');
            Route::post('/requisition/receive/store', [TechnicianRequisitionController::class, 'requisitionTechnicianReceiveStore'])->name('requisitionReceiveStore');
            Route::get('/requisition/receive/{id}', [TechnicianRequisitionController::class, 'requisitionTechnicianReceive'])->name('requisitionReceive');

        });

    });

    //Employee/Technician Route
    Route::group(['prefix' => 'hrm', 'as' => 'hrm.', 'middleware' => 'can:manage_permission|manage_user'], function () {
        // Route::resource('technician', 'Employee\EmployeeController');
        Route::get('technician', [EmployeeController::class,'index'])->name('technician');
        Route::get('technician/create', [EmployeeController::class,'create'])->name('technician.create');
        Route::post('technician/store', [EmployeeController::class,'store'])->name('technician.store');
        Route::get('technician/edit/{id}', [EmployeeController::class,'edit'])->name('technician.edit');
        Route::put('technician/update/{id}', [EmployeeController::class,'update'])->name('technician.update');
        Route::get('technician/show/{id}', [EmployeeController::class,'show'])->name('technician.show');
        Route::delete('technician/destroy/{id}', [EmployeeController::class,'destroy'])->name('technician.destroy');
        Route::post('technician/filter', [EmployeeController::class,'filterEmployee'])->name('technician.filter');
        Route::get('get/store/{id}', [EmployeeController::class,'getStore'])->name('get.outlet-store');
        //Designation Resource

        Route::resource('designation', 'Employee\DesignationController');
        Route::resource('attendance', 'Employee\AttendanceController');
        Route::resource('teamleader', 'Employee\TeamLeaderController');

    });
    Route::group(['namespace' => 'Ticket'], function () {
        Route::prefix('tickets')->group(function(){
            Route::get('warranty-types', [WarrantyTypeController::class, 'index'])->name('warranty-types');
            Route::post('warranty-types/store', [WarrantyTypeController::class, 'store'])->name('create.warranty');
            Route::get('warranty-types/{id}', [WarrantyTypeController::class, 'edit'])->name('edit.warranty');
            Route::post('warranty-types/update', [WarrantyTypeController::class, 'update'])->name('update.warranty');
            Route::delete('warranty_delete/{id}', [WarrantyTypeController::class, 'destroy'])->name('destroy.warranty');
        });

        Route::prefix('tickets')->group(function(){
            Route::get('service-types', [ServiceTypeController::class, 'index'])->name('service-types');
            Route::post('service-types/store', [ServiceTypeController::class, 'store'])->name('create.service');
            Route::get('service-types/{id}', [ServiceTypeController::class, 'edit'])->name('edit.service');
            Route::post('service-types/update', [ServiceTypeController::class, 'update'])->name('update.service');
            Route::delete('service/{id}', [ServiceTypeController::class, 'destroy'])->name('destroy.service');
        });

        Route::prefix('tickets')->group(function(){
            Route::get('job-priority', [JobPriorityController::class, 'index'])->name('jobs');
            Route::post('job-priority/store', [JobPriorityController::class, 'store'])->name('create.jobs');
            Route::get('job-priority/{id}', [JobPriorityController::class, 'edit'])->name('edit.jobs');
            Route::post('job-priority/update', [JobPriorityController::class, 'update'])->name('update.jobs');
            Route::delete('job/{id}', [JobPriorityController::class, 'destroy'])->name('destroy.jobs');
        });

        Route::prefix('tickets')->group(function(){
            Route::get('product_conditions', [ProductConditionController::class, 'index'])->name('product_conditions');
            Route::post('product_conditions/store', [ProductConditionController::class, 'store'])->name('create.product_conditions');
            Route::get('product_conditions/{id}', [ProductConditionController::class, 'edit'])->name('edit.product_conditions');
            Route::post('product_conditions/update', [ProductConditionController::class, 'update'])->name('update.product_conditions');
            Route::delete('product_conditions/{id}', [ProductConditionController::class, 'destroy'])->name('destroy.product_conditions');
        });

        Route::prefix('tickets')->group(function(){
            Route::get('accessories', [AccessoriesController::class, 'index'])->name('accessories');
            Route::post('accessories/store', [AccessoriesController::class, 'store'])->name('create.accessories');
            Route::get('accessories/{id}', [AccessoriesController::class, 'edit'])->name('edit.accessories');
            Route::post('accessories/update', [AccessoriesController::class, 'update'])->name('update.accessories');
            Route::delete('accessories/{id}', [AccessoriesController::class, 'destroy'])->name('destroy.accessories');
        });

        Route::prefix('tickets')->group(function(){
            Route::resource('receive-mode', 'ReceiveModeController');
            Route::resource('delivery-mode', 'DeliveryModeController');
            Route::get('purchase-history', [PurchaseHistoryController::class, 'index']);
            Route::post('show-api', [PurchaseHistoryController::class, 'callapi']);
            Route::get('ticket-index', [PurchaseHistoryController::class, 'ticketIndex'])->name('ticket-index');
            Route::get('ticket-create/{id}', [PurchaseHistoryController::class, 'ticketcreate']);
            Route::post('ticket-store', [PurchaseHistoryController::class, 'storeTicket'])->name('store-ticket');
            Route::get('ticket/edit/{id}', [PurchaseHistoryController::class, 'editTicket'])->name('edit-ticket-details');
            Route::post('ticket/update/{id}', [PurchaseHistoryController::class, 'updateTicket'])->name('update-ticket');
            Route::delete('ticket/delete/{id}', [PurchaseHistoryController::class, 'distroyTicket'])->name('ticket-destroy');
            Route::get('/get/service/amount/{id}', [PurchaseHistoryController::class, 'serviceAmount']);
            Route::get('/ticket/show/{id}', [PurchaseHistoryController::class, 'showTicket'])->name('show-ticket-details');
        });
        Route::prefix('tickets')->group(function(){
            Route::get('purchase-info', [ProductPurchaseController::class, 'purchaseInfo']);
            Route::get('purchaseinfo', [ProductPurchaseController::class, 'purchaseinfo_mobile']);
        });

    });

    // Product Purchase
    Route::group(['prefix' => 'product', 'as' => 'product.', 'middleware' => 'can:manage_permission|manage_user'], function () {
        Route::get('brand', [ProductPurchaseController::class, 'brand'])->name('brand');
        Route::get('model', [ProductPurchaseController::class, 'model'])->name('model');
        Route::resource('purchase', 'ProductPurchase\ProductPurchaseController');
        Route::resource('brand_model', 'Product\BrandModelController');
         //category
         Route::get('/category', [CategoryController::class, 'index'])->name('category-index');
         Route::post('/category/store', [CategoryController::class, 'store'])->name('category-store');
         Route::post('/category/update/{id}', [CategoryController::class, 'update'])->name('category.update');
         Route::get('/category/delete/{id}', [CategoryController::class, 'destroy'])->name('category.delete');
         //Brand
         Route::get('/', [BrandController::class, 'index'])->name('brand-index');
         Route::post('/store', [BrandController::class, 'store'])->name('brand-store');
         Route::get('/edit/{id}', [BrandController::class, 'edit'])->name('brand.edit');
         Route::patch('/update/{id}', [BrandController::class, 'update'])->name('brand.update');
         Route::get('/delete/{id}', [BrandController::class, 'destroy'])->name('brand.delete');
         Route::get('/brand/status/change/{id}', [BrandController::class, 'changeStatus'])->name('brand.status.change');
         //Customer Grade
         Route::resource('customer-grade', 'Customer\CustomerGradeController');
         //customer
         Route::get('/customer', [CustomerController::class, 'index'])->name('customer-index');
         Route::post('/customer/store', [CustomerController::class, 'store'])->name('create.customer');
         Route::get('/customer/edit/{id}', [CustomerController::class, 'edit'])->name('edit.customer');
         Route::post('/customer/update/{id}', [CustomerController::class, 'update'])->name('update.customer');
         Route::delete('/customer/delete/{id}', [CustomerController::class, 'destroy'])->name('destroy.customer');
         //Direct Parts Sell
         Route::get('/direct-parts-sell', [DirectPartsSellController::class, 'index'])->name('direct-parts-sell-index');
         Route::get('/direct-parts-sell/create', [DirectPartsSellController::class, 'create'])->name('create.direct-parts-sell');
         Route::post('/direct-parts-sell/store', [DirectPartsSellController::class, 'store'])->name('store.direct-parts-sell');
         Route::get('/direct-parts-sell/edit/{id}', [DirectPartsSellController::class, 'edit'])->name('edit.direct-parts-sell');
         Route::post('/direct-parts-sell/update/{id}', [DirectPartsSellController::class, 'update'])->name('update.direct-parts-sell');
         Route::delete('/direct-parts-sell/delete/{id}', [DirectPartsSellController::class, 'destroy'])->name('destroy.direct-parts-sell');
         //parts sell
         Route::get('/part-sell/stock', [PartSellController::class, 'partsStockDetails']);
         Route::resource('part-sell', 'Inventory\PartSellController');
    });

    // General
    Route::group(['prefix' => 'general', 'as' => 'general.', 'middleware' => 'can:manage_permission|manage_user'], function () {
        //Region
        Route::get('/region', [RegionController::class, 'index'])->name('region-index');
        Route::post('/region/store', [RegionController::class, 'store'])->name('create.region');
        Route::get('/region/edit/{id}', [RegionController::class, 'edit'])->name('edit.region');
        Route::post('/region/update/{id}', [RegionController::class, 'update'])->name('update.region');
        Route::delete('/region/delete/{id}', [RegionController::class, 'destroy'])->name('destroy.region');
        Route::get('get/district/{id}', [RegionController::class, 'getDistrict']);
        //outlet
        Route::get('/outlet', [OutletController::class, 'index'])->name('outlet-index');
        Route::post('/outlet/store', [OutletController::class, 'store'])->name('create.outlet');
        Route::get('/outlet/show/{id}', [OutletController::class, 'show'])->name('show.outlet');
        Route::get('/outlet/edit/{id}', [OutletController::class, 'edit'])->name('edit.outlet');
        Route::post('/outlet/update/{id}', [OutletController::class, 'update'])->name('update.outlet');
        Route::delete('/outlet/delete/{id}', [OutletController::class, 'destroy'])->name('destroy.outlet');
        Route::get('/product-sourcing-vendor', [ProductSourcingVendorController::class, 'index'])->name('product-sourcing-vendor-index');
        Route::get('/product-sourcing-vendor/create', [ProductSourcingVendorController::class, 'create'])->name('product-sourcing-vendor-create');
        Route::post('/new-product-sourcing-vendor', [ProductSourcingVendorController::class, 'store'])->name('add-new-product-sourcing-vendor');
        Route::get('product-sourcing-vendor/delete/{id}', [ProductSourcingVendorController::class, 'deleteVendor'])->name('delete-product-sourcing-vendor');
        Route::get('product-sourcing-vendor/{id}', [ProductSourcingVendorController::class, 'edit'])->name('delete-product-sourcing-vendor');
        Route::post('/update/product-sourcing-vendor', [ProductSourcingVendorController::class, 'updateVendor'])->name('update-product-sourcing-vendor-details');
        //Service sourcing vendor
        Route::get('/service-sourcing-vendor', [ServiceSourcingVendorController::class, 'index'])->name('service-sourcing-vendor-index');
        Route::get('/service-sourcing-vendor/create', [ServiceSourcingVendorController::class, 'create'])->name('sourcing-vendor-create');
        Route::post('/new-service-sourcing-vendor', [ServiceSourcingVendorController::class, 'store'])->name('add-new-service-sourcing-vendor');
        Route::get('service-sourcing-vendor/delete/{id}', [ServiceSourcingVendorController::class, 'deleteVendor'])->name('delete-service-sourcing-vendor');
        Route::get('service-sourcing-vendor/{id}', [ServiceSourcingVendorController::class, 'edit'])->name('delete-service-sourcing-vendor');
        Route::post('/update/service-sourcing-vendor', [ServiceSourcingVendorController::class, 'updateVendor'])->name('update-service-sourcing-vendor-details');
        Route::get('get/thana/{id}', [ServiceSourcingVendorController::class, 'getThana']);
        Route::get('/product-category', [ProductCategoryController::class, 'index'])->name('product-category-index');
        Route::post('/product-category/store', [ProductCategoryController::class, 'store'])->name('create.product-category');
        Route::get('/product-category/edit/{id}', [ProductCategoryController::class, 'edit'])->name('edit.product-category');
        Route::post('/product-category/update/{id}', [ProductCategoryController::class, 'update'])->name('update.product-category');
        Route::delete('/product-category/delete/{id}', [ProductCategoryController::class, 'destroy'])->name('destroy.product-category');
        //Group Management
        // Route::delete('single/delivery/{id}', 'Group\GroupController@singleDestroy')->name('single.delivery.delete');
        Route::resource('group', 'Group\GroupController');

    });

    //Job
    Route::group(['prefix' => 'job', 'as' => 'job.', 'middleware' => 'can:manage_permission|manage_user'], function () {
        // Route::get('job/create/{id}', 'Job\JobController');
        Route::get('job/create/{id}', [JobController::class, 'job_create'])->name('job_create');
        Route::get('employee/job-list', [JobController::class, 'employeeJobList'])->name('employee-jobs');
        Route::get('employee/job-list/details/{id}', [JobController::class, 'employeeJobDetails'])->name('show.employee-job.details');
        Route::get('employee/job/accept/{id}', [JobController::class, 'acceptJob'])->name('accept-job');
        Route::get('employee/job/start/{id}', [JobController::class, 'startJob'])->name('start-job');
        Route::get('employee/job/end/{id}', [JobController::class, 'endJob'])->name('end-job');
        Route::post('deny', [JobController::class, 'denyJob'])->name('deny-job');
        Route::resource('job', 'Job\JobController');
        // Route::resource('job-submit', 'Job\JobSubmissionController');
        Route::get('submission/create/{id}', [JobSubmissionController::class, 'createJobSubmission'])->name('job-submission-create');
        Route::post('submission/store', [JobSubmissionController::class, 'storeJobSubmission'])->name('job-submission-store');
        Route::get('submitted-jobs', [JobSubmissionController::class, 'index'])->name('submitted-jobs-index');
        Route::get('submitted-jobs/show/{id}', [JobSubmissionController::class, 'show'])->name('submitted-job-show');
    });
    Route::group(['prefix' => 'purchase', 'as' => 'purchase.'], function() {
        Route::get('/outlet/parts/stock', [PurchaseOrderController::class, 'getPartsStockForOutlet']);
        Route::get('/requisitions/details', [PurchaseOrderController::class, 'purchaseRequisitationDetails'])->name('requisitions.details');
            Route::resource('/requisitions', 'PO\PurchaseOrderController');
        });

});


// Route::get('/register', function () { return view('pages.register'); });
// Route::get('/login-1', function () { return view('pages.login'); });
