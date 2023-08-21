<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\Job\JobController;
use App\Http\Controllers\Loan\LoanController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Inventory\BinController;
use App\Http\Controllers\Outlet\OutletController;
use App\Http\Controllers\Account\CashinController;
use App\Http\Controllers\Inventory\RackController;
use App\Http\Controllers\Account\DepositController;
use App\Http\Controllers\Account\ExpenseController;
use App\Http\Controllers\Account\RevenueController;
use App\Http\Controllers\Inventory\BrandController;
use App\Http\Controllers\Inventory\FaultController;
use App\Http\Controllers\Inventory\ModelController;
use App\Http\Controllers\Inventory\PartsController;
use App\Http\Controllers\Inventory\StoreController;
use App\Http\Controllers\Inventory\RegionController;
use App\Http\Controllers\Inventory\SourceController;
use App\Http\Controllers\PO\PurchaseOrderController;
use App\Http\Controllers\Report\JobReportController;
use App\Http\Controllers\Report\KpiReportController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Job\JobSubmissionController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Inventory\PartSellController;
use App\Http\Controllers\Product\BrandModelController;
use App\Http\Controllers\Ticket\AccessoriesController;
use App\Http\Controllers\Ticket\JobPriorityController;
use App\Http\Controllers\Ticket\ReceiveModeController;
use App\Http\Controllers\Ticket\ServiceTypeController;
use App\Http\Controllers\Account\BankAccountController;
use App\Http\Controllers\Account\ExpenceItemController;
use App\Http\Controllers\Account\TransectionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Employee\AttendanceController;
use App\Http\Controllers\Employee\TeamLeaderController;
use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Ticket\DeliveryModeController;
use App\Http\Controllers\Ticket\WarrantyTypeController;
use App\Http\Controllers\Employee\DesignationController;
use App\Http\Controllers\Account\AccountBranchController;
use App\Http\Controllers\Ticket\TicketTrackingController;
use App\Http\Controllers\Customer\CustomerGradeController;
use App\Http\Controllers\Inventory\PartCategoryController;
// use App\Http\Controllers\Inventory\PartsReturnController;
use App\Http\Controllers\Loan\AcceptLoanRequestController;
use App\Http\Controllers\Report\FinancialReportController;
use App\Http\Controllers\Ticket\PurchaseHistoryController;
use App\Http\Controllers\Account\CashTransectionController;
use App\Http\Controllers\Allocation\ReAllocationController;
use App\Http\Controllers\Consumption\ConsumptionController;
use App\Http\Controllers\Requisition\RequisitionController;
use App\Http\Controllers\Ticket\ProductConditionController;
use App\Http\Controllers\Customer\CustomerFeedbackController;
use App\Http\Controllers\Customer\FeedbackQuestionController;
use App\Http\Controllers\Inventory\DirectPartsSellController;
use App\Http\Controllers\Inventory\PriceManagementController;
use App\Http\Controllers\Inventory\ProductCategoryController;
use App\Http\Controllers\Ticket\PurchaseHistoryApiController;
use App\Http\Controllers\Allocation\BranchAllocationController;
use App\Http\Controllers\Inventory\BranchPartsReturnController;
use App\Http\Controllers\Inventory\RackBinManagementController;
use App\Http\Controllers\Job\CustomerAdvancedPaymentController;
use App\Http\Controllers\Inventory\CentralPartsReturnController;
use App\Http\Controllers\Report\PartConsumptionReportController;
use App\Http\Controllers\Requisition\PendingRequisitonController;
use App\Http\Controllers\Inventory\TecnicianPartsReturnController;
use App\Http\Controllers\Allocation\TechnicianAllocationController;
use App\Http\Controllers\Inventory\ProductSourcingVendorController;
use App\Http\Controllers\Inventory\ServiceSourcingVendorController;
use App\Http\Controllers\ProductPurchase\ProductPurchaseController;
use App\Http\Controllers\Requisition\RequisitionAllocationController;
use App\Http\Controllers\Requisition\TechnicianRequisitionController;
use App\Http\Controllers\Allocation\BranchAllocationReceivedController;
use App\Http\Controllers\Allocation\BranchReceivedAllocationController;
use App\Http\Controllers\Requisition\BranchTechnicianAllocationController;
use App\Http\Controllers\Allocation\TechnicianAllocationReceivedController;


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

Route::resource('customer-advanced-payment', 'Job\CustomerAdvancedPaymentController');
Route::get('advance-payment/{id}', [CustomerAdvancedPaymentController::class,'createPayment'])->name('customer-advanced-payment.create-payment');

