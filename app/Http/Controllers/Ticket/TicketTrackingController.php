<?php

namespace App\Http\Controllers\Ticket;

use Illuminate\Http\Request;
use App\Models\Ticket\Ticket;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TicketTrackingController extends Controller
{
    public function showSearchForm()
    {
        return view('ticket.tracking.search');
    }
    public function search(Request $request)
    {
        // dd($request->all());

        
        try{
            $request->validate([
                'tsl' => 'required|numeric',
            ]);
            // $ticket = Ticket::find($request->tsl);
            $ticket=DB::table('tickets')
            ->join('outlets','tickets.outlet_id','=','outlets.id')
            ->join('purchases','tickets.purchase_id','=','purchases.id')
            ->join('categories','tickets.product_category_id','=','categories.id')
            ->join('brand_models','purchases.brand_model_id', '=', 'brand_models.id')
            ->join('customers','purchases.customer_id', '=', 'customers.id')
            ->join('districts','tickets.district_id', '=','districts.id' )
            ->join('thanas','thanas.id', '=', 'tickets.thana_id')
            ->select('brand_models.model_name as product_name','categories.name as product_category',
            'customers.name as customer_name', 'customers.mobile as customer_mobile', 'districts.name as district','thanas.name as thana',
            'purchases.product_serial as product_serial','tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name',
            'tickets.service_type_id as service_type_id','tickets.status as status','tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending',
            'tickets.is_paused as is_paused','tickets.is_ended as is_ended','tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader',
            'tickets.is_delivered_by_teamleader as is_delivered_by_teamleader','tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed',
            'tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted','tickets.is_rejected as is_rejected','tickets.end_date as end_date')
            ->where('tickets.id', $request->tsl)
            ->where('tickets.deleted_at',null)
            ->first();
            // dd($ticket);
            // return view('ticket.tracking.result',compact($ticket))->with('success', 'Result Loaded successfully');
            return view('ticket.tracking.result', compact('ticket'));
        }catch(\Exception $e){
            DB::rollback();
            $bug = $e->getMessage();
            // dd($bug);
            return redirect()->back()->with('error', $bug);
        }
    }
}
