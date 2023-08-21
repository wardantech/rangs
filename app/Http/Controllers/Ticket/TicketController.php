<?php

namespace App\Http\Controllers\Ticket;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Resources\Ticket as TicketResource;
use App\Http\Resources\TicketCollection as TicketResourceCollection;


class TicketController extends Controller
{
    use ResponseTrait;


    protected function totalTicketStatus()
    {
        try {
            $status= DB::table('tickets')
            ->where('deleted_at', NULL)
                ->selectRaw('count(*) as total')
                ->selectRaw("count(case when status = 0 then 1 end) as created")
                ->selectRaw("count(case when status = 6 and is_pending = 1 and is_paused = 0 and is_ended = 0 then 1 end) as pending")
                ->selectRaw("count(case when status = 9 and is_reopened = 1 then 1 end) as ticketReOpened")
                ->selectRaw("count(case when status = 12 and is_ended = 1 and is_closed = 1 and is_delivered_by_call_center = 1 then 1 end) as ticketClosed")
                ->selectRaw("count(case when status = 11 and is_ended = 1 then 1 end) as jobCompleted")
                ->selectRaw("count(case when status = 5 and is_paused = 1 then 1 end) as jobPaused")
                ->selectRaw("count(case when status = 4 and is_started = 1 then 1 end) as jobStarted")
                ->selectRaw("count(case when status = 3 and is_accepted = 1 then 1 end) as jobAccepted")
                ->selectRaw("count(case when status = 1 and is_assigned = 1 then 1 end) as assigned")
                ->selectRaw("count(case when status = 2 and is_rejected = 1 then 1 end) as rejected")
                ->selectRaw("count(case when status = 10 and is_delivered_by_call_center = 1 then 1 end) as deliveredby_call_center")
                ->selectRaw("count(case when status = 8 and is_delivered_by_teamleader = 1 then 1 end) as deliveredby_teamleader")
                ->selectRaw("count(case when status = 7 and is_closed_by_teamleader = 1 then 1 end) as closedby_teamleader")
                ->selectRaw("count(case when status = 12 and is_delivered_by_call_center = 0 and is_ended = 1 and is_closed = 1 then 1 end) as undelivered_close")
                ->first();
                return $this->sendResponse($status, 'Ticket status retrieved successfully.');
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return $this->sendError('Server error codes indicating that the request was accepted, but that an error on the server prevented the fulfillment of the request.', ['error' => $e->getMessage()]);
        }

    }
    public function ticketQuery(Request $request)
    {
        try {
            if ( empty( $request->except('_token') ) ){
                return $this->sendResponse('', 'Oops! The requested resource was not found.');
            }
            $data=DB::table('tickets')
            ->join('outlets','tickets.outlet_id','=','outlets.id')
            ->join('purchases','tickets.purchase_id','=','purchases.id')
            ->join('categories','tickets.product_category_id','=','categories.id')
            ->join('brand_models','purchases.brand_model_id', '=', 'brand_models.id')
            ->join('customers','purchases.customer_id', '=', 'customers.id')
            ->join('districts','tickets.district_id', '=','districts.id' )
            ->join('thanas','thanas.id', '=', 'tickets.thana_id')
            ->join('users', 'tickets.created_by', '=', 'users.id')
            ->select('users.name as created_by','brand_models.model_name as product_name','categories.name as product_category',
            'customers.name as customer_name', 'customers.mobile as customer_mobile', 'districts.name as district','thanas.name as thana',
            'purchases.product_serial as product_serial','purchases.invoice_number as invoice_number','tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name',
            'tickets.service_type_id as service_type_id','tickets.status as status','tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending',
            'tickets.is_paused as is_paused','tickets.is_ended as is_ended','tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader',
            'tickets.is_delivered_by_teamleader as is_delivered_by_teamleader','tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed',
            'tickets.is_assigned as is_assigned','tickets.is_rejected as is_rejected','tickets.delivery_date_by_call_center as delivery_date_by_call_center')
            ->where('tickets.deleted_at',null);

            if ($request->has('customer_name')) {

               $data=$data->where('customers.name', $request->customer_name);
               $ticket=$data->latest()->first();
            }
            if ($request->has('customer_number')) {

                $data=$data->where('customers.mobile', $request->customer_number);
                $ticket=$data->latest()->first();
             }
             if ($request->has('ticket_id')) {
                $ticket_id= $string = preg_replace("/[^0-9\.]/", '', $request->ticket_id);
                $data=$data->where('tickets.id', $ticket_id);
                $ticket=$data->latest()->first();
             }
             if ($request->has('product_sl_umber')) {

                $data=$data->where('purchases.product_serial', $request->product_sl_umber);
                $ticket=$data->latest()->first();
             }
             
             if ($ticket) {
                return $this->sendResponse(new TicketResource($ticket), 'Ticket retrieved successfully.');
             } else {
                return $this->sendResponse($ticket, 'The requested resource was not found.');
             }           

        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return $this->sendError('Server error codes indicating that the request was accepted, but that an error on the server prevented the fulfillment of the request.', ['error' => $e->getMessage()]);
        }
    }
    public function ticketQueryById(Request $request)
    {
        try {
            $array=$request->ticket_id;
            $ticket=DB::table('tickets')
            ->join('outlets','tickets.outlet_id','=','outlets.id')
            ->join('purchases','tickets.purchase_id','=','purchases.id')
            ->join('categories','tickets.product_category_id','=','categories.id')
            ->join('brand_models','purchases.brand_model_id', '=', 'brand_models.id')
            ->join('customers','purchases.customer_id', '=', 'customers.id')
            ->join('districts','tickets.district_id', '=','districts.id' )
            ->join('thanas','thanas.id', '=', 'tickets.thana_id')
            ->join('users', 'tickets.created_by', '=', 'users.id')
            ->select('users.name as created_by','brand_models.model_name as product_name','categories.name as product_category',
            'customers.name as customer_name', 'customers.mobile as customer_mobile', 'districts.name as district','thanas.name as thana',
            'purchases.product_serial as product_serial','purchases.invoice_number as invoice_number','tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name',
            'tickets.service_type_id as service_type_id','tickets.status as status','tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending',
            'tickets.is_paused as is_paused','tickets.is_ended as is_ended','tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader',
            'tickets.is_delivered_by_teamleader as is_delivered_by_teamleader','tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed',
            'tickets.is_assigned as is_assigned','tickets.is_rejected as is_rejected','tickets.delivery_date_by_call_center as delivery_date_by_call_center')
            ->where('tickets.deleted_at',null)
            ->whereIn('tickets.id',$array)
            ->latest()->get();

             if ($ticket) {
                return $this->sendResponse(new TicketResourceCollection($ticket), 'Ticket retrieved successfully.');
             } else {
                return $this->sendResponse($ticket, 'The requested resource was not found.');
             }           

        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return $this->sendError('Server error codes indicating that the request was accepted, but that an error on the server prevented the fulfillment of the request.', ['error' => $e->getMessage()]);
        }
    }
}
