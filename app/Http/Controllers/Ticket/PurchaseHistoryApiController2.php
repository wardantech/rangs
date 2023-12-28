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

class PurchaseHistoryApiController extends Controller
{
    public function purchaseHistoryGet(){
        $purchase_info = [];
        $purchases = [];
        return view('ticket.purchaseHistory.purchase_history_get',compact('purchase_info','purchases'));
    }
    public function purchaseHistoryPost(Request $request)
    {
        try{
        $search = $request->all();
        $faults = Fault::where('status', 1)->select('id', 'name', 'status')->get();

        if($request->products_serial_number != null || $request->invoice_number != null || $request->coustormer_phone_number != null || $request->coustormer_name != null)
        {
            //By Product Serial
            $purchase_info=[];
            // if ($request->products_serial_number != null) {
            //     $purchases = Purchase::with('ticket')
            //     ->where('product_serial', 'LIKE', "%$request->products_serial_number%")
            //     ->get();

            //     if ($purchases != null) {
            //         foreach ($purchases as $key => $purchase) {
            //             $item['purchase_id'] = $purchase->id;
            //             $item['invoice_number'] = $purchase->invoice_number;
            //             $item['customer_name'] = $purchase->customer->name;
            //             $item['customer_address'] = $purchase->customer->address;
            //             $item['customer_mobile'] = $purchase->customer->mobile;
            //             $item['customer_name'] = $purchase->customer->name;
            //             $item['purchase_date'] = $purchase->purchase_date;
            //             $item['product_serial'] = $purchase->product_serial;
            //             $item['product_name'] = $purchase->category->name;
            //             $item['product_brand_name'] = $purchase->brand->name;
            //             $item['product_model_name'] = $purchase->modelname->model_name;
            //             $item['point_of_purchase'] = $purchase->outlet->name;
            //             $tickets = $purchase->ticket;

            //             array_push($purchase_info, $item);
            //         }
            //         return view('ticket.purchaseHistory.purchase_history_get',compact('purchase_info','faults','purchases'));
            //     } else {
            //         return redirect()->back()->with('error', __('Sorry ! No Data Found'));
            //     }
            // }
            // //By Invoice Number
            // if ($request->invoice_number != null) {
            //     $purchases = Purchase::with('ticket')
            //     ->where('invoice_number', 'LIKE', "%$request->invoice_number%")
            //     ->get();

            //     if ($purchases != null) {
            //         foreach ($purchases as $key => $purchase) {
            //             $item['purchase_id'] = $purchase->id;
            //             $item['invoice_number'] = $purchase->invoice_number;
            //             $item['customer_name'] = $purchase->customer->name;
            //             $item['customer_address'] = $purchase->customer->address;
            //             $item['customer_mobile'] = $purchase->customer->mobile;
            //             $item['customer_name'] = $purchase->customer->name;
            //             $item['purchase_date'] = $purchase->purchase_date;
            //             $item['product_serial'] = $purchase->product_serial;
            //             $item['product_name'] = $purchase->category->name;
            //             $item['product_brand_name'] = $purchase->brand->name;
            //             $item['product_model_name'] = $purchase->modelname->model_name;
            //             $item['point_of_purchase'] = $purchase->outlet->name;
            //             $tickets = $purchase->ticket;

            //             array_push($purchase_info, $item);
            //         }
            //         return view('ticket.purchaseHistory.purchase_history_get',compact('purchase_info','faults','purchases'));
            //     } else {
            //         return redirect()->back()->with('error', __('Sorry ! No Data Found'));
            //     }
            // }
            // //By Phone Number
            // if ($request->coustormer_phone_number != null) {
            //     $customer=Customer::where('mobile', 'LIKE', "%$request->coustormer_phone_number%")->first();
            //     if($customer!=null){

            //         $purchases=Purchase::with('ticket')->where('customer_id', $customer->id)
            //         ->get();
            //         if ($purchases !=null) {
            //             foreach ($purchases as $key => $purchase) {
            //                 $item['purchase_id'] = $purchase->id;
            //                 $item['invoice_number'] = $purchase->invoice_number;
            //                 $item['customer_name'] = $purchase->customer->name;
            //                 $item['customer_address'] = $purchase->customer->address;
            //                 $item['customer_mobile'] = $purchase->customer->mobile;
            //                 $item['customer_name'] = $purchase->customer->name;
            //                 $item['purchase_date'] = $purchase->purchase_date;
            //                 $item['product_serial'] = $purchase->product_serial;
            //                 $item['product_name'] = $purchase->category->name;
            //                 $item['product_brand_name'] = $purchase->brand->name;
            //                 $item['product_model_name'] = $purchase->modelname->model_name;
            //                 $item['point_of_purchase'] = $purchase->outlet->name;
            //                 array_push($purchase_info, $item);
            //             }
            //             return view('ticket.purchaseHistory.purchase_history_get',compact('purchase_info','faults','purchases'));
            //         } else {
            //             return redirect()->back()->with('error', __('Sorry ! No Data Found'));
            //         }
            //     }
            // }
            // //By Name
            // if ($request->coustormer_name != null) {
            //     $customers = Customer::where('name', 'LIKE', "%$request->coustormer_name%")->first();
            //     if($customers == null){
            //         return redirect()->back()->with('error', __('Sorry ! No Data Found'));
            //     }else{
            //         $purchases=Purchase::with('ticket')->where('customer_id', $customers->id)
            //         ->get();
            //     }

            //     // $filters=[];
            //     // if ($customers!=null) {
            //     //     foreach ($customers as $key => $customer) {
            //     //         $p = Purchase::with('ticket')->where('customer_id', $customer->id)->first();
            //     //         array_push($filters, $p);
            //     //     }
            //     // }
            //     // $purchases = array_filter( $filters, function( $v ) { return !is_null( $v ); } );

            //     if ($purchases!=null) {
            //         foreach ($purchases as $key => $purchase) {

            //             $item['purchase_id'] = $purchase->id;
            //             $item['invoice_number'] = $purchase->invoice_number;
            //             $item['customer_name'] = $purchase->customer->name;
            //             $item['customer_address'] = $purchase->customer->address;
            //             $item['customer_mobile'] = $purchase->customer->mobile;
            //             $item['customer_name'] = $purchase->customer->name;
            //             $item['purchase_date'] = $purchase->purchase_date->format('m/d/Y');
            //             $item['product_serial'] = $purchase->product_serial;
            //             $item['product_name'] = $purchase->category->name;
            //             $item['product_brand_name'] = $purchase->brand->name;
            //             $item['product_model_name'] = $purchase->modelname->model_name;
            //             $item['point_of_purchase'] = $purchase->outlet->name;
            //             array_push($purchase_info, $item);
            //         }
            //         return view('ticket.purchaseHistory.purchase_history_get',compact('purchase_info','faults','purchases'));
            //     } else {
            //         return redirect()->back()->with('error', __('Sorry ! No Data Found'));
            //     }
            // }
            //If local system database can't serve data it will call API to fetch data
            if (empty($purchase_info)) {
                //API Data
                $datas = "username=IT_Service_App&password=IT@Service@2022&grant_type=password";
                $url="http://dms.sonyrangs.com/token";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                $access_token=json_decode($result);
                if(empty($access_token)){
                    return redirect()->back()->with('error', __("Whoops! Sorry... API stopped working"));
                }
                // return $access_token;
                if($access_token !=null){
                    $bearer_token="Authorization: Bearer ".$access_token->access_token;

                    $productsl=0;

                    if (!empty($request->products_serial_number)) {
                        $productsl=$request->products_serial_number;
                    }
                    $mobile=0;
                    if (!empty($request->coustormer_phone_number)) {
                        $mobile=$request->coustormer_phone_number;
                    }

                    $url="http://dms.sonyrangs.com/api/CustomerSalesHistory?ProductSL=$productsl&Mobile=$mobile";

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_HTTPGET, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        $bearer_token
                    ));
                    // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);
                    $purchaseData=json_decode($result);
                    dd($purchaseData);
                    // return $purchaseData;
                    if(empty($purchaseData)){
                        return redirect()->back()->with('error', __('Sorry ! No Data Found'));
                    }

                    if ($purchaseData != null) {
                        foreach ($purchaseData as $key => $value) {
                            // Warranty Date Creation
                            if ($value == null ) {
                                return redirect()->back()->with('error', __('Sorry ! No Data Found'));
                            }
                            if ($value->CustomerMobile == null ) {
                                return redirect()->back()->with('error', __('Sorry ! Unavailable Customer Mobile Number'));
                            }
                            if ($value->CustomerName == null ) {
                                return redirect()->back()->with('error', __('Sorry ! Unavailable Customer Name'));
                            }
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
                                'product_serial' => $value->SLNO,
                                'purchase_date'=>$value->PurchaseDate,
                                'outlet_id'=> $getOutlets ? $getOutlets->id : $outlets->id,
                                'general_warranty_date'=>$general_warranty_date,
                                'special_warranty_date' => $special_warranty_date,
                                'service_warranty_date' => $service_warranty_date,
                                'created_by' => Auth::id(),
                            ]);
                        }
                        // After successfull storing, expected data is showing from local DB

                        //By Product Serial
                        $purchase_info=[];
                        if ($request->products_serial_number != null) {
                            $purchases = Purchase::with('ticket')
                            ->where('product_serial', 'LIKE', "%$request->products_serial_number%")
                            ->get();
                            // $purchase_info=[];
                            if ($purchases != null) {
                                foreach ($purchases as $key => $purchase) {
                                    $item['purchase_id'] = $purchase->id;
                                    $item['invoice_number'] = $purchase->invoice_number;
                                    $item['customer_name'] = $purchase->customer->name;
                                    $item['customer_address'] = $purchase->customer->address;
                                    $item['customer_mobile'] = $purchase->customer->mobile;
                                    $item['customer_name'] = $purchase->customer->name;
                                    $item['purchase_date'] = $purchase->purchase_date;
                                    $item['product_serial'] = $purchase->product_serial;
                                    $item['product_name'] = $purchase->category->name;
                                    $item['product_brand_name'] = $purchase->brand->name;
                                    $item['product_model_name'] = $purchase->modelname->model_name;
                                    $item['point_of_purchase'] = $purchase->outlet->name;
                                    $tickets = $purchase->ticket;

                                    array_push($purchase_info, $item);
                                }

                                return view('ticket.purchaseHistory.purchase_history_get',compact('purchase_info','faults','purchases'));
                            } else {
                                return redirect()->back()->with('error', __('Sorry ! No Data Found'));
                            }
                        }
                        //By Phone Number
                        if ($request->coustormer_phone_number != null) {
                            $customer=Customer::where('mobile', 'LIKE', "%$request->coustormer_phone_number%")->first();
                            if($customer!=null){
                                // $purchase_info=[];
                                $purchases=Purchase::with('ticket')->where('customer_id', $customer->id)
                                ->get();
                                if ($purchases !=null) {
                                    foreach ($purchases as $key => $purchase) {
                                        $item['purchase_id'] = $purchase->id;
                                        $item['invoice_number'] = $purchase->invoice_number;
                                        $item['customer_name'] = $purchase->customer->name;
                                        $item['customer_address'] = $purchase->customer->address;
                                        $item['customer_mobile'] = $purchase->customer->mobile;
                                        $item['customer_name'] = $purchase->customer->name;
                                        $item['purchase_date'] = $purchase->purchase_date;
                                        $item['product_serial'] = $purchase->product_serial;
                                        $item['product_name'] = $purchase->category->name;
                                        $item['product_brand_name'] = $purchase->brand->name;
                                        $item['product_model_name'] = $purchase->modelname->model_name;
                                        $item['point_of_purchase'] = $purchase->outlet->name;
                                        array_push($purchase_info, $item);
                                    }
                                    return view('ticket.purchaseHistory.purchase_history_get',compact('purchase_info','faults','purchases'));
                                } else {
                                    return redirect()->back()->with('error', __('Sorry ! No Data Found'));
                                }
                            }
                        }
                        //By Name
                        if ($request->coustormer_name != null) {
                            $customers = Customer::where('name', 'LIKE', "%$request->coustormer_name%")->get();
                            // $purchase_info = [];
                            if ($customers!=null) {
                                foreach ($customers as $key => $customer) {
                                    $purchase = Purchase::with('ticket')->where('customer_id', $customer->id)->first();
                                    $item['purchase_id'] = $purchase->id;
                                    $item['invoice_number'] = $purchase->invoice_number;
                                    $item['customer_name'] = $purchase->customer->name;
                                    $item['customer_address'] = $purchase->customer->address;
                                    $item['customer_mobile'] = $purchase->customer->mobile;
                                    $item['customer_name'] = $purchase->customer->name;
                                    $item['purchase_date'] = $purchase->purchase_date->format('m/d/Y');
                                    $item['product_serial'] = $purchase->product_serial;
                                    $item['product_name'] = $purchase->category->name;
                                    $item['product_brand_name'] = $purchase->brand->name;
                                    $item['product_model_name'] = $purchase->modelname->model_name;
                                    $item['point_of_purchase'] = $purchase->outlet->name;
                                    array_push($purchase_info, $item);
                                }
                                // $faults = Fault::where('status', 1)->select('id', 'name', 'status')->get();
                                return view('ticket.purchaseHistory.purchase_history_get',compact('purchase_info','faults','purchases'));
                            } else {
                                return redirect()->back()->with('error', __('Sorry ! No Data Found'));
                            }
                        }
                    }
                    else{
                        return redirect()->back()->with('error', __('Sorry ! No Data Found'));
                    }
                }
            }
        }
        else{
            return redirect()->back()->with('error', __('Whoops ! Please input data'));
        }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function purchaseinfo_mobile(Request $request)
    {
        $search = $request->input('coustormer_phone_number');
        $customer=Customer::where('mobile', 'LIKE', "%$search%")->first();
        if($customer!=null){
            $purchase_info=[];
            $purchases = Purchase::with('ticket')
                        ->where('product_serial', 'LIKE', "%$search%")
                        ->get();
                        foreach ($purchases as $key => $purchase) {
                            $item['purchase_id'] = $purchase->id;
                            $item['customer_name'] = $purchase->customer->name;
                            $item['customer_address'] = $purchase->customer->address;
                            $item['customer_mobile'] = $purchase->customer->mobile;
                            $item['customer_name'] = $purchase->customer->name;
                            $item['purchase_date'] = $purchase->purchase_date;
                            $item['product_serial'] = $purchase->product_serial;
                            $item['product_name'] = $purchase->category->name;
                            $item['product_brand_name'] = $purchase->brand->name;
                            $item['product_model_name'] = $purchase->modelname->model_name;
                            $item['point_of_purchase'] = $purchase->outlet->name;
                            $tickets = $purchase->ticket;

                            array_push($purchase_info, $item);
                        }

            return response()->json($purchase_info);
        }
        // dd($request->all());
        $datas = "username=IT_Service_App&password=IT@Service@2022&grant_type=password";

        $url="http://dms.sonyrangs.com/token";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $access_token=json_decode($result);

        if($access_token !=null){
            $bearer_token="Authorization: Bearer ".$access_token->access_token;

            $data = [
                'Product_Serial_No' => $request->products_serial_number,
                ' Customer_Phone' => $request->coustormer_phone_number,
            ];

            $url="http://dms.sonyrangs.com/api/CustomerSalesHistory/6038/01687626504";
            // $url="https://dms.sonyrangs.com/api/CustomerSalesHistory/6038/01687626504";
            // $getUrl = $url."?".$data;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                $bearer_token
            ));
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            $purchaseData=json_decode($result);
            dd($purchaseData);
            // return response()->json($purchaseData->PurchaseDate);
        }

        $purchaseHistoryArr = [];
        return view('ticket.purchaseHistory.purchase_history_get',compact('purchaseHistoryArr'));

        // $purchaseQuery = Purchase::query();
    
        // $conditions = [];
    
        // // By Product Serial
        // if ($request->products_serial_number) {
        //     $conditions[] = ['product_serial', 'LIKE', "%$request->products_serial_number%"];
        // }
    
        // // By Invoice Number
        // if ($request->invoice_number) {
        //     $conditions[] = ['invoice_number', 'LIKE', "%$request->invoice_number%"];
        // }
    
        // // By Customer Phone Number
        // if ($request->customer_phone_number) {
        //     $customer = Customer::where('mobile', $request->customer_phone_number)->first();
    
        //     if ($customer) {
        //         $conditions[] = ['customer_id', $customer->id];
        //     }
        // }
    
        // // By Customer Name
        // if ($request->customer_name) {
        //     $customer = Customer::where('name', 'LIKE', "%$request->customer_name%")->first();
    
        //     if ($customer) {
        //         $conditions[] = ['customer_id', $customer->id];
        //     }
        // }
    
        // // Apply conditions to the query
        // $purchaseQuery->where($conditions);
    
        // // Execute the query and get the results
        // return $purchaseQuery->get();
    }
}
