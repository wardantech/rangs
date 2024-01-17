<table>
	<thead>
		<tr>
			<th colspan="18">Rangs Electronics Ltd</th>
		</tr>
		<tr>
			<th colspan="18">
				117/1 Airport Road, Tejgaon, Dhaka
			</th>
		</tr>
		<tr>
			<th colspan="18">
				Hotline: +88 09612 244 244.
			</th>
		</tr>
		<tr>
			<th colspan="18">{{ $status }}-Job List</th>
		</tr>	
		<tr>
			<th colspan="18">Report Generated On: {{ $date }}</th>	
		</tr>	
		<tr>
            <th>Sl#</th>
            <th>Technician</th>
            <th>Technician Type</th>
            <th>Branch</th>
            <th>Ticket SL</th>
            <th>Ticket Created At</th>
            <th>Customer Name</th>
            <th>Customer Phone</th>
            <th>Purchase Date</th>
            <th>Job Number</th>
            <th>Service Type</th>
            <th>Warranty Type</th>
            <th>Assigned Date</th>
            <th>Assigned By</th>
            <th>Job Priorty</th>
            <th>Product Category</th>
            <th>Brand Name</th>
            <th>Product Name</th>
            <th>Product Serial</th>
            <th>Point Of Purchase</th>
            <th>Invoice Number</th>
            <th>Job Status</th>
            <th>Completed At</th>
            <th>Job Pending Note</th>
        </tr>
	</thead>
	<tbody>
		@foreach($jobs as $key => $job)
        @php
            $selectedServiceTypeIds=json_decode($job->service_type_id);
            $service_type_data='N/A';
            $point_of_purchase='N/A';
            $pending_notes=null;
            $pendingNotes=DB::table('job_pending_notes')->where('job_id',$job->job_id)->whereNull('deleted_at')->get();
            foreach ($serviceTypes as $key => $serviceType) {
                if (in_array($serviceType->id, $selectedServiceTypeIds)) {
                    $service_type_data=$serviceType->service_type;
                    }
                }
            foreach ($pendingNotes as $key => $item) {
                    // $pending_notes.= $item->job_pending_remark .'*'. $item->special_components .'*'. $item->job_pending_note;
                    
                    $pending_notes .= $item->job_pending_remark;
    
                    if ($item->special_components) {
                        $pending_notes .= '*' . $item->special_components;
                    }

                    if ($item->job_pending_note) {
                        $pending_notes .= '*' . $item->job_pending_note;
                    }
                    
                    if (!$loop->first && !$loop->last) {
                        $pending_notes .= '; ';
                    }
            }
            $data=App\Models\Outlet\Outlet::where('id', '=', $job->outletid)->first();
            if ($data) {
                $point_of_purchase=$data->name;
            } else {
                $point_of_purchase='N/A';
            }
            

        @endphp
		<tr>
			<td>{{ $loop->iteration }}</td>
            <td>{{ $job->employee_name ?? null }}</td>
            <td>{{ $job->vendor_id ? 'Vendor':'Own' }}</td>
            <td>{{ $job->outlet_name ?? null }}</td>
			<td>TSL-{{ $job->ticket_id ?? null }}</td>
			<td>{{   Carbon\Carbon::parse($job->created_at)->format('m/d/Y')  ?? null  }}</td>
            <td>{{ $job->customer_name ?? null }}</td>
            <td>{{ $job->customer_mobile ?? null }}</td>
			<td>{{   Carbon\Carbon::parse($job->purchase_date)->format('m/d/Y')  ?? null  }}</td>
			<td>JSL-{{ $job->job_id }}</td>
            <td>{{ $service_type_data }}</td>
            <td>{{ $job->warranty_type }}</td>
            <td>{{   Carbon\Carbon::parse($job->assigning_date)->format('m/d/Y')  ?? null  }}</td>
            <td>{{ $job->created_by ?? null }}</td>
			<td>{{ $job->job_priority ?? null }}</td>
			<td>{{ $job->product_category ?? null }}</td>
			<td>{{ $job->brand_name ?? null }}</td>
			<td>{{ $job->model_name ?? null }}</td>
			<td>{{ $job->product_serial ?? null}}</td>
			<td>{{ $point_of_purchase ?? null}}</td>
			<td>{{ $job->invoice_number ?? null}}</td>
            @if ($job->status == 6 )
                <td>Paused</td>
            @elseif($job->status == 5)
                <td>Pending</td>
            @elseif($job->status == 0)
                <td>Created</td>
            @elseif($job->status == 4)
                <td>Completed</td>
            @elseif($job->status == 3)
                <td>Started</td>
            @elseif($job->status == 1)
                <td>Accepted</td>
            @elseif($job->status == 2)
                <td>Rejected</td>
            @endif
            {{-- <td>{{   Carbon\Carbon::parse($job->job_end_time)->format('m/d/Y') ?? null  }} </td> --}}
            <td>{{ optional(Carbon\Carbon::parse($job->job_end_time))->format('m/d/Y') }}</td>

            <td>{{ $pending_notes }}</td>
		</tr>
		@endforeach
	</tbody>
	<tfoot>
		<tr>
            <th colspan="18">Report generated on: {{ $date }}</th>
        </tr>
	</tfoot>
</table>
