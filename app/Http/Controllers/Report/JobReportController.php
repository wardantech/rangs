<?php

namespace App\Http\Controllers\Report;

use Validator;
use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Ticket\Ticket;
use App\Models\Inventory\Category;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class JobReportController extends Controller
{
    //Job Report
    public function jobReportGet(Request $request)
    {

        try{
            $currentDate = Carbon::now('Asia/Dhaka');
            $currentDate=$currentDate->toDateString();
            $formattedCurrentMonth=now()->month;
            $ac=['AC','AIR-CON','VRF'];
            $appliance=['APPLIANCES','HOME APPLIANCE','SMALL APPLIANCES','KITCHEN APPLIANCES','REFRIGERATOR'];
            $av_systems=['AMPLIFIER','AV SYSTEM','CAR AUDIO','CD PLAYER','DVD PLAYER','EXTRA BASS','HEADPHONE','HIFI/DECK','HOME THEATRE','MICROPHONE','RCR','SOUND BAR','SPEAKER','VCR','VIDEO RECORDER','WALKMAN','ALPHA CAMERA','CAMERA','CAMERA PARTS','CYBER SHOT','MEMORY STICK'];
            $camera=['ALPHA CAMERA','CAMERA','CAMERA PARTS','CYBER SHOT','MEMORY STICK'];
            $mobiles=['MOBILE','MOBILE SET GSM','TELEPHONE'];
            $others=['ACCESSORIES','BULB/LAMP','CHARGER FAN','CHARGER LIGHT','COMPUTER','FAN','GENERATOR','GIFT ITEM','LED TV ACCESSORIES','OTHERS','REMOTE','STABILIZER','STAND/TROLLY','UPS/IPS'];
            $professionals=['WLAPTOP','MONITOR','PHOTOCOPIER','PLAY STATION','PRINTER','PROFESSIONAL CAMERA','PROFESSIONAL ITEM','PROFESSIONAL VIDEO REC','PROJECTOR'];
            $water_purifier=['WATER PURIFIER'];
            $ltv=['CTV','LCD TV','LCD/LED TV','LG LED TV','WALTON LED TV','SONY LED TV','SAMSUNG LED TV','RANGS LED TV','PROJECTION TV','PLASMA TV','PHILIPS LED TV'];
            
            $outlets=DB::table('outlets')->where('deleted_at', '=', null)->get();

            $acid=DB::table('categories')->whereIn('name', $ac)->pluck('id');
            $applianceid=DB::table('categories')->whereIn('name', $appliance)->pluck('id');
            $av_systemsid=DB::table('categories')->whereIn('name', $av_systems)->pluck('id');
            $camera_id=DB::table('categories')->whereIn('name', $camera)->pluck('id');
            $mobiles_id=DB::table('categories')->whereIn('name', $mobiles)->pluck('id');
            $others_id=DB::table('categories')->whereIn('name', $others)->pluck('id');
            $professionals_id=DB::table('categories')->whereIn('name', $professionals)->pluck('id');
            $water_purifier_id=DB::table('categories')->whereIn('name', $water_purifier)->pluck('id');
            $ltvid=DB::table('categories')->whereIn('name', $ltv)->pluck('id');

            $categories=DB::table('categories')->pluck('id');
            // dd($acid);
            if(request()->ajax()){

                return DataTables::of($outlets)
 
                        ->addColumn('ltv_category', function ($outlets) use($ltvid, $formattedCurrentMonth) {
                            $category=DB::table('tickets')
                                    ->join('categories','tickets.product_category_id','=','categories.id')
                                    ->select('categories.name as categoryName')
                                    ->where('tickets.outlet_id', '=', $outlets->id)
                                    ->whereIn('tickets.product_category_id', $ltvid)
                                    ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                    ->where('tickets.deleted_at',null)
                                    ->groupBy('tickets.outlet_id')
                                    ->get();
                                    $data='';
                                    foreach ($category as $key => $value) {
                                        $data=$value->categoryName;
                                    }
                                    return $data;                             

                            })  

                            //Received
                        ->addColumn('ltv_received', function ($outlets) use($ltvid, $formattedCurrentMonth) {
                                $received=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $ltvid)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $received;
    
                                })
                                
                        ->addColumn('ac_received', function ($outlets) use($acid, $formattedCurrentMonth) {
                                $received=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $acid)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $received;
    
                                })
                                
                        ->addColumn('appliance_received', function ($outlets) use($applianceid, $formattedCurrentMonth) {
                                $received=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $applianceid)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $received;
    
                                })

                        ->addColumn('av_systems_received', function ($outlets) use($av_systemsid, $formattedCurrentMonth) {
                                $received=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $av_systemsid)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $received;
    
                                })
                        ->addColumn('camera_received', function ($outlets) use($camera_id, $formattedCurrentMonth) {
                                $received=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $camera_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $received;
    
                                })

                        ->addColumn('mobiles_received', function ($outlets) use($mobiles_id, $formattedCurrentMonth) {
                                $received=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $mobiles_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $received;
    
                                })
                        ->addColumn('others_received', function ($outlets) use($others_id, $formattedCurrentMonth) {
                                $received=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $others_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $received;
    
                                })
                        ->addColumn('professionals_received', function ($outlets) use($professionals_id, $formattedCurrentMonth) {
                                $received=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $professionals_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $received;
    
                                })
                                
                        ->addColumn('water_purifier_received', function ($outlets) use($water_purifier_id, $formattedCurrentMonth) {
                                $received=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $water_purifier_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $received;
    
                                })
                        ->addColumn('days_tat_received', function ($outlets) use($water_purifier_id, $currentDate) {
                                    $days_tat_received=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as total_received'))
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $water_purifier_id)
                                        ->whereDate('tickets.created_at','=', $currentDate)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $days_tat_received;
                                })  

                                //Repaired
                        ->addColumn('ltv_repaired', function ($outlets) use ($ltvid, $formattedCurrentMonth){
                            $repaired=DB::table('tickets')
                                    // ->join('jobs', function ($join){
                                    //     $join->on('tickets.id','=','jobs.ticket_id')
                                    //     ->where('jobs.is_started', '=', 1)
                                    //     ->where('jobs.is_ended', '=', 1);
                                    // })
                                    // ->join('categories', function($join) use ($categories){
                                    //     $join->on('tickets.product_category_id','=','categories.id')
                                    //     ->where('categories.id','=', $categories);
                                    // })
                                    ->join('categories','tickets.product_category_id','=','categories.id')
                                    ->select(DB::raw('COUNT(*) as Repaired'))
                                    ->where('tickets.is_started', '=', '1')
                                    ->where('tickets.is_ended', '=', '1')
                                    ->where('tickets.outlet_id', '=', $outlets->id)
                                    ->whereIn('tickets.product_category_id', $ltvid)
                                    ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                    ->where('tickets.deleted_at',null)
                                    ->count();
                                return $repaired;

                            })  

                        ->addColumn('ac_repaired', function ($outlets) use ($acid, $formattedCurrentMonth){
                            $repaired=DB::table('tickets')
                                    ->select(DB::raw('COUNT(*) as Repaired'))
                                    ->where('tickets.is_started', '=', '1')
                                    ->where('tickets.is_ended', '=', '1')
                                    ->where('tickets.outlet_id', '=', $outlets->id)
                                    ->whereIn('tickets.product_category_id', $acid)
                                    ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                    ->where('tickets.deleted_at',null)
                                    ->count();
                                return $repaired;
                            }) 
                        ->addColumn('appliance_repaired', function ($outlets) use ($applianceid, $formattedCurrentMonth){
                            $repaired=DB::table('tickets')
                                    ->select(DB::raw('COUNT(*) as Repaired'))
                                    ->where('tickets.is_started', '=', '1')
                                    ->where('tickets.is_ended', '=', '1')
                                    ->where('tickets.outlet_id', '=', $outlets->id)
                                    ->whereIn('tickets.product_category_id', $applianceid)
                                    ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                    ->where('tickets.deleted_at',null)
                                    ->count();
                                return $repaired;
                            }) 
                        ->addColumn('av_systems_repaired', function ($outlets) use ($av_systemsid, $formattedCurrentMonth){
                            $repaired=DB::table('tickets')
                                    ->select(DB::raw('COUNT(*) as Repaired'))
                                    ->where('tickets.is_started', '=', '1')
                                    ->where('tickets.is_ended', '=', '1')
                                    ->where('tickets.outlet_id', '=', $outlets->id)
                                    ->whereIn('tickets.product_category_id', $av_systemsid)
                                    ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                    ->where('tickets.deleted_at',null)
                                    ->count();
                                return $repaired;
                            }) 
                        ->addColumn('camera_repaired', function ($outlets) use ($camera_id, $formattedCurrentMonth){
                            $repaired=DB::table('tickets')
                                    ->select(DB::raw('COUNT(*) as Repaired'))
                                    ->where('tickets.is_started', '=', '1')
                                    ->where('tickets.is_ended', '=', '1')
                                    ->where('tickets.outlet_id', '=', $outlets->id)
                                    ->whereIn('tickets.product_category_id', $camera_id)
                                    ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                    ->where('tickets.deleted_at',null)
                                    ->count();
                                return $repaired;
                            }) 
                        ->addColumn('mobiles_repaired', function ($outlets) use ($mobiles_id, $formattedCurrentMonth){
                            $repaired=DB::table('tickets')
                                    ->select(DB::raw('COUNT(*) as Repaired'))
                                    ->where('tickets.is_started', '=', '1')
                                    ->where('tickets.is_ended', '=', '1')
                                    ->where('tickets.outlet_id', '=', $outlets->id)
                                    ->whereIn('tickets.product_category_id', $mobiles_id)
                                    ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                    ->where('tickets.deleted_at',null)
                                    ->count();
                                return $repaired;
                            }) 
                        ->addColumn('others_repaired', function ($outlets) use ($others_id, $formattedCurrentMonth){
                            $repaired=DB::table('tickets')
                                    ->select(DB::raw('COUNT(*) as Repaired'))
                                    ->where('tickets.is_started', '=', '1')
                                    ->where('tickets.is_ended', '=', '1')
                                    ->where('tickets.outlet_id', '=', $outlets->id)
                                    ->whereIn('tickets.product_category_id', $others_id)
                                    ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                    ->where('tickets.deleted_at',null)
                                    ->count();
                                return $repaired;
                            })
                        ->addColumn('professionals_repaired', function ($outlets) use ($professionals_id, $formattedCurrentMonth){
                            $repaired=DB::table('tickets')
                                    ->select(DB::raw('COUNT(*) as Repaired'))
                                    ->where('tickets.is_started', '=', '1')
                                    ->where('tickets.is_ended', '=', '1')
                                    ->where('tickets.outlet_id', '=', $outlets->id)
                                    ->whereIn('tickets.product_category_id', $professionals_id)
                                    ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                    ->where('tickets.deleted_at',null)
                                    ->count();
                                return $repaired;
                            }) 
                        ->addColumn('water_purifier_repaired', function ($outlets) use ($water_purifier_id, $formattedCurrentMonth){
                            $repaired=DB::table('tickets')
                                    ->select(DB::raw('COUNT(*) as Repaired'))
                                    ->where('tickets.is_started', '=', '1')
                                    ->where('tickets.is_ended', '=', '1')
                                    ->where('tickets.outlet_id', '=', $outlets->id)
                                    ->whereIn('tickets.product_category_id', $water_purifier_id)
                                    ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                    ->where('tickets.deleted_at',null)
                                    ->count();
                                return $repaired;
                            }) 
                            ->addColumn('days_tat_repaired', function ($outlets) use($water_purifier_id, $currentDate) {
                                $days_tat_repaired=DB::table('tickets')
                                    ->select(DB::raw('COUNT(*) as Repaired'))
                                    ->where('tickets.is_started', '=', '1')
                                    ->where('tickets.is_ended', '=', '1')
                                    ->where('tickets.outlet_id', '=', $outlets->id)
                                    ->whereIn('tickets.product_category_id', $water_purifier_id)
                                    ->whereDate('tickets.created_at','=', $currentDate)
                                    ->where('tickets.deleted_at',null)
                                    ->groupBy('tickets.outlet_id')
                                    ->count();
                                return $days_tat_repaired;
                            })  
                            
                            //Delivered
                            ->addColumn('ltv_delivered', function ($outlets) use ($ltvid, $formattedCurrentMonth){
                                $deliverd=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as Delivered'))
                                        ->where('tickets.is_started', '=', '1')
                                        ->where('tickets.is_ended', '=', '1')
                                        ->where('tickets.is_delivered_by_teamleader', '=', '1')
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $ltvid)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $deliverd;
    
                                })  
                            ->addColumn('ac_delivered', function ($outlets) use ($acid, $formattedCurrentMonth){
                                $deliverd=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as Delivered'))
                                        ->where('tickets.is_started', '=', '1')
                                        ->where('tickets.is_ended', '=', '1')
                                        ->where('tickets.is_delivered_by_teamleader', '=', '1')
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $acid)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $deliverd;
    
                                })
                            ->addColumn('appliance_delivered', function ($outlets) use ($applianceid, $formattedCurrentMonth){
                                $deliverd=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as Delivered'))
                                        ->where('tickets.is_started', '=', '1')
                                        ->where('tickets.is_ended', '=', '1')
                                        ->where('tickets.is_delivered_by_teamleader', '=', '1')
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $applianceid)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $deliverd;
    
                                })
                            ->addColumn('av_systems_delivered', function ($outlets) use ($av_systemsid, $formattedCurrentMonth){
                                $deliverd=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as Delivered'))
                                        ->where('tickets.is_started', '=', '1')
                                        ->where('tickets.is_ended', '=', '1')
                                        ->where('tickets.is_delivered_by_teamleader', '=', '1')
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $av_systemsid)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $deliverd;
    
                                })
                            ->addColumn('camera_delivered', function ($outlets) use ($camera_id, $formattedCurrentMonth){
                                $deliverd=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as Delivered'))
                                        ->where('tickets.is_started', '=', '1')
                                        ->where('tickets.is_ended', '=', '1')
                                        ->where('tickets.is_delivered_by_teamleader', '=', '1')
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $camera_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $deliverd;
    
                                })
                            ->addColumn('mobiles_delivered', function ($outlets) use ($mobiles_id, $formattedCurrentMonth){
                                $deliverd=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as Delivered'))
                                        ->where('tickets.is_started', '=', '1')
                                        ->where('tickets.is_ended', '=', '1')
                                        ->where('tickets.is_delivered_by_teamleader', '=', '1')
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $mobiles_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $deliverd;
    
                                })
                            ->addColumn('others_delivered', function ($outlets) use ($others_id, $formattedCurrentMonth){
                                $deliverd=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as Delivered'))
                                        ->where('tickets.is_started', '=', '1')
                                        ->where('tickets.is_ended', '=', '1')
                                        ->where('tickets.is_delivered_by_teamleader', '=', '1')
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $others_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $deliverd;
    
                                })
                            ->addColumn('professionals_delivered', function ($outlets) use ($professionals_id, $formattedCurrentMonth){
                                $deliverd=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as Delivered'))
                                        ->where('tickets.is_started', '=', '1')
                                        ->where('tickets.is_ended', '=', '1')
                                        ->where('tickets.is_delivered_by_teamleader', '=', '1')
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $professionals_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $deliverd;
    
                                })
                            ->addColumn('water_purifier_delivered', function ($outlets) use ($water_purifier_id, $formattedCurrentMonth){
                                $deliverd=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as Delivered'))
                                        ->where('tickets.is_started', '=', '1')
                                        ->where('tickets.is_ended', '=', '1')
                                        ->where('tickets.is_delivered_by_teamleader', '=', '1')
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $water_purifier_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $deliverd;
    
                                })
                                ->addColumn('days_tat_delivered', function ($outlets) use($water_purifier_id, $currentDate) {
                                    $days_tat_delivered=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as Delivered'))
                                        ->where('tickets.is_started', '=', '1')
                                        ->where('tickets.is_ended', '=', '1')
                                        ->where('tickets.is_delivered_by_teamleader', '=', '1')
                                        ->where('tickets.outlet_id', '=', $outlets->id)
                                        ->whereIn('tickets.product_category_id', $water_purifier_id)
                                        ->whereDate('tickets.created_at','=', $currentDate)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('tickets.outlet_id')
                                        ->count();
                                    return $days_tat_delivered;
                                }) 
                                
                                //Pending
                                ->addColumn('ltv_pending', function ($outlets) use($ltvid, $formattedCurrentMonth) {
                                    $pending=DB::table('tickets')
                                        ->select(DB::raw('COUNT(jobs.*) as pending'))
                                        ->where('tickets.is_assigned', '=', '1')
                                        ->where('tickets.is_started', '=', '0')
                                        ->where('tickets.is_ended', '=', '0')
                                        ->where('outlet_id', '=', $outlets->id)
                                        ->whereIn('product_category_id', $ltvid)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('outlet_id')
                                        ->count();
                                        return $pending;
                                    })
                                ->addColumn('ac_pending', function ($outlets) use($acid, $formattedCurrentMonth) {
                                    $pending=DB::table('tickets')
                                        ->select(DB::raw('COUNT(jobs.*) as pending'))
                                        ->where('tickets.is_assigned', '=', '1')
                                        ->where('tickets.is_started', '=', '0')
                                        ->where('tickets.is_ended', '=', '0')
                                        ->where('outlet_id', '=', $outlets->id)
                                        ->whereIn('product_category_id', $acid)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('outlet_id')
                                        ->count();
                                        return $pending;
                                    })  
                                ->addColumn('appliance_pending', function ($outlets) use($applianceid, $formattedCurrentMonth) {
                                    $pending=DB::table('tickets')
                                        ->select(DB::raw('COUNT(jobs.*) as pending'))
                                        ->where('tickets.is_assigned', '=', '1')
                                        ->where('tickets.is_started', '=', '0')
                                        ->where('tickets.is_ended', '=', '0')
                                        ->where('outlet_id', '=', $outlets->id)
                                        ->whereIn('product_category_id', $applianceid)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('outlet_id')
                                        ->count();
                                        return $pending;
                                    })  
                                ->addColumn('av_systems_pending', function ($outlets) use($av_systemsid, $formattedCurrentMonth) {
                                    $pending=DB::table('tickets')
                                        ->select(DB::raw('COUNT(jobs.*) as pending'))
                                        ->where('tickets.is_assigned', '=', '1')
                                        ->where('tickets.is_started', '=', '0')
                                        ->where('tickets.is_ended', '=', '0')
                                        ->where('outlet_id', '=', $outlets->id)
                                        ->whereIn('product_category_id', $av_systemsid)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('outlet_id')
                                        ->count();
                                        return $pending;
                                    })  
                                ->addColumn('camera_pending', function ($outlets) use($camera_id, $formattedCurrentMonth) {
                                    $pending=DB::table('tickets')
                                        ->select(DB::raw('COUNT(jobs.*) as pending'))
                                        ->where('tickets.is_assigned', '=', '1')
                                        ->where('tickets.is_started', '=', '0')
                                        ->where('tickets.is_ended', '=', '0')
                                        ->where('outlet_id', '=', $outlets->id)
                                        ->whereIn('product_category_id', $camera_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('outlet_id')
                                        ->count();
                                        return $pending;
                                    })  
                                ->addColumn('mobiles_pending', function ($outlets) use($mobiles_id, $formattedCurrentMonth) {
                                    $pending=DB::table('tickets')
                                        ->select(DB::raw('COUNT(jobs.*) as pending'))
                                        ->where('tickets.is_assigned', '=', '1')
                                        ->where('tickets.is_started', '=', '0')
                                        ->where('tickets.is_ended', '=', '0')
                                        ->where('outlet_id', '=', $outlets->id)
                                        ->whereIn('product_category_id', $mobiles_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('outlet_id')
                                        ->count();
                                        return $pending;
                                    })  
                                ->addColumn('others_pending', function ($outlets) use($others_id, $formattedCurrentMonth) {
                                    $pending=DB::table('tickets')
                                        ->select(DB::raw('COUNT(jobs.*) as pending'))
                                        ->where('tickets.is_assigned', '=', '1')
                                        ->where('tickets.is_started', '=', '0')
                                        ->where('tickets.is_ended', '=', '0')
                                        ->where('outlet_id', '=', $outlets->id)
                                        ->whereIn('product_category_id', $others_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('outlet_id')
                                        ->count();
                                        return $pending;
                                    })  
                                ->addColumn('professionals_pending', function ($outlets) use($professionals_id, $formattedCurrentMonth) {
                                    $pending=DB::table('tickets')
                                        ->select(DB::raw('COUNT(jobs.*) as pending'))
                                        ->where('tickets.is_assigned', '=', '1')
                                        ->where('tickets.is_started', '=', '0')
                                        ->where('tickets.is_ended', '=', '0')
                                        ->where('outlet_id', '=', $outlets->id)
                                        ->whereIn('product_category_id', $professionals_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('outlet_id')
                                        ->count();
                                        return $pending;
                                    })  
                                ->addColumn('water_purifier_pending', function ($outlets) use($water_purifier_id, $formattedCurrentMonth) {
                                    $pending=DB::table('tickets')
                                        ->select(DB::raw('COUNT(jobs.*) as pending'))
                                        ->where('tickets.is_assigned', '=', '1')
                                        ->where('tickets.is_started', '=', '0')
                                        ->where('tickets.is_ended', '=', '0')
                                        ->where('outlet_id', '=', $outlets->id)
                                        ->whereIn('product_category_id', $water_purifier_id)
                                        ->whereMonth('tickets.created_at','=', $formattedCurrentMonth)
                                        ->where('tickets.deleted_at',null)
                                        ->groupBy('outlet_id')
                                        ->count();
                                        return $pending;
                                    })  
                                    
                                    ->addColumn('days_tat_pending', function ($outlets) use($water_purifier_id, $currentDate) {
                                        $days_tat_pending=DB::table('tickets')
                                            ->select(DB::raw('COUNT(*) as pending'))
                                            ->where('tickets.is_assigned', '=', '1')
                                            ->where('tickets.is_started', '=', '0')
                                            ->where('tickets.is_ended', '=', '0')
                                            ->where('tickets.outlet_id', '=', $outlets->id)
                                            ->whereIn('tickets.product_category_id', $water_purifier_id)
                                            ->whereDate('tickets.created_at','=', $currentDate)
                                            ->where('tickets.deleted_at',null)
                                            ->groupBy('tickets.outlet_id')
                                            ->count();
                                        return $days_tat_pending;
                                    })                                 
                                ->addIndexColumn()
                                ->rawColumns(['repaired','delivered','pending'])
                                ->make(true);
            }

            return view ('reports.job.job-report',compact('outlets'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    public function jobReportPost(Request $request)
    {
        try{
            $startDate=Carbon::parse($request->start_date)->format('Y-m-d');
            $endDate=Carbon::parse($request->end_date)->format('Y-m-d');
            $outlets=DB::table('outlets')->where('deleted_at', '=', null)->get();
            $product_category_name='';
            // dd($product_category_name);
            // dd($request->all());
            // $raws=DB::table('tickets')
            // ->where('product_category_id', $request->product_category)
            // ->join('jobs','tickets.id','=','jobs.ticket_id')
            // ->join('outlets','tickets.outlet_id','=','outlets.id')
            // ->join('categories','tickets.product_category_id','=','categories.id')
            // ->select('jobs.*','tickets.is_closed_by_teamleader as delivered','categories.name as categoryname','outlets.name as outletname')
            // ->get();
    
            // if(request()->ajax()){
            //     return DataTables::of($raws)
            //             ->addColumn('partmodel', function ($raws) {

            //                     if ($raws->partModel !=null ) {
            //                         $partModel = $raws->partModel->name;
            //                     } else {
            //                         $partModel ='null';
            //                     }
            //                     return $partModel;
            //                     })
                                
            //                 ->addColumn('balance', function ($raws) {  
            //                     $ins = InventoryStock::where('part_id', $raws->id)
            //                                 ->sum('stock_in');
    
            //                     $outs = InventoryStock::where('part_id', $raws->id)
            //                                 ->sum('stock_out');
            //                     $balance=abs($ins - $outs );
            //                     return $balance;
            //                 })                                    
                            
            //                     ->addIndexColumn()
            //                     ->rawColumns(['partmodel','balance'])
            //                     ->make(true);
            // }
            $ticket_info=[];
            foreach ($outlets as $key => $outlet) {
                if(!empty($request->product_category) && !empty($request->start_date) && !empty($request->end_date) )
                {
                    $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                    $received=DB::table('tickets')
                    ->select(DB::raw('COUNT(*) as total_received'))
                    ->where('outlet_id', '=', $outlet->id)
                    ->where('tickets.product_category_id', $request->product_category)
                    ->whereBetween('tickets.created_at',[$startDate, $endDate])
                    ->where('tickets.deleted_at',null)
                    // ->groupBy('tickets.outlet_id')
                    ->count();

                    $repaired=DB::table('tickets')
                                    ->select(DB::raw('COUNT(*) as Repaired'))
                                    ->where('tickets.is_started', '=', '1')
                                    ->where('tickets.is_ended', '=', '1')
                                    ->where('outlet_id', '=', $outlet->id)
                                    ->where('tickets.product_category_id', $request->product_category)
                                    ->whereBetween('tickets.created_at',[$startDate, $endDate])
                                    ->where('tickets.deleted_at',null)
                                    ->count();

                    $delivered=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as Delivered'))
                                        ->where('tickets.is_started', '=', '1')
                                        ->where('tickets.is_ended', '=', '1')
                                        ->where('tickets.is_delivered_by_teamleader', '=', '1')
                                        ->where('outlet_id', '=', $outlet->id)
                                        ->where('tickets.product_category_id', $request->product_category)
                                        ->whereBetween('tickets.created_at',[$startDate, $endDate])
                                        ->where('tickets.deleted_at',null)
                                        // ->groupBy('tickets.outlet_id')
                                        ->count();

                    $pending=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as delivered'))
                                        ->where('tickets.is_assigned', '=', '1')
                                        ->where('tickets.is_started', '=', '0')
                                        ->where('tickets.is_ended', '=', '0')
                                        ->where('outlet_id', '=', $outlet->id)
                                        ->where('tickets.product_category_id', $request->product_category)
                                        ->whereBetween('tickets.created_at',[$startDate, $endDate])
                                        ->where('tickets.deleted_at',null)
                                        // ->groupBy('outlet_id')
                                        ->count();
                    $item['outlet_name'] = $outlet->name;
                    $item['received'] = $received;
                    $item['repaired'] = $repaired;
                    $item['delivered'] = $delivered;
                    $item['pending'] = $pending;
                    array_push($ticket_info, $item);    
                }
                else if(!empty($request->start_date) && !empty($request->end_date))
                {

                    // $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                    $received=DB::table('tickets')
                    ->select(DB::raw('COUNT(*) as total_received'))
                    ->where('outlet_id', '=', $outlet->id)
                    ->whereBetween('tickets.created_at',[$startDate, $endDate])
                    ->where('tickets.deleted_at',null)
                    ->groupBy('tickets.outlet_id')
                    ->count();
                    
                    $repaired=DB::table('tickets')
                    ->select(DB::raw('COUNT(*) as Repaired'))
                    ->where('tickets.is_started', '=', '1')
                    ->where('tickets.is_ended', '=', '1')
                    ->where('outlet_id', '=', $outlet->id)
                    ->whereBetween('tickets.created_at',[$startDate, $endDate])
                    ->where('tickets.deleted_at',null)
                    ->count();

                    $delivered=DB::table('tickets')
                        ->select(DB::raw('COUNT(*) as Delivered'))
                        ->where('tickets.is_started', '=', '1')
                        ->where('tickets.is_ended', '=', '1')
                        ->where('tickets.is_delivered_by_teamleader', '=', '1')
                        ->where('outlet_id', '=', $outlet->id)
                        ->whereBetween('tickets.created_at',[$startDate, $endDate])
                        ->where('tickets.deleted_at',null)
                        // ->groupBy('tickets.outlet_id')
                        ->count();

                    $pending=DB::table('tickets')
                        ->select(DB::raw('COUNT(*) as delivered'))
                        ->where('tickets.is_assigned', '=', '1')
                        ->where('tickets.is_started', '=', '0')
                        ->where('tickets.is_ended', '=', '0')
                        ->where('outlet_id', '=', $outlet->id)
                        ->whereBetween('tickets.created_at',[$startDate, $endDate])
                        ->where('tickets.deleted_at',null)
                        // ->groupBy('outlet_id')
                        ->count();
                    $item['outlet_name'] = $outlet->name;
                    $item['received'] = $received;
                    $item['repaired'] = $repaired;
                    $item['delivered'] = $delivered;
                    $item['pending'] = $pending;
                    array_push($ticket_info, $item); 

                }
                else if(!empty($request->product_category))
                {
                    $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                    $received=DB::table('tickets')
                    ->select(DB::raw('COUNT(*) as total_received'))
                    ->where('outlet_id', '=', $outlet->id)
                    ->where('tickets.product_category_id', $request->product_category)
                    ->where('tickets.deleted_at',null)
                    // ->groupBy('tickets.outlet_id')
                    ->count();

                    $repaired=DB::table('tickets')
                                    ->select(DB::raw('COUNT(*) as Repaired'))
                                    ->where('tickets.is_started', '=', '1')
                                    ->where('tickets.is_ended', '=', '1')
                                    ->where('outlet_id', '=', $outlet->id)
                                    ->where('tickets.product_category_id', $request->product_category)
                                    ->where('tickets.deleted_at',null)
                                    ->count();

                    $delivered=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as Delivered'))
                                        ->where('tickets.is_started', '=', '1')
                                        ->where('tickets.is_ended', '=', '1')
                                        ->where('tickets.is_delivered_by_teamleader', '=', '1')
                                        ->where('outlet_id', '=', $outlet->id)
                                        ->where('tickets.product_category_id', $request->product_category)
                                        ->where('tickets.deleted_at',null)
                                        // ->groupBy('tickets.outlet_id')
                                        ->count();

                    $pending=DB::table('tickets')
                                        ->select(DB::raw('COUNT(*) as delivered'))
                                        ->where('tickets.is_assigned', '=', '1')
                                        ->where('tickets.is_started', '=', '0')
                                        ->where('tickets.is_ended', '=', '0')
                                        ->where('outlet_id', '=', $outlet->id)
                                        ->where('tickets.product_category_id', $request->product_category)
                                        ->where('tickets.deleted_at',null)
                                        // ->groupBy('outlet_id')
                                        ->count();
                    $item['outlet_name'] = $outlet->name;
                    $item['received'] = $received;
                    $item['repaired'] = $repaired;
                    $item['delivered'] = $delivered;
                    $item['pending'] = $pending;
                    array_push($ticket_info, $item);  
                }
                else
                {
                    $currentDate = Carbon::now('Asia/Dhaka');
                    $formattedCurrentDate=$currentDate->toDateString();
                    $received=DB::table('tickets')
                    ->select(DB::raw('COUNT(*) as total_received'))
                    ->where('outlet_id', '=', $outlet->id)
                    ->whereDate('tickets.created_at', $formattedCurrentDate)
                    ->where('tickets.deleted_at',null)
                    ->groupBy('tickets.outlet_id')
                    ->count();  

                    $repaired=DB::table('tickets')
                    ->select(DB::raw('COUNT(*) as Repaired'))
                    ->where('tickets.is_started', '=', '1')
                    ->where('tickets.is_ended', '=', '1')
                    ->where('outlet_id', '=', $outlet->id)
                    ->whereDate('tickets.created_at', $formattedCurrentDate)
                    ->where('tickets.deleted_at',null)
                    ->count();

                    $delivered=DB::table('tickets')
                    ->select(DB::raw('COUNT(*) as Delivered'))
                    ->where('tickets.is_started', '=', '1')
                    ->where('tickets.is_ended', '=', '1')
                    ->where('tickets.is_delivered_by_teamleader', '=', '1')
                    ->where('outlet_id', '=', $outlet->id)
                    ->whereDate('tickets.created_at', $formattedCurrentDate)
                    ->where('tickets.deleted_at',null)
                    // ->groupBy('tickets.outlet_id')
                    ->count();

                    $pending=DB::table('tickets')
                    ->select(DB::raw('COUNT(jobs.*) as delivered'))
                    ->where('tickets.is_assigned', '=', '1')
                    ->where('tickets.is_started', '=', '0')
                    ->where('tickets.is_ended', '=', '0')
                    ->where('outlet_id', '=', $outlet->id)
                    ->whereDate('tickets.created_at', $formattedCurrentDate)
                    ->where('tickets.deleted_at',null)
                    // ->groupBy('outlet_id')
                    ->count();

                    $item['outlet_name'] = $outlet->name;
                    $item['received'] = $received;
                    $item['repaired'] = $repaired;
                    $item['delivered'] = $delivered;
                    $item['pending'] = $pending;
                    array_push($ticket_info, $item);  
                }
            }


            return view ('reports.job.job-report-filter', compact('ticket_info','product_category_name'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

        
    }
    
}
