<p class="mb-1">Service History</p>
<table id="datatable" class="table">
    <thead>
        <tr>
            <th>{!! __('label.DATE_AND_TIME') !!}</th>
            <th>{{ __('label.PRODUCT')}}</th>
            <th>{{ __('label.BRAND')}}</th>
            <th>{{ __('label.STATUS')}}</th>
            <th>{{ __('label.SERVICE_DESCRIPTION')}}</th>
            <th>{{ __('label.DETAILS')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($purchases as $purchase)
            @foreach($purchase->ticket as $ticket)
                <tr>
                    <td>{{ $ticket->date->format('m/d/Y')}}</td>
                    <td>{{ $ticket->purchase->category->name }}</td>
                    <td>{{ $ticket->purchase->brand->name }}</td>
                    <td>
                        @if ($ticket->status == 0)
                        <span class="badge bg-yellow">Pending</span>
                        @elseif($ticket->status == 1 && $ticket->is_ended == 1 && $ticket->is_closed_by_teamleader == 1 && $ticket->is_reopened == 1)
                        <span class="badge bg-red">Ticket Re-Opened</span>
                        @elseif($ticket->status == 1 && $ticket->is_ended == 1 && $ticket->is_closed == 1)
                        <span class="badge badge-danger">Ticket Closed</span>
                        @elseif($ticket->status == 1 && $ticket->is_ended == 1)
                        <span class="badge badge-success">Job Completed</span>
                        @elseif($ticket->status == 1 && $ticket->is_started == 1)
                        <span class="badge badge-info">Job Started</span>
                        @elseif($ticket->status == 1 && $ticket->is_accepted == 1)
                        <span class="badge badge-primary">Job Accepted</span>
                        @elseif($ticket->status == 1 && $ticket->is_assigned == 1)
                        <span class="badge bg-blue">Assigned</span>
                        @elseif ($ticket->status == 2 && $ticket->is_rejected == 1)
                        <span class="badge bg-red">Rejected</span>
                        @endif
                    </td>
                    @php
                        $descriptions = json_decode($ticket->fault_description_id);
                    @endphp
                    <td>
                        @foreach ($descriptions as $key=> $description)
                            @foreach ($faults as $fault)
                                @if ($fault->id == $description)
                                    <span class="badge badge-info">
                                        {{ $fault->name }}
                                    </span>
                                @endif
                            @endforeach
                        @endforeach
                    </td>
                    <td>
                        @can('create')
                            <a href="{{route('show-ticket-details', $ticket->id)}}" class="btn btn-warning">{{ __('label.TICKET')}}</a>
                        @endcan

                        {{-- @can('show')
                            <a href="{{ route('job.job.show', $ticket->job->id) }}" class="btn btn-warning">{{ __('label.JOB')}}</a>
                        @endcan --}}
                    </td>
                <tr>
            @endforeach
        @endforeach
    </tbody>
</table>
