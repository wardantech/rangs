<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Slip</title>
</head>
<style>
    table{
        width: 100%;
        border-collapse: collapse;
        border:1px solid #000;
    }
    .custom-head{
        border:1px solid #000;
        border-collapse: collapse;
    }
    .custom-complainment{
        border-collapse: collapse; 

    }
    td{
   
        padding: 7px;  
    }
    tr{
        padding: 7px;
    }
    @media print{
        table{
        width: 100%;
    }
    .custom-head{
        border:1px solid #000;
        border-collapse: collapse;
    }
    .custom-complainment{
        border-collapse: collapse; 
        border: 1px solid #000;
    }  
    td{
        padding: 7px;  
    }
    tr{
        padding: 7px;
    }
    }
</style>
<body onload="window.print();">
    <table style="border:0px !important">
        <tr>
            <th width="33%"><img src="{{asset('img/rangs_red.png')}}" alt="brand-logo" height="35px" width="120px"></th>
            <th width="33%" style="text-align: center;padding-bottom:15px;">
                <h2 style="font-size: 28px;margin-bottom:5px;">Job Slip</h2>
                <span style="padding: 5px; border:1px solid rgb(74, 74, 74)">Job No : {{ 'JSL-'.$job->id }}</span>
            </th>
            <th width="33%">
                <span style="font-size: 22px">Rangs Electronics Ltd.</span>
                <br>
                <span> 117/1 Airport Road, Tejgaon, Dhaka </span>
                <br>
                <span>Hotline: +88 09612 244 244.</span>
            </th>
        </tr>
    </table>
    <table class="custom-head" border="1">
        <tr>
            <th colspan="2">Customer Information</th>
            <th>Product Information</th>
            <th colspan="2">Service Information</th>
        </tr>
        <tr>
            <td  colspan="2">
                <p>Name: {{ $job->ticket->purchase->customer->name }}</p>
                <p>Address: {{ $job->ticket->purchase->customer->address }}</p>
                <p>Phone: {{ $job->ticket->purchase->customer->mobile }}</p>
            </td>
            <td>
                <p>Category: {{ $job->ticket->category->name }}</p>
                <p>Brand: {{ $job->ticket->purchase->brand->name }}</p>
                <p>Model: {{ $job->ticket->purchase->modelname->model_name }}</p>
                <p>Serial No: {{ $job->ticket->purchase->product_serial }}</p>
                <p>Point Of Purchase: {{ $job->ticket->purchase->outlet->name }}</p>
            </td>
            <td colspan="2">
                <p>Receive at: {{ $job->ticket->outlet->name }}</p>
                <p>Phone: {{ $job->ticket->outlet->mobile }}</p>
                <p>Received Date: {{ $job->ticket->created_at->format('m/d/Y') }}</p>
                <p>Del. Dt (approx): {{ \Carbon\Carbon::parse($job->ticket->end_date)->addDays(5)->format('m/d/Y') }}</p>
            </td>
        </tr>
    </table>
    <table border="1" class="custom-complainment">
        <tr>
            <th colspan="2">
                Customer Complaints / Symptom(s)
            </th>
            <th>Job Type</th>
            <th>Physical Condition</th>
            <th>Accessories</th>
        </tr>
        <tr style="height: 100px;">
            @php
               $faults=json_decode($job->ticket->fault_description_id); 
               $productConditionId = json_decode($job->ticket->product_condition_id);
            @endphp

            @if ($job->ticket->fault_description_note || $job->ticket->fault_description_id)
                <td colspan="2">
                    @isset ($job->ticket->fault_description_id)
                        @foreach($allFaults as $fault)
                            @if ($fault != null && $faults !=null)
                                @if(in_array($fault->id, $faults))
                                    <span class="badge badge-warning">{{$fault->name}}</span><br>
                                @endif
                            @endif
                        @endforeach
                    @endisset
                    @isset ($job->ticket->fault_description_note)
                    Note: {{ $job->ticket->fault_description_note }}
                    @endisset
                </td>
            @else
                <td colspan="2">Not Found</td>
            @endif
            <td>
                <?php $selectedServiceTypeIds= json_decode($job->ticket->service_type_id)?>
                @foreach ($serviceTypes as $serviceType)
                    @if ($serviceType != null && $selectedServiceTypeIds !=null)
                        @if (in_array($serviceType->id, $selectedServiceTypeIds))
                            {{$serviceType->service_type}}
                        @endif
                    @endif
                @endforeach
            </td>
            @isset ($job->ticket->product_condition_id)
            <td>
                @foreach ($product_conditions as $product)
                    @if ($product->id != null && $productConditionId != null)
                        @if (in_array($product->id, $productConditionId))
                            {{ $product->product_condition }},
                        @endif
                    @endif
                @endforeach
            </td>
            @endisset
            @if ($job->ticket->accessories_list_id)
                <?php $accessories=json_decode($job->ticket->accessories_list_id)?>
                <td>
                    @foreach($allAccessories as $accessory)
                    @if ($accessory != null && $accessories !=null)
                        @if(in_array($accessory->id, $accessories))
                            <span class="badge badge-success">{{$accessory->accessories_name}}</span><br>
                        @endif
                    @endif
                    @endforeach
                </td>
                @else
                <td>Not Found</td>
            @endif
        </tr>
    </table>
    <table  border="1" class="charge-table">
        <tr>
            <td>Repaired By</td>
            <td>Q.C By</td>
            <td>Parts Name</td>
            <td colspan="3">Part No</td>
            <td>Qty</td>
            <td style="width: 90px;">Price</td>
        </tr>
        <tr>
            <td>
                <p>Name:</p>
                <p>Sign:</p>
                <p>Date:</p>
            </td>
            <td></td>
            <td></td>
            <td colspan="3"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2" rowspan="4">
                Repair Description
            </td>
            <td>Returend Items</td>
            <td rowspan="3">Customer Signature</td>
            <td colspan="2">Total Cost of spares:</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td rowspan="3"></td>
            {{-- <td></td> --}}
            <td colspan="2">Repair Charge</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            {{-- <td rowspan="1"></td> --}}
            <td  colspan="2">Other Charge:</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td>{{ $job->ticket->purchase->customer->name }}</td>
            <td  colspan="2">Total</td>
            <td colspan="3"></td>
        </tr>
    </table>
</body>
</html>