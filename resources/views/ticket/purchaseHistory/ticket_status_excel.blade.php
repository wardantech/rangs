<table>
	<thead>
		<tr>
			<th colspan="13">Rangs Electronics Ltd</th>
		</tr>
		<tr>
			<th colspan="13">
				117/1 Airport Road, Tejgaon, Dhaka
			</th>
		</tr>
		<tr>
			<th colspan="13">
				Hotline: +88 09612 244 244.
			</th>
		</tr>
		<tr>
			<th colspan="13">{{ $status }}-Ticket List</th>
		</tr>	
		<tr>
			<th colspan="13">Report Generated On: {{ $date }}</th>	
		</tr>	
		<tr>
            <th>Sl#</th>
            <th>Ticket Number</th>
            <th>Point Of Purchase</th>
            <th>Invoice Number</th>
            <th>Customer Name</th>
            <th>Contact</th>
            <th>Place(District, Thana)</th>
            <th>Product Category</th>
            <th>Product Name</th>
            <th>Product SL</th>
            <th>Service Type</th>
            <th>Warranty Type</th>
            <th>Branch</th>
            <th>Created By</th>
            <th>Created At</th>
            <th>Status</th>
			<th>Delivered By CC Date</th>
        </tr>
	</thead>
	<tbody>
		@foreach($tickets as $key => $ticket)
		@php
			$selectedServiceTypeIds=json_decode($ticket->service_type_id);
			$service_type_data='N/A';
			$point_of_purchase="N/A";
			foreach ($serviceTypes as $key => $serviceType) {
				if (in_array($serviceType->id, $selectedServiceTypeIds)) {
					$service_type_data=$serviceType->service_type;
					}
				}
					$data=App\Models\Outlet\Outlet::where('id', '=', $ticket->outletid)->first();
					if ($data) {
						$point_of_purchase=$data->name;
					} else {
						$point_of_purchase='N/A';
					}

		@endphp
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>TSL-{{ $ticket->ticket_id }}</td>
			<td>{{ $point_of_purchase ?? null }}</td>
			<td>{{ $ticket->invoice_number ?? null }}</td>
			<td>{{ $ticket->customer_name }}</td>
			<td>{{ $ticket->customer_mobile }}</td>
			<td>{{ $ticket->district }}-{{ $ticket->thana }}</td>
			<td>{{ $ticket->product_category }}</td>
			<td>{{ $ticket->product_name }}</td>
			<td>{{ $ticket->product_serial }}</td>
			<td>{{ $service_type_data }}</td>
			<td>{{ $ticket->warranty_type }}</td>
			<td>{{ $ticket->outlet_name }}</td>
			<td>{{ $ticket->created_by }}</td>
			<td>{{  Carbon\Carbon::parse($ticket->created_at)->format('m/d/Y')   }}</td>
			@if ($ticket->status == 9 && $ticket->is_reopened == 1)
				<td>Ticket Re-Opened</td>
			@elseif($ticket->status == 0)
				<td>Created</td>
			@elseif($ticket->status == 6 && $ticket->is_pending==1)
				<td>Pending</td>
			@elseif($ticket->status == 5 && $ticket->is_paused == 1)
				<td>Paused</td>
			@elseif($ticket->status == 7  && $ticket->is_closed_by_teamleader == 1)
				<td>Forwarded to CC</td>
			@elseif($ticket->status == 10 && $ticket->is_delivered_by_call_center == 1)
				<td>Delivered by CC</td>
			@elseif($ticket->status == 8 && $ticket->is_delivered_by_teamleader == 1)
				<td>Delivered by TL</td>
			@elseif($ticket->status == 12  && $ticket->is_delivered_by_call_center == 1 && $ticket->is_closed == 1)
				<td>Tticket is Closed</td>
			@elseif($ticket->status == 12 && $ticket->is_delivered_by_call_center == 0 && $ticket->is_closed == 1)
				<td>Ticket is Undelivered Closed</td>
			@elseif($ticket->status == 11 && $ticket->is_ended == 1)
				<td>Job Completed</td>
			@elseif($ticket->status == 4 && $ticket->is_started == 1)
				<td>Job Started</td>
			@elseif($ticket->status == 3 && $ticket->is_accepted == 1)
				<td>Job Accepted</td>
			@elseif($ticket->status == 1 && $ticket->is_assigned == 1)
				<td>Job Asigned</td>
			@elseif($ticket->status == 2 && $ticket->is_rejected == 1)
				<td>Job Rejected</td>
			@endif

			@if ($ticket->delivery_date_by_call_center != null)
				<td>{{ Carbon\Carbon::parse($ticket->delivery_date_by_call_center)->format('m/d/Y') }}</td>
			@else
				<td>Not Found</td>
			@endif

		</tr>
		@endforeach
	</tbody>
	<tfoot>
		<tr>
            <th colspan="13">Report generated on: {{ $date }}</th>
        </tr>
	</tfoot>
</table>