Route::get('login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('login', [LoginController::class,'login']);
Route::post('register', [RegisterController::class,'register']);

//Ticket Tracking
Route::get('ticket/tracking', [TicketTrackingController::class,'showSearchForm']);
Route::post('ticket/tracking', [TicketTrackingController::class,'search'])->name('ticket.tracking');

Route::get('password/forget',  function () {
	return view('pages.forgot-password');
})->name('password.forget');
Route::post('password/email', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class,'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class,'reset'])->name('password.update');


// SMS Testing
Route::get('sms', function(){
            $url="http://66.45.237.70/api.php";
            $number="01609181578";
            $message="SMS notification Testing";
            $data=array(
                'username' =>"01322644599",
                'password' =>"4NBHSC3G",
                'number' =>$number,
                'message' =>$message
            );
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            $smsresult=curl_exec($ch);
            $p=explode("|",$smsresult);
            $sendstatus=$p[0];
});
// SMS Testing
Route::get('sslsms', function(){
    $url="https://smsplus.sslwireless.com/api/v3/send-sms";
    $params = [
        "api_token" => "ub0xwa5y-dhzk4vfn-mrafjuto-jnq0lgkz-ehmi4uzr",
        "sid" => "RANGSBRANDAPI",
        "msisdn" => "01609181578",
        "sms" => "Test SMS",
        "csms_id" => "123456789"
    ];
    $params = json_encode($params);
    $ch = curl_init(); // Initialize cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($params),
        'accept:application/json'
    ));

    $response = curl_exec($ch);

    curl_close($ch);

    return $response;
});

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

    Route::group(['prefix' => 'sell', 'as' => 'sell.'], function () {
        Route::get('/parts-sell', [DirectPartsSellController::class, 'index'])->name('direct-parts-sell-index');
        Route::get('/parts-sell/create', [DirectPartsSellController::class, 'create'])->name('create.direct-parts-sell');
        Route::get('/parts-sell/row', [DirectPartsSellController::class, 'getPartSellRow'])->name('row.direct-parts-sell');
        Route::post('/parts-sell/store', [DirectPartsSellController::class, 'store'])->name('store.direct-parts-sell');
        Route::get('/parts-sell/edit/{id}', [DirectPartsSellController::class, 'edit'])->name('edit.direct-parts-sell');
        Route::get('/parts-sell/row-for-edit', [DirectPartsSellController::class, 'getPartSellRowForEdit'])->name('row-for-edit.direct-parts-sell');
        Route::post('/parts-sell/update/{id}', [DirectPartsSellController::class, 'update'])->name('update.direct-parts-sell');
        Route::get('/parts-sell/delete/{id}', [DirectPartsSellController::class, 'destroy'])->name('destroy.direct-parts-sell');
        Route::get('/get/customer-info', [DirectPartsSellController::class, 'getCustomerInfo'])->name('get-customer-info');
        Route::get('/parts-sell/show/{id}', [DirectPartsSellController::class, 'show'])->name('show.direct-parts-sell');
    });

    Route::group(['namespace' => 'Inventory'], function () {
        //only those have manage_user permission will get access
        Route::get('/users', [UserController::class,'index']);
        Route::get('/user/get-list', [UserController::class,'getUserList']);
        Route::get('/user/create', [UserController::class,'create']);
        Route::post('/user/create', [UserController::class,'store'])->name('create-user');
        Route::get('/user/{id}', [UserController::class,'edit']);
        Route::post('/user/update', [UserController::class,'update']);
        Route::get('/user/delete/{id}', [UserController::class,'delete']);

        //only those have manage_role permission will get access
            Route::get('/roles', [RolesController::class,'index']);
            Route::get('/role/get-list', [RolesController::class,'getRoleList']);
            Route::post('/role/create', [RolesController::class,'create']);
            Route::get('/role/edit/{id}', [RolesController::class,'edit']);
            Route::post('/role/update', [RolesController::class,'update']);
            Route::get('/role/delete/{id}', [RolesController::class,'delete']);

        //only those have manage_permission permission will get access
            Route::get('/permission', [PermissionController::class,'index']);
            Route::get('/permission/get-list', [PermissionController::class,'getPermissionList']);
            Route::post('/permission/create', [PermissionController::class,'create']);
            Route::get('/permission/update', [PermissionController::class,'update']);
            Route::get('/permission/delete/{id}', [PermissionController::class,'delete']);

        // Ticket Management
        Route::get('/customer-info', function () { return view('ticket-management.customer_info'); });
        Route::get('/create-ticket', function () { return view('ticket-management.create'); });
        Route::get('/ticket-closing', function () { return view('ticket-management.ticket-closing'); });


        // Inventory
        Route::get('inventory', [InventoryController::class,'index']);
        Route::get('inventory/create',[InventoryController::class,'create'])->name('create-inventory');
        Route::post('inventory/create',[InventoryController::class,'store']);
        Route::get('inventory/edit', [InventoryController::class,'edit'])->name('edit-inventory')->middleware('signed');
        // Route::patch('inventory/{id}', [InventoryController::class,'update']);
        Route::post('inventory/update/{id}', [InventoryController::class,'update'])->name('update-inventory');
        Route::delete('inventory/{id}', [InventoryController::class,'destroy']);
        Route::get('inventory/show/{id}', [InventoryController::class,'show'])->name('show-inventory');
        //Large Data Upload
        Route::get('inventory/receive/sample-excel', [InventoryController::class,'sampleExcel'])->name('sample-parts-receive-excel');
        Route::post('inventory/receive/import', [InventoryController::class,'import'])->name('import-receive-inventory');

        Route::get('inventory/sample-excel', [PartsController::class,'sampleExcel'])->name('sample-parts-excel');
        Route::post('inventory/import', [PartsController::class,'import'])->name('import-inventory');
        Route::get('/stock-list', function () { return view('inventory.stock-list'); });
        Route::get('get/price/{part_id}', [PriceManagementController::class, 'getPrice'])->name('get-price');
        Route::get('inventory/parts-receive/rows', [InventoryController::class, 'getPartReceiveRow'])->name('part-receive-row');

    });

    // Stock Report
    Route::group(['prefix' => 'inventory', 'as' => 'inventory.'], function () {
        //source
        Route::post('source-edit', [SourceController::class, 'edit'])->name('source-edit');
        Route::get('source/destroy/{id}', [SourceController::class, 'destroy']);
        Route::get('source/status/{id}', [SourceController::class, 'activeInactive'])
            ->name('source.status');
        Route::resource('source', 'Inventory\SourceController')->except('create', 'edit', 'show');

        // Parts Model
        Route::get('parts_model/status/{id}', [ModelController::class, 'activeInactive'])
            ->name('parts_model.status');
        Route::get('parts_model/destroy/{id}', [ModelController::class, 'destroy']);
        Route::resource('parts_model', 'Inventory\ModelController')->except('show', 'destroy');
        Route::get('/sample/part-model/excel', [ModelController::class, 'sampleExcel'])->name('sample-part-model-excel');
        Route::post('/import/excel/part-model', [ModelController::class, 'import'])->name('import-part-model');

        // Stock
        Route::get('stock', [InventoryController::class,'stock'])->name('stock');
        Route::get('stock-in-hand', [InventoryController::class,'stockInHandGet'])->name('stock-in-hand');
        // Route::post('stock-in-hand', [InventoryController::class,'stockInHandPost'])->name('stock-in-hand');
        Route::get('stock-in-hand-all', [InventoryController::class,'stockInHandPostAll'])->name('stock-in-hand-all');
        Route::get('stock-in-hand-all-by-store/{id}', [InventoryController::class,'stockInHandPostAllByStore'])->name('stock-in-hand-all-by-store');

        Route::get('stock_details', [InventoryController::class,'stockDetails']);
        Route::get('show-inventory-details/{id}/{store_id}', [InventoryController::class, 'inventoryDetails'])->name('show-inventory-details');
        Route::get('model', [ModelController::class,'getModel']);
        Route::get('get-part', [ModelController::class,'getPart']);
        Route::get('get/part-model/{id}', [PartCategoryController::class,'getPartModel']);
        Route::get('get-part/{id}', [PartCategoryController::class,'getPart']);
        Route::get('parts/model', [ModelController::class,'getPartsModel']);

        Route::get('parts/stock', [ModelController::class,'getPartsStock']);
        Route::get('outlet/parts/stock', [ModelController::class,'getPartsStockForOutlet']);
        Route::get('outlet/parts/stock/edit', [ModelController::class, 'getPartsStockForOutletEdit']);
        Route::get('parts/outlet/stock', [ModelController::class,'getPartsStock']);
        Route::get('parts/technician/stock', [TechnicianRequisitionController::class,'getPartsStock']);
        Route::get('parts_consumption/technician/stock', [ConsumptionController::class,'getPartsStock']);
        Route::get('parts/technician/stock-for-partsreturn', [TechnicianRequisitionController::class,'getPartsStockForPartsReturn']);
        Route::get('parts/technician/stock-for-job', [TechnicianRequisitionController::class,'getPartsStockForJob']);

        Route::get('getStockData', [ModelController::class,'getStockDetails']);
        Route::get('getStockInfo', [ModelController::class,'getStocInfo']);

        //store
        Route::get('store/destroy/{id}', [StoreController::class, 'destroy']);
        Route::get('store/status/{id}', [StoreController::class, 'activeInactive'])
            ->name('store.status');
        Route::resource('/store', 'Inventory\StoreController');

        //parts
        // Route::post('parts/category/search', [PartsController::class, 'search'])
        //     ->name('parts.category.search');
        Route::get('parts/status/{id}', [PartsController::class, 'aciveInactive'])
            ->name('parts.status');
        Route::resource('parts', 'Inventory\PartsController')->except('destroy');
        Route::get('parts/destroy/{id}', [PartsController::class,'destroy']);
        Route::post('parts/get_all', [PartsController::class,'parts'])->name('get_parts'); //Select2 Ajax
        Route::post('categories/get_all', [CategoryController::class,'cateGories'])->name('get_categories'); //Select2 Ajax

        //parts category
        Route::get('part-category/status/{id}', [PartCategoryController::class, 'aciveInactive'])
            ->name('part-category.status');
        Route::resource('part-category', 'Inventory\PartCategoryController')->except('create', 'show','destroy');
        Route::get('part-category/destroy/{id}', [PartCategoryController::class,'destroy']);
        Route::get('/sample/part-category/excel', [PartCategoryController::class, 'sampleExcel'])->name('sample-part-category-excel');
        Route::post('/import/excel/part-category', [PartCategoryController::class, 'import'])->name('import-part-category');

        //Rack
        Route::get('racks/destroy/{id}', [RackController::class, 'destroy']);
        Route::get('racks/status/{id}', [RackController::class, 'activeInactive'])
            ->name('racks.status');
        Route::resource('racks', 'Inventory\RackController');

        //Bins
        Route::get('get/rack/{id}', [BinController::class, 'getRack']);//Should Be Removed
        Route::get('get/bin/{id}', [BinController::class, 'getBin']);//Should Be Removed

        Route::get('bins/destroy/{id}', [BinController::class, 'destroy']);
        Route::get('bins/status/{id}', [BinController::class, 'activeInactive'])
            ->name('bins.status');
        Route::resource('bins', 'Inventory\BinController')->except('show');
        Route::get('/sample/bin/excel', [BinController::class, 'sampleExcel'])->name('sample-bin-excel');
        Route::post('/import/excel/bin', [BinController::class, 'import'])->name('import-bin');

        //product category
        Route::get('/product-category', [ProductCategoryController::class, 'index'])->name('product-category-index');
        Route::post('/product-category/store', [ProductCategoryController::class, 'store'])->name('create.product-category');
        Route::get('/product-category/edit/{id}', [ProductCategoryController::class, 'edit'])->name('edit.product-category');
        Route::post('/product-category/update/{id}', [ProductCategoryController::class, 'update'])->name('update.product-category');
        Route::delete('/product-category/delete/{id}', [ProductCategoryController::class, 'destroy'])->name('destroy.product-category');

        //fault
        Route::get('fault/destroy/{id}', [FaultController::class, 'destroy']);
        Route::get('fault/status/{id}', [FaultController::class, 'activeInactive'])
            ->name('fault.status');
        Route::resource('fault', 'Inventory\FaultController')->except('show', 'destroy');

        // Price Management
        Route::resource('price-management', 'Inventory\PriceManagementController');
        Route::get('price-management-history/{id}', [PriceManagementController::class,'history'])->name('price-management-history');
        Route::get('parts/get-price', [PriceManagementController::class, 'getPrice'])->name('get-part-price');

        Route::get('/sample/price/excel', [PriceManagementController::class, 'sampleExcel'])->name('sample_price_excel');
        Route::post('/import/excel/price', [PriceManagementController::class, 'import'])->name('import_price');

        // Rack & Bin Management
        Route::get('rack-bin-management/destroy/{id}', [RackBinManagementController::class, 'destroy']);
        Route::resource('rack-bin-management', 'Inventory\RackBinManagementController')->except('show', 'destroy');
        Route::get('/sample/rack_bin/excel', [RackBinManagementController::class, 'sampleExcel'])->name('sample_rack_bin_excel');
        Route::post('/import/excel/rack_bin', [RackBinManagementController::class, 'import'])->name('import_rack_bin');
    });

    //Branch
    Route::group(['prefix' => 'branch', 'as' => 'branch.'], function () {
        Route::get('requisitions/create', [RequisitionController::class, 'outletRequisitionCreate'])->name('requisition.create');
        Route::post('requisitions/store', [RequisitionController::class, 'outletRequisitionStore'])->name('requisition.store');

        Route::get('requisitions', [RequisitionController::class, 'outletRequisitionList'])->name('requisitions');

        Route::get('requisitions/edit/{id}', [RequisitionController::class, 'outletRequisitionEdit'])->name('requisition.edit');
        Route::put('requisitions/update/{id}', [RequisitionController::class, 'outletRequisitionUpdate'])->name('requisition.update');
        Route::get('requisitions/destroy/{id}', [RequisitionController::class, 'outletRequisitionDestroy'])->name('requisition.destroy');
        Route::get('requisitions/decline/{id}', [RequisitionController::class, 'requisitationDecline'])->name('requisitions.decline');

        Route::get('requisitions/show/{id}', [RequisitionController::class, 'outletRequisitionShow'])->name('requisitions-details');
        Route::get('allocations', [BranchAllocationController::class, 'branchAllocationIndex'])->name('allocations');
        Route::get('allocation/show/{id}', [BranchAllocationController::class, 'showBranchAllocation'])->name('allocation-details');
        Route::get('re-allocations', [ReAllocationController::class, 'reAllocatedRequisitionIndex'])->name('re-allocations');
        Route::get('re-allocation/show/{id}',  [ReAllocationController::class, 'show'])->name('re-allocation.show');

        Route::get('receive/', [
            BranchAllocationReceivedController::class,'index'])->name('allocation.received.index');

        Route::get('receive/{id}', [BranchAllocationReceivedController::class,'allocationReceiveForm'])->name('requisition-receive-form');

        Route::post('receive/store', [BranchAllocationReceivedController::class,'requisitionOutletReceiveStore'])->name('requisition-receive-store');

        Route::get('receive/show/{id}', [BranchAllocationReceivedController::class,'show'])->name('allocation.received.show');

        Route::get('receive/{id}/edit', [BranchAllocationReceivedController::class,'edit'])->name('allocation.received.edit');

        Route::put('receive/{id}', [BranchAllocationReceivedController::class,'update'])->name('allocation.received.update');

        Route::delete('receive/delete/{id}', [BranchAllocationReceivedController::class,'destroy'])->name('allocation.received.destroy');

        //Reallocation
        Route::get('re-allocated-receive/{id}', [ReAllocationController::class, 'requisitionOutletReceiveForm'])->name('re-allocated-receive');
        Route::post('re-allocated-receive/store', [ReAllocationController::class, 'requisitionOutletReceiveStore'])->name('re-allocated-store');

        // Branch Received Allocation
        Route::get('received/allocations', [BranchReceivedAllocationController::class, 'index'])
                ->name('received.allocations');

        //Technicians Requisition in Branch
        // Route::get('technician-requisitions', [TechnicianRequisitionController::class,'indexForStores'])->name('technician-requisitions');

        Route::get('technician/requisitions', [BranchTechnicianAllocationController::class, 'requisitions'])
                ->name('technician-requisitions');

        Route::get('technician/allocations', [BranchTechnicianAllocationController::class, 'allocations'])
                ->name('technician.allocations');

        Route::get('technician/allocations/{id}/edit', [BranchTechnicianAllocationController::class, 'edit'])
                ->name('technician.allocations.edit');

        Route::put('technician/allocations/update/{id}', [BranchTechnicianAllocationController::class, 'update'])
                ->name('technician.allocations.update');

        Route::get('technician/allocations/show/{id}', [BranchTechnicianAllocationController::class, 'show'])
                ->name('technician.allocations.show');

        Route::delete('technician/allocations/destroy/{id}', [BranchTechnicianAllocationController::class, 'destroy'])
                ->name('technician.allocations.destroy');

        Route::get('technician_allocation', [TechnicianAllocationController::class, 'branchAllocationIndex'])->name('technician_allocation');
        Route::get('technician-requisitions/show/{id}', [TechnicianRequisitionController::class,'showforbranch'])->name('technician-requisitions.show');
        Route::get('requisition-allocate/{id}', [BranchTechnicianAllocationController::class, 'requisitationAllocate'])->name('requisition-allocate');
        Route::post('requisition-allocate/store', [BranchTechnicianAllocationController::class, 'requisitationAllocateStore'])->name('requisition-allocate.store');

        //
        Route::get('stocks', [InventoryController::class,'stockOutlet'])->name('stocks');
        Route::get('stock/details/{id}/{store_id}', [InventoryController::class,'outletInventoryDetails'])->name('stock.details');
        //Parts Return
        Route::get('parts-return', [TecnicianPartsReturnController::class,'indexforBranch'])->name('parts-return');
        Route::resource('branch-parts-return', 'Inventory\BranchPartsReturnController');
        Route::post('update/parts-return/{id}', [BranchPartsReturnController::class,'updatePartsReturn'])->name('update-parts-return');
        Route::get('parts-return/stock', [BranchPartsReturnController::class,'partsReturnRow'])->name('parts-return-row');
        Route::get('parts-return/received-details/{id}', [TecnicianPartsReturnController::class,'receivedShowForBranch'])->name('parts-return.received-details-view');
        Route::get('parts-return/show/{id}', [TecnicianPartsReturnController::class,'showForBranch'])->name('parts-return.show');
        // Route::get('parts-return/receive/{id}', [TecnicianPartsReturnController::class,'receiveParts'])->name('parts-return.receive');
        // Route::get('return-parts/receive/store', [TecnicianPartsReturnController::class,'receivePartsStore'])->name('parts-return.received');
        Route::get('parts-return/received', [TecnicianPartsReturnController::class,'receivedIndexforBranch'])->name('parts-return.received');
        Route::get('parts-return/receive/edit/{id}', [TecnicianPartsReturnController::class,'receiveEdit'])->name('parts-return.receive.edit');
        Route::post('parts-return/receive/update', [TecnicianPartsReturnController::class,'receiveupdate'])->name('parts-return.receive.update');
        // Route::post('parts-return/receive/update', [TecnicianPartsReturnController::class,'receiveupdate'])->name('parts-return.receive.update');
        Route::delete('parts-return/receive/delete/{id}', [TecnicianPartsReturnController::class, 'receiveDestroy'])->name('parts-return.receive.destroy');
        Route::get('parts-return/receive/{id}', [TecnicianPartsReturnController::class,'receiveParts'])->name('parts-return.receive');
        Route::post('parts-return/receive/store', [TecnicianPartsReturnController::class,'receivePartsStore'])->name('parts-return.receive.store');
        //Receive returned parts

    });
    //Central
    Route::group(['prefix' => 'central', 'as' => 'central.'], function () {
        Route::get('requisitions', [RequisitionController::class, 'centralRequisitionList'])->name('requisitions');
        Route::get('requisitions/show/{id}', [RequisitionController::class, 'centralRequisitionShow'])->name('requisitions.show');
        Route::get('requisitions/decline/{id}', [RequisitionController::class, 'requisitationDecline'])->name('requisitions.decline');
        Route::get('requisitions/allocate/{id}', [RequisitionAllocationController::class, 'requisitationAllocate'])->name('requisitations.allocate');
        // Route::post('requisition/allocate/store', [RequisitionController::class, 'requisitationAllocateStore'])->name('requisitations.allocate.store');
        Route::post('requisition/allocate/store', [RequisitionAllocationController::class, 'requisitationAllocateStore'])->name('requisitations.allocate.store');

        //Reallocation
        Route::get('re-allocations', [ReAllocationController::class, 'reAllocatedRequisitionIndexForCentral'])->name('re-allocations');
        Route::get('re-allocate/{id}', [ReAllocationController::class, 'requisitationReAllocate'])->name('re-allocate');
        Route::post('re-allocate/store', [ReAllocationController::class, 'requisitationReAllocateStore'])->name('re-allocate-store');
        Route::get('re-allocation/edit/{id}', [ReAllocationController::class, 'edit'])->name('re-allocation.edit');
        Route::put('re-allocation/update/{id}', [ReAllocationController::class, 'update'])->name('re-allocation.update');

        Route::get('re-allocation/show/{id}',  [ReAllocationController::class, 'show'])->name('re-allocation.show');
        Route::get('re-allocation/destroy/{id}',  [ReAllocationController::class, 'destroy'])->name('re-allocation.destroy');
        //branch parts return
        Route::get('branch-parts-return', [BranchPartsReturnController::class,'partsReturnIndexForCentral'])->name('branch-parts-return');
        Route::get('branch-parts-return/show-details/{id}', [BranchPartsReturnController::class,'showForCentral'])->name('branch-parts-return.show');
        Route::get('branch-parts-return/received-details/{id}', [BranchPartsReturnController::class,'receivedShowForCentral'])->name('branch-parts-return.received-show');
        Route::get('branch-parts-return/receive/{id}', [BranchPartsReturnController::class,'receiveParts'])->name('parts-return.receive');
        Route::post('branch-parts-return/receive/store', [BranchPartsReturnController::class,'receivePartsStore'])->name('parts-return.receive.store');
        Route::get('branch-parts-return/receive/edit/{id}', [BranchPartsReturnController::class,'receiveEdit'])->name('parts-return.receive.edit');
        Route::post('branch-parts-return/receive/update', [BranchPartsReturnController::class,'receiveupdate'])->name('parts-return.receive.update');
        Route::get('branch-parts-return/received', [BranchPartsReturnController::class,'receivedIndexforCentral'])->name('parts-return.received');
        Route::delete('branch-parts-return/received/delete/{id}', [BranchPartsReturnController::class,'receiveDestroy'])->name('parts-return.received.destroy');

        // Central Requisitions Allocation
        Route::resource('requisitions/allocation', 'Requisition\RequisitionAllocationController')->except('create', 'store','destroy');
        Route::get('requisitions/allocation/delete/{id}', [RequisitionAllocationController::class,'destroy']);
        Route::get('requisitions/allocation/print/{id}', [RequisitionAllocationController::class,'print'])->name('requisitions.allocation.print');
    });
    //Accounts Management
    Route::group(['namespace' => 'Account'], function () {
        // Expense
        Route::get('/expense', [ExpenseController::class, 'index'])->name('expense-index');
        Route::post('/expense/store', [ExpenseController::class, 'store'])->name('create.expense');
        Route::get('/expense/edit/{id}', [ExpenseController::class, 'edit'])->name('edit.expense');
        Route::post('/expense/update/{id}', [ExpenseController::class, 'update'])->name('update.expense');
        Route::get('/expense/delete/{id}', [ExpenseController::class, 'destroy'])->name('destroy.expense');

        // Expense Item
        Route::get('expense-items/destroy/{id}', [ExpenceItemController::class, 'destroy']);
        Route::get('expense-items/status/{id}', [ExpenceItemController::class, 'activeInactive'])
            ->name('expense-items.status');
        Route::resource('expense-items', 'ExpenceItemController')->except('create', 'show', 'destroy');

        //Revenue
        Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue-index');
        Route::post('/revenue/store', [RevenueController::class, 'store'])->name('create.revenue');
        Route::get('/revenue/edit/{id}', [RevenueController::class, 'edit'])->name('edit.revenue');
        Route::post('/revenue/update/{id}', [RevenueController::class, 'update'])->name('update.revenue');
        Route::get('/revenue/delete/{id}', [RevenueController::class, 'destroy'])->name('destroy.revenue');

        //Bank Account
        Route::get('/bank-account', [BankAccountController::class, 'index'])->name('bank-account-index');
        Route::post('/bank-account/store', [BankAccountController::class, 'store'])->name('create.bank-account');
        Route::get('/bank-account/edit/{id}', [BankAccountController::class, 'edit'])->name('edit.bank-account');
        Route::post('/bank-account/update/{id}', [BankAccountController::class, 'update'])->name('update.bank-account');
        Route::get('/bank-account/delete/{id}', [BankAccountController::class, 'destroy'])->name('destroy.bank-account');
        Route::get('/deposit', [DepositController::class, 'index'])->name('deposit-index');
        Route::post('/deposit/store', [DepositController::class, 'store'])->name('deposit.store');
        Route::get('/deposit/edit/{id}', [DepositController::class, 'edit'])->name('edit.deposit');
        Route::post('/deposit/update/{id}', [DepositController::class, 'update'])->name('update.deposit');
        Route::get('/deposit/delete/{id}', [DepositController::class, 'destroy'])->name('destroy.deposit');

        Route::get('pettycash', [AccountBranchController::class, 'pettycash'])->name('transections.pettycash');
        Route::get('transections/branch', [AccountBranchController::class, 'index'])->name('transections.branch');
        Route::get('transections/branch/show/{id}', [AccountBranchController::class, 'show'])->name('transections.branch.show');
        Route::resource('cash-transections', 'CashTransectionController')->except('destroy');
        Route::get('/cash-transections/destroy/{id}', [CashTransectionController::class, 'delete']);
    });

    //Requisition
    Route::group(['namespace' => 'Requisition'], function () {
        Route::get('/requisition/details', [RequisitionController::class, 'requisitationDetaails'])->name('requisition.details');

        // Route::get('/requisition/outlet/receive/{id}', [RequisitionController::class, 'requisitionOutletReceive'])->name('outlet.requisitionReceive');

        Route::resource('requisitions', 'RequisitionController');
        Route::resource('pending-requisitions', 'PendingRequisitonController');

        //Technician Requisition Route
        Route::group(['prefix' => 'technician', 'as' => 'technician.'], function () {
            //Attendance
            Route::get('attendance', [AttendanceController::class,'indexForTechnician'])->name('attendance');
            Route::resource('requisition', 'TechnicianRequisitionController');
            Route::delete('requisitions/destroy/{id}', [RequisitionController::class, 'outletRequisitionDestroy'])->name('requisitionDestroy');
            Route::get('requisition-by-job/{id}', [TechnicianRequisitionController::class,'requisitionCreateByJob']);

            //Allocation
            Route::get('allocation/show/{id}', [TechnicianAllocationController::class, 'showTechnicianAllocation'])->name('allocation.show');
            Route::get('allocation', [TechnicianAllocationController::class, 'technicianAllocationIndex'])->name('allocation');

            //Consumption
            Route::get('consumption/edit/{id}', [ConsumptionController::class,'edit'])->name('consumption.edit');
            Route::put('consumption/update/{id}', [ConsumptionController::class,'update'])->name('consumption.update');
            Route::get('consumption-by-job/{id}', [ConsumptionController::class,'consumptionCreateByJob']);
            Route::post('consumption/store', [ConsumptionController::class,'consumptionStoreByJob'])->name('consumption.store');

            Route::get('/allocations/receive', [TechnicianAllocationReceivedController::class, 'index'])->name('requisition.allocate.receive');

            Route::get('/allocations/receive/{id}', [TechnicianAllocationReceivedController::class,'requisitionTechnicianReceiveform'])->name('requisitionReceive-form');

            Route::post('/allocations/receive/store', [TechnicianAllocationReceivedController::class,'requisitionTechnicianReceiveStore'])->name('requisitionReceiveStore');

            Route::get('/allocations/receive/{id}/edit', [TechnicianAllocationReceivedController::class,'edit'])->name('allocation.received.edit');

            Route::put('/allocations/receive/{id}', [TechnicianAllocationReceivedController::class,'update'])->name('allocation.received.update');

            Route::get('/allocations/receive/show/{id}', [TechnicianAllocationReceivedController::class,'show'])->name('allocation.received.show');

            Route::delete('/form/requisition/receive/{id}', [TechnicianAllocationReceivedController::class,'destroy'])->name('allocation.received.destroy');

            // Need to remove
            // Route::get('/requisition/receive/{id}', [TechnicianRequisitionController::class, 'requisitionTechnicianReceive'])->name('requisitionReceive');

            //Stock
            Route::get('stock', [InventoryController::class,'stockForTechnician'])->name('stock');
            Route::get('stock_details/{id}/{store_id}', [InventoryController::class,'stockForTechnicianDetails'])->name('stock_details');
            //Employee Job
            Route::get('jobs', [JobController::class, 'employeeJobs'])->name('jobs');
            Route::get('jobs/show/{id}', [JobController::class, 'employeeJobShow'])->name('jobs.show');
            // Route::get('jobs/status/{id}', [JobController::class, 'status'])->name('jobs.status');

            Route::get('submission/create/{id}', [JobSubmissionController::class, 'createJobSubmission'])->name('job-submission-create');
            Route::post('submission/store', [JobSubmissionController::class, 'storeJobSubmission'])->name('job-submission-store');
            Route::get('submitted-jobs', [JobSubmissionController::class, 'index'])->name('submitted-jobs');
            Route::get('submitted-jobs/show/{id}', [JobSubmissionController::class, 'show'])->name('submitted-job-show');
            Route::get('submitted-jobs/print/{id}', [JobSubmissionController::class, 'print'])->name('submitted-job-print');
            Route::get('submission/photo/upload/{id}', [JobSubmissionController::class, 'submissionImageUpload'])->name('submission.photo.upload');
            Route::post('submission/photo/store', [JobSubmissionController::class, 'submissionImageStore'])->name('submission.photo.store');
            Route::get('submission/photo/download/{filename}', [JobSubmissionController::class, 'imageDownload'])->name('photo.download');
            //Parts
            Route::get('parts-return', [TecnicianPartsReturnController::class,'indexforTechnician'])->name('parts-return');
            Route::get('parts-return/create', [TecnicianPartsReturnController::class,'create'])->name('parts-return.create');
            Route::post('parts-return/store', [TecnicianPartsReturnController::class,'store'])->name('parts-return.store');
            Route::get('parts-return/show/{id}', [TecnicianPartsReturnController::class,'show'])->name('parts-return.show');
            Route::get('parts-return/edit/{id}', [TecnicianPartsReturnController::class,'edit'])->name('parts-return.edit');
            Route::put('parts-return/update/{id}', [TecnicianPartsReturnController::class,'update'])->name('parts-return.update');
            Route::delete('parts-return/destroy/{id}', [TecnicianPartsReturnController::class,'destroy'])->name('parts-return.destroy');
            // Route::resource('parts-return', 'Inventory\TecnicianPartsReturnController');
            Route::get('submitted-jobs/{id}/edit', [JobSubmissionController::class, 'edit'])->name('submitted-jobs.edit');
            Route::put('submitted-jobs/{id}', [JobSubmissionController::class, 'update'])->name('submitted-jobs.update');
            Route::get('submitted-jobs/destroy/{id}', [JobSubmissionController::class, 'destroy'])->name('submitted-jobs.destroy');
            Route::get('jobstatus/{employee}', [JobController::class, 'techNicianJobStatus']);
        });

    });

    Route::group(['prefix' => 'loan', 'as' => 'loan.'], function () {
        Route::post('received-loan/update', [LoanController::class,'updateReceivedLoan'])->name('received-loan.update');
        Route::get('received-loan/edit/{id}', [LoanController::class,'editReceivedLoan'])->name('received-loan.edit');
        Route::delete('received-loan/destroy/{id}', [LoanController::class,'destroyReceivedLoan'])->name('received-loan.destroy');
        Route::get('all-received-loans', [LoanController::class,'allReceivedLoans'])->name('received-loans');
        Route::post('receive/store', [LoanController::class,'loanReceiveStore'])->name('loan-receive-store');
        Route::get('receive/form/{id}', [LoanController::class,'loanReceive'])->name('loan-receive');
        Route::post('request/update/{id}', [LoanController::class,'updateLoan'])->name('loan.update');
        Route::get('request/edit-parts-row', [LoanController::class,'getPartsRows'])->name('loan-request.edit.parts-row');
        Route::get('allocated-list', [LoanController::class,'allocatedList'])->name('loan-allocated.list');
        Route::get('details', [LoanController::class,'loanDetails'])->name('details');
        Route::resource('loan-request', 'Loan\LoanController');

        Route::get('accepted-loan/edit/{id}', [AcceptLoanRequestController::class,'editAcceptedLoan'])->name('accepted-loan.edit');
        Route::get('all-accepted-loans', [AcceptLoanRequestController::class,'acceptedLoans'])->name('accepted-loans');
        Route::get('all-accepted-loans/show/{id}', [AcceptLoanRequestController::class,'showForAllAcceptedLoans'])->name('all-accepted-loans.show');
        Route::get('accept/loan-request/{id}', [AcceptLoanRequestController::class,'issueLoan'])->name('issue-loan');
        Route::resource('accept-loan', 'Loan\AcceptLoanRequestController');
        Route::post('accepted-loan/update/{id}', [AcceptLoanRequestController::class, 'updateAcceptedLoan'])->name('accepted-loan.update');
    });

    //Employee/Technician Route
    Route::group(['prefix' => 'hrm', 'as' => 'hrm.'], function () {
        // Route::resource('technician', 'Employee\EmployeeController');
        Route::get('technician', [EmployeeController::class,'index'])->name('technician');
        Route::get('technician/create', [EmployeeController::class,'create'])->name('technician.create');
        Route::post('technician/store', [EmployeeController::class,'store'])->name('technician.store');
        Route::get('technician/edit/{id}', [EmployeeController::class,'edit'])->name('technician.edit');
        Route::put('technician/update/{id}', [EmployeeController::class,'update'])->name('technician.update');
        Route::get('technician/show/{id}', [EmployeeController::class,'show'])->name('technician.show');
        Route::get('technician/destroy/{id}', [EmployeeController::class,'destroy'])->name('technician.destroy');
        Route::post('technician/filter', [EmployeeController::class,'filterEmployee'])->name('technician.filter');
        Route::get('get/store/{id}', [EmployeeController::class,'getStore'])->name('get.outlet-store');
        Route::get('/user/profile', [EmployeeController::class, 'profile'])->name('user.profile');
        Route::get('technician/status/{id}',[EmployeeController::class, 'updateStatus'])->name('technician.status');

        // Designation
        Route::get('designation/destroy/{id}', [DesignationController::class, 'destroy']);
        Route::get('designation/status/{id}', [DesignationController::class, 'activeInactive'])
            ->name('designation.status');
        Route::resource('designation', 'Employee\DesignationController')->except('destroy');

        // Designation
        Route::resource('attendance', 'Employee\AttendanceController');
        Route::resource('teamleader', 'Employee\TeamLeaderController')->except('destroy');
        Route::get('teamleader/destroy/{id}', [TeamLeaderController::class, 'destroy']);
        Route::get('technicians/{type}',[EmployeeController::class,'getEmployees']);

    });

    Route::prefix('tickets')->namespace('Ticket')->group(function(){
        // Warranty Types
        Route::get('warranty-types/destroy/{id}', [WarrantyTypeController::class, 'destroy']);
        Route::get('warranty-types/status/{id}', [WarrantyTypeController::class, 'activeInactive'])
        ->name('warranty-types.status');
        Route::resource('warranty-types', 'WarrantyTypeController')->except('show', 'destroy');

        // Service Types
        Route::get('service-types/destroy/{id}', [ServiceTypeController::class, 'destroy']);
        Route::get('service-types/status/{id}', [ServiceTypeController::class, 'activeInactive'])
        ->name('service-types.status');
        Route::resource('service-types', 'ServiceTypeController')->except('show', 'destroy');

        // Job Priority
        Route::post('job-priority/status/{id}', [JobPriorityController::class, 'aciveInactive'])
                ->name('job-priority.status');
        Route::post('job-priority/update', [JobPriorityController::class, 'update'])->name('update.jobs');
        Route::get('job-priority/{id}', [JobPriorityController::class, 'edit'])->name('edit.jobs');
        Route::resource('job-priority', 'JobPriorityController')->except('show', 'create');

        // Product Conditions
        Route::post('product_conditions/status/{id}', [ProductConditionController::class, 'aciveInactive'])
        ->name('product_conditions.status');
        Route::get('product_conditions/{id}', [ProductConditionController::class, 'edit'])->name('edit.product_conditions');
        Route::post('product_conditions/update', [ProductConditionController::class, 'update'])->name('update.product_conditions');
        Route::resource('product_conditions', 'ProductConditionController')->except('show', 'create');

        // Accessories
        Route::post('accessories/status/{id}', [AccessoriesController::class, 'aciveInactive'])
                ->name('accessories.status');
        Route::get('accessories/{id}', [AccessoriesController::class, 'edit'])->name('edit.accessories');
        Route::post('accessories/update', [AccessoriesController::class, 'update'])->name('update.accessories');
        Route::resource('accessories', 'AccessoriesController')->except('show', 'create');

        // Receive mode
        Route::get('receive-mode/destroy/{id}', [ReceiveModeController::class, 'destroy']);
        Route::get('receive-mode/status/{id}', [ReceiveModeController::class, 'activeInactive'])
        ->name('receive-mode.status');
        Route::resource('receive-mode', 'ReceiveModeController')->except('show', 'destroy');

        // Delivery mode
        Route::get('delivery-mode/destroy/{id}', [DeliveryModeController::class, 'destroy']);
        Route::get('delivery-mode/status/{id}', [DeliveryModeController::class, 'activeInactive'])
        ->name('delivery-mode.status');
        Route::resource('delivery-mode', 'DeliveryModeController')->except('show', 'destroy');

        Route::get('purchase-history', [PurchaseHistoryController::class, 'index']);
        Route::get('customer-purchase-history', [PurchaseHistoryApiController::class, 'purchaseHistoryGet'])
            ->name('customer-purchase-history');
        Route::post('customer-purchase-history', [PurchaseHistoryApiController::class, 'purchaseHistoryPost']);

        Route::get('ticket-index', [PurchaseHistoryController::class, 'ticketIndex'])->name('ticket-index');
        Route::get('ticket-create/{id}', [PurchaseHistoryController::class, 'ticketcreate']);
        Route::get('ticket-purchase-show/{id}', [PurchaseHistoryController::class, 'purchaseShow']);
        Route::post('ticket-store', [PurchaseHistoryController::class, 'storeTicket'])->name('store-ticket');
        Route::get('ticket/edit/{id}', [PurchaseHistoryController::class, 'editTicket'])->name('edit-ticket-details');
        Route::post('ticket/update/{id}', [PurchaseHistoryController::class, 'updateTicket'])->name('update-ticket');
        Route::get('ticket/delete/{id}', [PurchaseHistoryController::class, 'destroyTicket'])->name('ticket-destroy');
        Route::get('/get/service/amount', [PurchaseHistoryController::class, 'serviceAmount']);
        Route::get('/ticket/show/{id}', [PurchaseHistoryController::class, 'showTicket'])->name('show-ticket-details');
        Route::get('/close/{id}', [PurchaseHistoryController::class, 'close'])->name('ticket-close');
        Route::get('/close-by-teamleader/{id}', [PurchaseHistoryController::class, 'closeByTeamleader'])
            ->name('ticket-close-by-teamleader');

        Route::get('/product_delivery_team_leader/{id}', [PurchaseHistoryController::class, 'deliveryByTeamLeader'])
            ->name('product_delivery_team_leader');
        Route::post('product_delivery_call_center', [PurchaseHistoryController::class, 'deliveryByCallCenter'])
            ->name('product_delivery_call_center');

        Route::post('re-open', [PurchaseHistoryController::class, 'reOpen'])->name('re-open');
        Route::get('/status/{id}', [PurchaseHistoryController::class, 'status'])->name('tickets.status');
        // Route::get('/total-jobs/{id}', [PurchaseHistoryController::class, 'totalJobs'])->name('total-job');

        Route::get('purchase-info', [ProductPurchaseController::class, 'purchaseInfo']);
        Route::get('purchaseinfo', [ProductPurchaseController::class, 'purchaseinfo_mobile']);
        Route::get('purchaseinfo-name', [ProductPurchaseController::class, 'purchaseInfoName']);

        Route::get('claim/{id}', [PurchaseHistoryController::class, 'claim'])->name('tickets.claim');
        Route::get('slip/{id}', [PurchaseHistoryController::class, 'slip'])->name('tickets.slip');
        //Excel Download
        Route::get('status/excel/{id}', [PurchaseHistoryController::class,'excelDownload'])->name('status.excel');
    });

    // Product Purchase
    Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
        Route::get('brand', [ProductPurchaseController::class, 'brand'])->name('brand');
        Route::get('model', [ProductPurchaseController::class, 'model'])->name('model');
        Route::get('/purchase/sample-excel', [ProductPurchaseController::class, 'sampleExcel'])->name('sample-purchase-excel');
        Route::post('/purchase/import/excel', [ProductPurchaseController::class, 'import'])->name('import-purchase');
        Route::resource('purchase', 'ProductPurchase\ProductPurchaseController')->except('destroy');
        Route::get('purchase/destroy/{id}', [ProductPurchaseController::class, 'destroy']);

        // Brand Model
        Route::get('brand_model/destroy/{id}', [BrandModelController::class, 'destroy']);
        Route::get('brand_model/status/{id}', [BrandModelController::class, 'activeInactive'])
            ->name('brand_model.status');
        Route::resource('brand_model', 'Product\BrandModelController')->except('show', 'destroy');

        Route::get('/brand-model/sample/excel', [BrandModelController::class, 'sampleExcel'])->name('sample-brand-model-excel');
        Route::post('/brand_model/import', [BrandModelController::class, 'import'])->name('import-brand-model');

        //category
        Route::get('sample/category/excel', [CategoryController::class, 'sampleExcel'])->name('sample-product-category-excel');
        Route::post('import/category', [CategoryController::class, 'import'])->name('import-product-category');
        Route::get('category/destroy/{id}', [CategoryController::class, 'destroy']);
        Route::get('category/status/{id}', [CategoryController::class, 'activeInactive'])
            ->name('category.status');
        Route::resource('category', 'Inventory\CategoryController')->except('show', 'create', 'destroy', 'edit'); //Product Category
        Route::post('edit-category', [CategoryController::class, 'edit'])->name('edit-category');
        //Brand
        Route::get('brand/status/{id}', [BrandController::class, 'activeInactive'])
            ->name('brand.status');
        Route::get('brand/destroy/{id}', [BrandController::class, 'destroy']);
        Route::get('/brand/sample/excel', [BrandController::class, 'sampleExcel'])
            ->name('sample-brand-excel');
        Route::post('import/brand', [BrandController::class, 'import'])
            ->name('import-brand');
        Route::get('get-brand', [ProductPurchaseController::class, 'brand'])->name('get-brand');
        Route::resource('brand', 'Inventory\BrandController')->except('show', 'destroy');
        // Route::post('/brand/search', [BrandController::class, 'search'])->name('brand.search');

        Route::get('/part-sell/stock', [PartSellController::class, 'partsStockDetails']);
        Route::resource('part-sell', 'Inventory\PartSellController');
    });

    // Call Center
    Route::group(['prefix' => 'call-center', 'as' => 'call-center.'], function () {
        //Customer Grade
        Route::get('customer-grade/destroy/{id}', [CustomerGradeController::class, 'destroy']);
        Route::get('customer-grade/status/{id}', [CustomerGradeController::class, 'activeInactive'])
            ->name('customer-grade.status');
         Route::resource('customer-grade', 'Customer\CustomerGradeController')->except('create', 'show','destroy');

         //customer
         Route::get('/customer', [CustomerController::class, 'index'])->name('customer-index');
         Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
         Route::get('/customer/edit/{id}', [CustomerController::class, 'edit'])->name('edit.customer');
         Route::post('/customer/update/{id}', [CustomerController::class, 'update'])->name('update.customer');
         Route::get('/customer/delete/{id}', [CustomerController::class, 'destroy'])->name('destroy.customer');
        //  Route::post('parts/get_all', [PartsController::class,'parts'])->name('get_parts'); //Select2 Ajax
         Route::post('customer_data', [CustomerController::class, 'customerData'])->name('customer_data');

         //customer feedback
         Route::get('customer-feedback-question/destroy/{id}', [FeedbackQuestionController::class, 'destroy']);
         Route::get('customer-feedback-question/status/{id}', [FeedbackQuestionController::class, 'activeInactive'])
            ->name('customer-feedback-question.status');
         Route::resource('customer-feedback-question', 'Customer\FeedbackQuestionController')->except('show', 'destroy');
         Route::resource('customer-feedback', 'Customer\CustomerFeedbackController');
    });

    // General
    Route::group(['prefix' => 'general', 'as' => 'general.'], function () {
        //Region
        Route::get('region/destroy/{id}', [RegionController::class, 'destroy']);
        Route::get('region/status/{id}', [RegionController::class, 'activeInactive'])
            ->name('region.status');
        Route::resource('region', 'Inventory\RegionController')->except('destroy');
        Route::get('get/district/{id}', [RegionController::class, 'getDistrict']);

        // Outlet
        Route::get('outlet/destroy/{id}', [OutletController::class, 'destroy']);
        Route::get('outlet/status/{id}', [OutletController::class, 'activeInactive'])
            ->name('outlet.status');
        Route::resource('outlet', 'Outlet\OutletController');

        // Get Thana for Ajax reques
        Route::get('get/thana/{id}', [ServiceSourcingVendorController::class, 'getThana']);
        Route::get('get/multi/thana/', [ServiceSourcingVendorController::class, 'getMultiThana']);

        // Product sourcing vendor
        Route::get('product-sourcing-vendor/destroy/{id}', [ProductSourcingVendorController::class, 'destroy']);
        Route::get('product-sourcing-vendor/status/{id}', [ProductSourcingVendorController::class, 'activeInactive'])
            ->name('product-sourcing-vendor.status');
        Route::resource('product-sourcing-vendor', 'Inventory\ProductSourcingVendorController')->except('show');

        //Service sourcing vendor
        Route::get('service-sourcing-vendor/destroy/{id}', [ServiceSourcingVendorController::class, 'destroy']);
        Route::get('service-sourcing-vendor/status/{id}', [ServiceSourcingVendorController::class, 'activeInactive'])
            ->name('service-sourcing-vendor.status');
        Route::resource('service-sourcing-vendor', 'Inventory\ServiceSourcingVendorController')->except('show', 'destroy');

        // Product Category
        Route::get('/product-category', [ProductCategoryController::class, 'index'])->name('product-category-index');
        Route::post('/product-category/store', [ProductCategoryController::class, 'store'])->name('create.product-category');
        Route::get('/product-category/edit/{id}', [ProductCategoryController::class, 'edit'])->name('edit.product-category');
        Route::post('/product-category/update/{id}', [ProductCategoryController::class, 'update'])->name('update.product-category');
        Route::delete('/product-category/delete/{id}', [ProductCategoryController::class, 'destroy'])->name('destroy.product-category');

        //Group Management
        Route::get('group/destroy/{id}', [GroupController::class, 'destroy']);
        Route::get('group/status/{id}', [GroupController::class, 'activeInactive'])
            ->name('group.status');
        Route::resource('group', 'Group\GroupController')->except('show');

    });

    //Job
    Route::group(['prefix' => 'job', 'as' => 'job.'], function () {
        // Route::get('job/create/{id}', 'Job\JobController');
        Route::get('job/create/{id}', [JobController::class, 'job_create'])->name('job_create');
        Route::get('employee/job-list', [JobController::class, 'employeeJobList'])->name('employee-jobs');
        Route::get('employee/job-list/details/{id}', [JobController::class, 'employeeJobDetails'])->name('show.employee-job.details');
        Route::get('employee/job/accept/{id}', [JobController::class, 'acceptJob'])->name('accept-job');
        Route::get('employee/job/start/{id}', [JobController::class, 'startJob'])->name('start-job');
        Route::post('employee/job/end/{id}', [JobController::class, 'endJob'])->name('end-job');
        Route::post('employee/job/pending/{id}', [JobController::class, 'pendingJob'])->name('pending-job');
        Route::post('deny', [JobController::class, 'denyJob'])->name('deny-job');
        Route::resource('job', 'Job\JobController')->except('destroy');
        Route::get('job/delete/{id}', [JobController::class, 'destroy'])->name('job-destroy');
        Route::get('job/claim/{id}', [JobController::class, 'claim'])->name('job-claim');
        Route::get('job/slip/{id}', [JobController::class, 'slip'])->name('job-slip');

        Route::get('submitted-jobs', [JobSubmissionController::class, 'index'])->name('submitted-jobs.index');
        Route::get('submitted-jobs/show/{id}', [JobSubmissionController::class, 'show'])->name('submitted-job-show');
        Route::get('status/{id}', [JobController::class, 'status'])->name('status');
        Route::get('job-status/excel/{id}', [JobController::class,'jobExcelDownload'])->name('job-status.excel');
        Route::get('testcsv', [JobController::class,'csvTest']);
    });

    Route::group(['prefix' => 'purchase', 'as' => 'purchase.'], function() {
        Route::get('/outlet/parts/stock', [PurchaseOrderController::class, 'getPartsStockForOutlet']);
        Route::get('/requisitions/details', [PurchaseOrderController::class, 'purchaseRequisitationDetails'])->name('requisitions.details');
        Route::resource('/requisitions', 'PO\PurchaseOrderController')->except('destroy');
        Route::get('requisitions/destroy/{id}', [PurchaseOrderController::class, 'destroy']);
    });

    Route::group(['prefix' => 'report', 'as' => 'report.'], function() {
        Route::any('job-report-get', [JobReportController::class, 'jobReportGet'])->name('job-report-get');
        Route::any('job-report-post', [JobReportController::class, 'jobReportPost'])->name('job-report-post');
        Route::get('kpi-report-get',[KpiReportController::class,'KpiReportGet'])->name('kpi-report-get');
        Route::get('kpi-report-post',[KpiReportController::class,'KpiReportPost'])->name('kpi-report-post');

        Route::get('consumption-report-get',[PartConsumptionReportController::class,'partConsumptionGet'])->name('consumption-report-get');
        Route::get('consumption-report-post',[PartConsumptionReportController::class,'partConsumptionPost'])->name('consumption-report-post');
        
        Route::get('finance-report-get',[FinancialReportController::class,'financeReportGet'])->name('finance-report-get');
        Route::get('finance-report-post',[FinancialReportController::class,'financeReportPost'])->name('finance-report-post');
    });

});


// Route::get('/register', function () { return view('pages.register'); });
// Route::get('/login-1', function () { return view('pages.login'); });
