<?php

namespace App\Http\Controllers\Report;

use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class FinancialReportController extends Controller
{
    public function financeReportGet()
    {
        try {
            $currentDate = Carbon::now('Asia/Dhaka');
            $formattedCurrentDate=$currentDate->toDateString();
            $formattedCurrentMonth=$currentDate->month;
            
            $outlets=DB::table('outlets')->where('deleted_at', '=', null)->orderBy('name', 'ASC')->get();
            if(request()->ajax()){
                return DataTables::of($outlets)
 
                        ->addColumn('collection', function ($outlets) use ($formattedCurrentMonth) {
                            $collection=DB::table('revenues')
                                    ->where('revenues.outlet_id', '=', $outlets->id)
                                    ->whereMonth('revenues.created_at','=', $formattedCurrentMonth)
                                    ->where('revenues.deleted_at',null)
                                    ->sum('revenues.amount');
                                    return $collection;                             

                            })  
                            ->addColumn('days_total_collection', function ($outlets) use ($formattedCurrentDate) {
                                $days_total=DB::table('revenues')
                                        // ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('revenues.outlet_id', '=', $outlets->id)
                                        ->whereDate('revenues.created_at','=', $formattedCurrentDate)
                                        ->where('revenues.deleted_at',null)
                                        ->sum('revenues.amount');
                                        return $days_total;                             
    
                                }) 
                            ->addColumn('opening', function ($outlets) use ($formattedCurrentMonth) {
                                $opening=DB::table('revenues')
                                        // ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('revenues.outlet_id', '=', $outlets->id)
                                        ->whereMonth('revenues.created_at','=', $formattedCurrentMonth)
                                        ->where('revenues.deleted_at',null)
                                        ->sum('revenues.amount');
                                        return $opening;                             
    
                                })
                            ->addColumn('days_total_deposit', function ($outlets) use ($formattedCurrentDate) {
                                $days_total=DB::table('deposits')
                                        // ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('deposits.outlet_id', '=', $outlets->id)
                                        ->whereDate('deposits.created_at','=', $formattedCurrentDate)
                                        ->where('deposits.deleted_at',null)
                                        ->sum('deposits.amount');
                                        return $days_total;                             
    
                                })   
                            ->addColumn('deposit', function ($outlets) use ($formattedCurrentMonth) {
                                $deposit=DB::table('deposits')
                                        // ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('deposits.outlet_id', '=', $outlets->id)
                                        ->whereMonth('deposits.created_at','=', $formattedCurrentMonth)
                                        ->where('deposits.deleted_at',null)
                                        ->sum('deposits.amount');
                                        return $deposit;                             

                                })
                            ->addColumn('balance', function ($outlets) use ($formattedCurrentMonth) {
                                $collection=DB::table('revenues')
                                        ->where('revenues.outlet_id', '=', $outlets->id)
                                        ->whereMonth('revenues.created_at','=', $formattedCurrentMonth)
                                        ->where('revenues.deleted_at',null)
                                        ->sum('revenues.amount');

                                $deposit=DB::table('deposits')
                                        // ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('deposits.outlet_id', '=', $outlets->id)
                                        ->whereMonth('deposits.created_at','=', $formattedCurrentMonth)
                                        ->where('deposits.deleted_at',null)
                                        ->sum('deposits.amount');

                                $balance=$collection-$deposit;
                                
                                return $balance;

                                })  
                                ->addIndexColumn()
                                ->rawColumns(['collection','deposit'])
                                ->make(true);
            }
            return view ('reports.financial.financial-report',compact('formattedCurrentDate'));
        }catch(\Exception $e){
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function financeReportPost(Request $request)
    {
        $this->validate($request, [
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        try {
            $currentDate = Carbon::now('Asia/Dhaka');
            $formattedCurrentDate=$currentDate->toDateString();

            $startDate=Carbon::parse($request->start_date)->format('Y-m-d');
            $endDate=Carbon::parse($request->end_date)->format('Y-m-d');



            if (!empty($request->start_date) && !empty($request->end_date)) {
                $outlets=DB::table('outlets')->where('deleted_at', '=', null)->orderBy('name', 'ASC')->get();
                $financeInfo=[];
                foreach ($outlets as $key => $outlet) {

                    $revenue_collection=DB::table('revenues')
                            ->where('revenues.outlet_id', '=', $outlet->id)
                            ->whereBetween('revenues.created_at',[$startDate, $endDate])
                            ->where('revenues.deleted_at',null)
                            ->sum('revenues.amount'); 

                    $days_total_revenue=DB::table('revenues')
                            ->where('revenues.outlet_id', '=', $outlet->id)
                            ->whereDate('revenues.created_at','=', $formattedCurrentDate)
                            ->where('revenues.deleted_at',null)
                            ->sum('revenues.amount');

                    $days_total_deposit=DB::table('deposits')
                            ->where('deposits.outlet_id', '=', $outlet->id)
                            ->whereDate('deposits.created_at','=', $formattedCurrentDate)
                            ->where('deposits.deleted_at',null)
                            ->sum('deposits.amount');
                            
                    $deposit=DB::table('deposits')
                            ->where('deposits.outlet_id', '=', $outlet->id)
                            ->whereBetween('deposits.created_at',[$startDate, $endDate])
                            ->where('deposits.deleted_at',null)
                            ->sum('deposits.amount'); 

                    $balance=$revenue_collection-$deposit;

                    $item['outlet_name'] = $outlet->name;
                    $item['days_total_revenue'] = $days_total_revenue;
                    $item['revenue_collection'] = $revenue_collection;
                    $item['opening_bf'] = $revenue_collection;
                    $item['days_total_deposit'] = $days_total_deposit;
                    $item['deposit'] = $deposit;
                    $item['balance'] = $balance;
                    array_push($financeInfo, $item); 

                }
                return view ('reports.financial.financial-report-filter',compact('financeInfo','startDate','endDate'));
            }
            else
            {
                return view ('reports.financial.financial-report');
            }
            return view ('reports.financial.financial-report');
        }catch(\Exception $e){
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
