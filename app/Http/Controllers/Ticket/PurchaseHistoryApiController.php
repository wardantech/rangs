<?php

namespace App\Http\Controllers\Ticket;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Fault;
use App\Models\Customer\Customer;
use App\Models\Inventory\Category;
use App\Models\Product\BrandModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Ticket\PurchaseHistory;
use App\Models\ProductPurchase\Purchase;
use Illuminate\Support\Facades\DB;

class PurchaseHistoryApiController extends Controller
{
    public function purchaseHistoryGet(){
        $purchase_info = [];
        $purchases = [];
        return view('ticket.purchaseHistory.purchase_history_get',compact('purchase_info','purchases'));
    }

    public function purchaseHistoryPostOld(Request $request)
    {
        try{
        $search = $request->all();
        $faults = Fault::where('status', 1)->select('id', 'name', 'status')->get();

        if($request->products_serial_number != null || $request->invoice_number != null || $request->customer_phone_number != null || $request->customer_name != null)
        {
            // Initialize an empty query builder
            $purchaseQuery = Purchase::query();

            $purchase_info=[];

            //By Product Serial
            if ($request->products_serial_number != null) {
                $purchaseQuery->where('product_serial', 'LIKE', "%$request->products_serial_number%");
            }

            //By Invoice Number
            if ($request->invoice_number) {
                $purchaseQuery->where('invoice_number', 'LIKE', "%$request->invoice_number%");
            }

            //By Customer Phone Number
            if ($request->customer_phone_number) {
                $customer = Customer::where('mobile', $request->customer_phone_number)->first();

                if ($customer) {
                    $purchaseQuery->where('customer_id', $customer->id);
                }
            }

            //By Customer Phone Number
            if ($request->customer_name) {
                $customer = Customer::where('name', 'LIKE', "%$request->customer_name%")->first();

                if ($customer) {
                    $purchaseQuery->where('customer_id', $customer->id);
                }
            }

            // Check if any conditions are applied to the query
            
            if ($purchaseQuery->getQuery()->wheres) {
                // Execute the query and get the results
                $purchases = $purchaseQuery->with('ticket')->get();

                // Check if the resulting collection is not empty
                if ($purchases->isNotEmpty()) {
                    // The $purchaseQuery has data that matches the conditions
                    foreach ($purchases as $purchase) {
                        $item = [
                            'purchase_id' => $purchase->id,
                            'invoice_number' => $purchase->invoice_number,
                            'customer_name' => $purchase->customer->name,
                            'customer_address' => $purchase->customer->address,
                            'customer_mobile' => $purchase->customer->mobile,
                            'purchase_date' => $purchase->purchase_date->format('m/d/Y'),
                            'product_serial' => $purchase->product_serial,
                            'product_name' => $purchase->category->name,
                            'product_brand_name' => $purchase->brand->name,
                            'product_model_name' => $purchase->modelname->model_name,
                            'point_of_purchase' => $purchase->outlet->name,
                        ];
                
                        array_push($purchase_info, $item);
                    }

                    return view('ticket.purchaseHistory.purchase_history_get', compact('purchase_info', 'faults', 'purchases'));
                } else {
                    // No data found based on the conditions
                    return redirect()->back()->with('error', __('Sorry! No Data Found'));
                }
            } else {
                // No conditions applied to the query
                return redirect()->back()->with('error', __('No search conditions matched'));
            }

            //If local system database can't serve data it will call API to fetch data
            if (empty($purchase_info)) {
                # code...

                    // Build the API URL using the provided parameters
                $url = 'http://202.84.32.124:8081/api/CustomerInfo/getcustomerInfo';

                $queryParameters = [
                    'custname' => $request->customer_name,
                    'mobileNumber' => $request->customer_phone_number,
                    'invoice' => $request->invoice_number,
                    'invoice' => $request->invoice_number,
                    'serialNumber' => $request->products_serial_number,
                ];

                // Use Http::get for a GET request with query parameters
                $response = Http::get($url, $queryParameters);
                $statusCode = $response->status();

                if ($statusCode >= 200 && $statusCode < 300) {
                    // Success
                    $purchaseData= json_decode($response);
                    if ($purchaseData != null) {
                        foreach ($purchaseData as $key => $value) {
                            if ($value == null ) {
                                return redirect()->back()->with('error', __('Sorry ! No Data Found'));
                            }
                            if ($value->CustomerMobile == null ) {
                                return redirect()->back()->with('error', __('Sorry ! Unavailable Customer Mobile Number'));
                            }
                            if ($value->CustomerName == null ) {
                                return redirect()->back()->with('error', __('Sorry ! Unavailable Customer Name'));
                            }
                               // Warranty Date Creation
                            $general_warranty_date = Carbon::parse($value->PurchaseDate)->addYears($value->GeneralPartsWarranty);
                            $special_warranty_date = Carbon::parse($value->PurchaseDate)->addYears($value->SpecialPartsWarranty);
                            $service_warranty_date = Carbon::parse($value->PurchaseDate)->addYears($value->ServiceWarranty);

                            $getCustomer = Customer::where('mobile',$value->CustomerMobile)->first();
                            if ($getCustomer==null) {
                                $customer = Customer::create([
                                    'name' => $value->CustomerName,
                                    'mobile' => $value->CustomerMobile,
                                    'address' => $value->CustomerAddress,
                                ]);
                            }

                            $getCategory = Category::where('name', $value->Category)->first();
                            if ($getCategory==null) {
                                $category = Category::create([
                                    'name' => $value->Category,
                                ]);
                            }

                            $getBrand = Brand::where('name',$value->GroupName)->first();
                            if ($getBrand==null) {
                                $brand = Brand::create([
                                    'name' => $value->GroupName,
                                    'product_category_id'=>$getCategory ? $getCategory->id : $category->id,
                                    'code' => 0
                                ]);
                            }

                            $getBrandmodel=BrandModel::where('model_name',$value->ModelName)->first();
                            if ($getBrandmodel==null) {
                                $brandmodel = BrandModel::create([
                                    'model_name' => $value->ModelName,
                                    'product_category_id' => $getCategory ? $getCategory->id : $category->id,
                                    'brand_id' => $getBrand ? $getBrand->id : $brand->id,
                                ]);
                            }

                            $getOutlets = Outlet::where('name',$value->DeliveryFrom)->first();
                            if ($getOutlets==null) {
                                $outlets = Outlet::create([
                                    'name' => $value->DeliveryFrom,
                                    'code' => 0,
                                    'address' => "",
                                    'outlet_owner_name' => "",
                                    'market' => "",
                                    'mobile' => "",
                                    'outlet_owner_address' => "",
                                ]);
                            }

                            $purchase=Purchase::create([
                                'customer_id' => $getCustomer ? $getCustomer->id : $customer->id,
                                'product_category_id' =>  $getCategory ? $getCategory->id : $category->id,
                                'brand_id' =>  $getBrand ? $getBrand->id : $brand->id,
                                'brand_model_id' =>  $getBrandmodel ? $getBrandmodel->id : $brandmodel->id,
                                'product_serial' => $value->ProductSerial,
                                'purchase_date'=>$value->PurchaseDate,
                                'invoice_number' => $value->InvoiceNo,
                                'outlet_id'=> $getOutlets ? $getOutlets->id : $outlets->id,
                                'general_warranty_date'=>$general_warranty_date,
                                'special_warranty_date' => $special_warranty_date,
                                'service_warranty_date' => $service_warranty_date,
                                'created_by' => Auth::id(),
                            ]);
                        }
                        // After successfull storing purchase info in database, desired data is showing from the database
                        //By Product Serial
                        if ($request->products_serial_number != null) {
                            $purchaseQuery->where('product_serial', 'LIKE', "%$request->products_serial_number%");
                        }

                        //By Invoice Number
                        if ($request->invoice_number) {
                            $purchaseQuery->where('invoice_number', 'LIKE', "%$request->invoice_number%");
                        }

                        //By Customer Phone Number
                        if ($request->customer_phone_number) {
                            $customer = Customer::where('mobile', $request->customer_phone_number)->first();

                            if ($customer) {
                                $purchaseQuery->where('customer_id', $customer->id);
                            }
                        }
                        
                        //By Customer Phone Number
                        if ($request->customer_name) {
                            $customer = Customer::where('name', 'LIKE', "%$request->customer_name%")->first();

                            if ($customer) {
                                $purchaseQuery->where('customer_id', $customer->id);
                            }
                        }

                        // Check if any conditions are applied to the query
                        
                        if ($purchaseQuery->getQuery()->wheres) {
                            // Execute the query and get the results
                            $purchases = $purchaseQuery->with('ticket')->get();

                            // Check if the resulting collection is not empty
                            if ($purchases->isNotEmpty()) {
                                // The $purchaseQuery has data that matches the conditions
                                foreach ($purchases as $purchase) {
                                    $item = [
                                        'purchase_id' => $purchase->id,
                                        'invoice_number' => $purchase->invoice_number,
                                        'customer_name' => $purchase->customer->name,
                                        'customer_address' => $purchase->customer->address,
                                        'customer_mobile' => $purchase->customer->mobile,
                                        'purchase_date' => $purchase->purchase_date->format('m/d/Y'),
                                        'product_serial' => $purchase->product_serial,
                                        'product_name' => $purchase->category->name,
                                        'product_brand_name' => $purchase->brand->name,
                                        'product_model_name' => $purchase->modelname->model_name,
                                        'point_of_purchase' => $purchase->outlet->name,
                                    ];
                            
                                    array_push($purchase_info, $item);
                                }

                                return view('ticket.purchaseHistory.purchase_history_get', compact('purchase_info', 'faults', 'purchases'));
                            } else {
                                // No data found based on the conditions
                                return redirect()->back()->with('error', __('Sorry! No Data Found'));
                            }
                        } else {
                            // No conditions applied to the query
                            return redirect()->back()->with('error', __('No search conditions matched'));
                        }
                    }
                } else {
                    // Handle other status codes
                    $errorData = $response->json();
                        
                    return redirect()->back()->with('error', $errorData['Message']);
                }
            }
            //If local system database can't serve data it will call API to fetch data
            // if (empty($purchase_info)) {
            //     //API Data
            //     $datas = "username=IT_Service_App&password=IT@Service@2022&grant_type=password";
            //     $url="http://dms.sonyrangs.com/token";

            //     $ch = curl_init();
            //     curl_setopt($ch, CURLOPT_URL, $url);
            //     curl_setopt($ch, CURLOPT_POST, 1);
            //     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            //     curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
            //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //     $result = curl_exec($ch);
            //     $access_token=json_decode($result);
            //     if(empty($access_token)){
            //         return redirect()->back()->with('error', __("Whoops! Sorry... API stopped working"));
            //     }
            //     // return $access_token;
            //     if($access_token !=null){
            //         $bearer_token="Authorization: Bearer ".$access_token->access_token;

            //         $productsl=0;

            //         if (!empty($request->products_serial_number)) {
            //             $productsl=$request->products_serial_number;
            //         }
            //         $mobile=0;
            //         if (!empty($request->customer_phone_number)) {
            //             $mobile=$request->customer_phone_number;
            //         }

            //         $url="http://dms.sonyrangs.com/api/CustomerSalesHistory?ProductSL=$productsl&Mobile=$mobile";

            //         $ch = curl_init();
            //         curl_setopt($ch, CURLOPT_URL, $url);
            //         curl_setopt($ch, CURLOPT_HTTPGET, 1);
            //         curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            //             'Content-Type: application/json',
            //             $bearer_token
            //         ));
            //         // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //         $result = curl_exec($ch);
            //         $purchaseData=json_decode($result);
            //         // return $purchaseData;
            //         if(empty($purchaseData)){
            //             return redirect()->back()->with('error', __('Sorry ! No Data Found'));
            //         }

            //         if ($purchaseData != null) {
            //             foreach ($purchaseData as $key => $value) {
            //                 // Warranty Date Creation
            //                 if ($value == null ) {
            //                     return redirect()->back()->with('error', __('Sorry ! No Data Found'));
            //                 }
            //                 if ($value->CustomerMobile == null ) {
            //                     return redirect()->back()->with('error', __('Sorry ! Unavailable Customer Mobile Number'));
            //                 }
            //                 if ($value->CustomerName == null ) {
            //                     return redirect()->back()->with('error', __('Sorry ! Unavailable Customer Name'));
            //                 }
            //                 $general_warranty_date = Carbon::parse($value->PurchaseDate)->addYears($value->GeneralPartsWarranty);
            //                 $special_warranty_date = Carbon::parse($value->PurchaseDate)->addYears($value->SpecialPartsWarranty);
            //                 $service_warranty_date = Carbon::parse($value->PurchaseDate)->addYears($value->ServiceWarranty);

            //                 $getCustomer = Customer::where('mobile',$value->CustomerMobile)->first();
            //                 if ($getCustomer==null) {
            //                     $customer = Customer::create([
            //                         'name' => $value->CustomerName,
            //                         'mobile' => $value->CustomerMobile,
            //                         'address' => $value->CustomerAddress,
            //                     ]);
            //                 }

            //                 $getCategory = Category::where('name', $value->Category)->first();
            //                 if ($getCategory==null) {
            //                     $category = Category::create([
            //                         'name' => $value->Category,
            //                     ]);
            //                 }

            //                 $getBrand = Brand::where('name',$value->GroupName)->first();
            //                 if ($getBrand==null) {
            //                     $brand = Brand::create([
            //                         'name' => $value->GroupName,
            //                         'product_category_id'=>$getCategory ? $getCategory->id : $category->id,
            //                         'code' => 0
            //                     ]);
            //                 }

            //                 $getBrandmodel=BrandModel::where('model_name',$value->ModelName)->first();
            //                 if ($getBrandmodel==null) {
            //                     $brandmodel = BrandModel::create([
            //                         'model_name' => $value->ModelName,
            //                         'product_category_id' => $getCategory ? $getCategory->id : $category->id,
            //                         'brand_id' => $getBrand ? $getBrand->id : $brand->id,
            //                     ]);
            //                 }

            //                 $getOutlets = Outlet::where('name',$value->DeliveryFrom)->first();
            //                 if ($getOutlets==null) {
            //                     $outlets = Outlet::create([
            //                         'name' => $value->DeliveryFrom,
            //                         'code' => 0,
            //                         'address' => "",
            //                         'outlet_owner_name' => "",
            //                         'market' => "",
            //                         'mobile' => "",
            //                         'outlet_owner_address' => "",
            //                     ]);
            //                 }

            //                 $purchase=Purchase::create([
            //                     'customer_id' => $getCustomer ? $getCustomer->id : $customer->id,
            //                     'product_category_id' =>  $getCategory ? $getCategory->id : $category->id,
            //                     'brand_id' =>  $getBrand ? $getBrand->id : $brand->id,
            //                     'brand_model_id' =>  $getBrandmodel ? $getBrandmodel->id : $brandmodel->id,
            //                     'product_serial' => $value->ProductSerial,
            //                     'purchase_date'=>$value->PurchaseDate,
            //                     'outlet_id'=> $getOutlets ? $getOutlets->id : $outlets->id,
            //                     'general_warranty_date'=>$general_warranty_date,
            //                     'special_warranty_date' => $special_warranty_date,
            //                     'service_warranty_date' => $service_warranty_date,
            //                     'created_by' => Auth::id(),
            //                 ]);
            //             }
            //             // After successfull storing, expected data is showing from local DB

            //             //By Product Serial
            //             $purchase_info=[];
            //             if ($request->products_serial_number != null) {
            //                 $purchases = Purchase::with('ticket')
            //                 ->where('product_serial', 'LIKE', "%$request->products_serial_number%")
            //                 ->get();
            //                 // $purchase_info=[];
            //                 if ($purchases != null) {
            //                     foreach ($purchases as $key => $purchase) {
            //                         $item['purchase_id'] = $purchase->id;
            //                         $item['invoice_number'] = $purchase->invoice_number;
            //                         $item['customer_name'] = $purchase->customer->name;
            //                         $item['customer_address'] = $purchase->customer->address;
            //                         $item['customer_mobile'] = $purchase->customer->mobile;
            //                         $item['customer_name'] = $purchase->customer->name;
            //                         $item['purchase_date'] = $purchase->purchase_date;
            //                         $item['product_serial'] = $purchase->product_serial;
            //                         $item['product_name'] = $purchase->category->name;
            //                         $item['product_brand_name'] = $purchase->brand->name;
            //                         $item['product_model_name'] = $purchase->modelname->model_name;
            //                         $item['point_of_purchase'] = $purchase->outlet->name;
            //                         $tickets = $purchase->ticket;

            //                         array_push($purchase_info, $item);
            //                     }

            //                     return view('ticket.purchaseHistory.purchase_history_get',compact('purchase_info','faults','purchases'));
            //                 } else {
            //                     return redirect()->back()->with('error', __('Sorry ! No Data Found'));
            //                 }
            //             }
            //             //By Phone Number
            //             if ($request->customer_phone_number != null) {
            //                 $customer=Customer::where('mobile', 'LIKE', "%$request->customer_phone_number%")->first();
            //                 if($customer!=null){
            //                     // $purchase_info=[];
            //                     $purchases=Purchase::with('ticket')->where('customer_id', $customer->id)
            //                     ->get();
            //                     if ($purchases !=null) {
            //                         foreach ($purchases as $key => $purchase) {
            //                             $item['purchase_id'] = $purchase->id;
            //                             $item['invoice_number'] = $purchase->invoice_number;
            //                             $item['customer_name'] = $purchase->customer->name;
            //                             $item['customer_address'] = $purchase->customer->address;
            //                             $item['customer_mobile'] = $purchase->customer->mobile;
            //                             $item['customer_name'] = $purchase->customer->name;
            //                             $item['purchase_date'] = $purchase->purchase_date;
            //                             $item['product_serial'] = $purchase->product_serial;
            //                             $item['product_name'] = $purchase->category->name;
            //                             $item['product_brand_name'] = $purchase->brand->name;
            //                             $item['product_model_name'] = $purchase->modelname->model_name;
            //                             $item['point_of_purchase'] = $purchase->outlet->name;
            //                             array_push($purchase_info, $item);
            //                         }
            //                         return view('ticket.purchaseHistory.purchase_history_get',compact('purchase_info','faults','purchases'));
            //                     } else {
            //                         return redirect()->back()->with('error', __('Sorry ! No Data Found'));
            //                     }
            //                 }
            //             }
            //             //By Name
            //             if ($request->customer_name != null) {
            //                 $customers = Customer::where('name', 'LIKE', "%$request->customer_name%")->get();
            //                 // $purchase_info = [];
            //                 if ($customers!=null) {
            //                     foreach ($customers as $key => $customer) {
            //                         $purchase = Purchase::with('ticket')->where('customer_id', $customer->id)->first();
            //                         $item['purchase_id'] = $purchase->id;
            //                         $item['invoice_number'] = $purchase->invoice_number;
            //                         $item['customer_name'] = $purchase->customer->name;
            //                         $item['customer_address'] = $purchase->customer->address;
            //                         $item['customer_mobile'] = $purchase->customer->mobile;
            //                         $item['customer_name'] = $purchase->customer->name;
            //                         $item['purchase_date'] = $purchase->purchase_date->format('m/d/Y');
            //                         $item['product_serial'] = $purchase->product_serial;
            //                         $item['product_name'] = $purchase->category->name;
            //                         $item['product_brand_name'] = $purchase->brand->name;
            //                         $item['product_model_name'] = $purchase->modelname->model_name;
            //                         $item['point_of_purchase'] = $purchase->outlet->name;
            //                         array_push($purchase_info, $item);
            //                     }
            //                     // $faults = Fault::where('status', 1)->select('id', 'name', 'status')->get();
            //                     return view('ticket.purchaseHistory.purchase_history_get',compact('purchase_info','faults','purchases'));
            //                 } else {
            //                     return redirect()->back()->with('error', __('Sorry ! No Data Found'));
            //                 }
            //             }
            //         }
            //         else{
            //             return redirect()->back()->with('error', __('Sorry ! No Data Found'));
            //         }
            //     }
            // }
        }
        else{
            return redirect()->back()->with('error', __('Whoops ! Please input data'));
        }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function purchaseHistoryPost(Request $request)
    {
        $faults = Fault::where('status', 1)->select('id', 'name', 'status')->get();
        // Try to fetch data from the local database
        if($request->products_serial_number != null || $request->invoice_number != null || $request->customer_phone_number != null || $request->customer_name != null)
        {
            $purchases = $this->searchLocalDatabase($request);
        } else{
            return redirect()->back()->with('error', __('Sorry ! Minimum One Item Is Required For Searching'));
        }
        // If no data found, fetch from the sales API and store in the database
        if ($purchases->isEmpty()) {
            $this->searchSalesApiAndStoreInDatabase($request);
    
            // Query local database again after storing data from API
            $purchases = $this->searchLocalDatabase($request);
        }
    
        // Check if any conditions are applied to the query
        if ($purchases->isNotEmpty()) {
            $purchase_info = $this->formatPurchaseData($purchases);
    
            return view('ticket.purchaseHistory.purchase_history_get', compact('purchase_info', 'faults', 'purchases'));
        } else {
            return redirect()->back()->with('error', __('No search conditions matched'));
        }
    }
    
    private function searchLocalDatabase($request)
    {

        $purchaseQuery = Purchase::query();
    
        // By Product Serial
        if ($request->products_serial_number) {
            $purchaseQuery->where('product_serial', 'LIKE', "%$request->products_serial_number%");
        }
    
        // By Invoice Number
        if ($request->invoice_number) {
            $purchaseQuery->where('invoice_number', 'LIKE', "%$request->invoice_number%");
        }
    
        // By Customer Phone Number
        if ($request->customer_phone_number) {
            $customer = Customer::where('mobile', $request->customer_phone_number)->first();
    
            if ($customer) {
                $purchaseQuery->where('customer_id', $customer->id);
            }
        }
    
        // By Customer Name
        if ($request->customer_name) {
            $customer = Customer::where('name', 'LIKE', "%$request->customer_name%")->first();
    
            if ($customer) {
                $purchaseQuery->where('customer_id', $customer->id);
            }
        }

        // Check if any conditions are applied
        if ($purchaseQuery->getQuery()->wheres) {
            // Execute the query and get the results
            return $purchaseQuery->get();
        } else {
            // No conditions applied, return an empty result
            return collect();
        }


    }
    
    private function searchSalesApiAndStoreInDatabase($request)
    {
        // Build the API URL using the provided parameters
        $url = 'http://202.84.32.124:8081/api/CustomerInfo/getcustomerInfo';
    
        $queryParameters = [
            'custname' => $request->customer_name,
            'mobileNumber' => $request->customer_phone_number,
            'invoice' => $request->invoice_number,
            'serialNumber' => $request->products_serial_number,
        ];
    
        // Use Http::get for a GET request with query parameters
        $response = Http::get($url, $queryParameters);
    
        $this->handleApiResponse($response);
    }
    
    private function handleApiResponse($response)
    {
        $statusCode = $response->status();
    
        if ($statusCode >= 200 && $statusCode < 300) {
            $purchaseData = json_decode($response);
    
            if ($purchaseData != null) {

                foreach ($purchaseData as $key => $value) {
                    // Try catch Modified by me based on DB:Transaction method
                    try {
                        $this->storePurchaseDataLocally($value);
                    } catch (\Exception $e) {
                        // Handle the exception by redirecting the user back with the error message
                        return redirect()->back()->with('error', $e->getMessage());
                    }
                }
            }
        } else {
            $errorData = $response->json();
            return redirect()->back()->with('error', $errorData['Message']);
        }
    }
    
    private function storePurchaseDataLocally($value)
    {
        // Use a database transaction
        DB::beginTransaction();   // Try catch Modified by me based on DB:Transaction method

        try {
            // Process the data and store it in the local database
            if ($value == null) {
                return redirect()->back()->with('error', __('Sorry ! No Data Found'));
            }
            if ($value->CustomerMobile == null) {
                return redirect()->back()->with('error', __('Sorry ! Unavailable Customer Mobile Number'));
            }
            if ($value->CustomerName == null) {
                return redirect()->back()->with('error', __('Sorry ! Unavailable Customer Name'));
            }

        
            // Warranty Date Creation
            $general_warranty_date = Carbon::parse($value->PurchaseDate)->addYears($value->GeneralPartsWarranty);
            $special_warranty_date = Carbon::parse($value->PurchaseDate)->addYears($value->SpecialPartsWarranty);
            $service_warranty_date = Carbon::parse($value->PurchaseDate)->addYears($value->ServiceWarranty);

            $getCustomer = Customer::firstOrNew(['mobile' => $value->CustomerMobile], [
                'name' => $value->CustomerName,
                'mobile' => $value->CustomerMobile,
                'address' => $value->CustomerAddress,
            ]);
            $getCustomer->save();

            $getCategory = Category::firstOrCreate(['name' => $value->Category]);
            $getBrand = Brand::firstOrCreate(['name' => $value->GroupName], [
                'product_category_id' => $getCategory->id,
                'code' => 0,
            ]);
            $getBrandmodel = BrandModel::firstOrCreate(['model_name' => $value->ModelName], [
                'product_category_id' => $getCategory->id,
                'brand_id' => $getBrand->id,
            ]);
            $getOutlet = Outlet::firstOrCreate(['name' => $value->DeliveryFrom], [
                'code' => 0,
                'address' => "",
                'outlet_owner_name' => "",
                'market' => "",
                'mobile' => "",
                'outlet_owner_address' => "",
            ]);

            Purchase::create([
                'customer_id' => $getCustomer->id,
                'product_category_id' => $getCategory->id,
                'brand_id' => $getBrand->id,
                'brand_model_id' => $getBrandmodel->id,
                'product_serial' => $value->ProductSerial,
                'purchase_date' => $value->PurchaseDate,
                'invoice_number' => $value->InvoiceNo,
                'outlet_id' => $getOutlet->id,
                'general_warranty_date' => $general_warranty_date,
                'special_warranty_date' => $special_warranty_date,
                'service_warranty_date' => $service_warranty_date,
                'created_by' => Auth::id(),
            ]);
            
            // Commit the transaction
            DB::commit();

        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollBack();
            
            // Throw the exception for the parent function to handle
            throw new \Exception($e->getMessage());
        }

    }
    
    private function formatPurchaseData($purchases)
    {

        $purchase_info = [];
    
        if ($purchases->isNotEmpty()) {
            // The $purchaseQuery has data that matches the conditions
            foreach ($purchases as $purchase) {
                $item = [
                    'purchase_id' => $purchase->id,
                    'invoice_number' => $purchase->invoice_number,
                    'customer_name' => $purchase->customer->name,
                    'customer_address' => $purchase->customer->address,
                    'customer_mobile' => $purchase->customer->mobile,
                    'purchase_date' => $purchase->purchase_date->format('m/d/Y'),
                    'product_serial' => $purchase->product_serial,
                    'product_name' => $purchase->category->name,
                    'product_brand_name' => $purchase->brand->name,
                    // 'product_model_name' => $purchase->modelname->model_name,
                    'product_model_name' => $purchase->modelname ? $purchase->modelname->model_name : null,
                    'point_of_purchase' => $purchase->outlet->name,
                ];
    
                array_push($purchase_info, $item);
            }
        }
    
        return $purchase_info;
    }
    
}
