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
    .job-slip-header{
        text-align: center;
        padding-bottom:15px;
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
            <th width="33%" class="job-slip-header">
                <h2 style="font-size: 28px;margin-bottom:5px;">Job Slip</h2>
                <span style="padding: 5px; border:1px solid rgb(74, 74, 74)">Job No : TSL-{{ $ticket->id }}</span>
            </th>
            <th width="33%">
                <span style="font-size: 18px">RANGS Electronics Ltd.</span>
                <br>
                <span> 117/1 Airport Road, Tejgaon, Dhaka </span>
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
                <p>Name: {{ $ticket->purchase->customer->name }}</p>
                <p>Address: {{ $ticket->purchase->customer->address }}</p>
                <p>Phone: {{ $ticket->purchase->customer->mobile }}</p>
            </td>
            <td>
                <p>Category: {{ $ticket->category->name }}</p>
                <p>Brand: {{ $ticket->purchase->brand->name }}</p>
                <p>Model: {{ $ticket->purchase->modelname->model_name }}</p>
                <p>Serial No: {{ $ticket->purchase->product_serial }}</p>
            </td>
            <td colspan="2">
                <p>Receive at: {{ $ticket->outlet->name }}</p>
                <p>Phone: {{ $ticket->outlet->mobile }}</p>
                <p>Received Date: {{ $ticket->created_at->format('m/d/Y') }}</p>
                <p>Del. Dt (approx): {{ \Carbon\Carbon::parse($ticket->end_date)->addDays(5)->format('m/d/Y') }}</p>
            </td>
        </tr>
    </table>
    <table border="1" class="custom-complainment">
        <tr>
            <th colspan="2">
                Customer Complaints / Symptom(s)
            </th>
            <th>Purchase Date</th>
            <th>Physical Condition</th>
            <th>Accessories</th>
        </tr>
        <tr style="height: 60px !important;">
            @php
               $faults=json_decode($ticket->fault_description_id); 
               $product_conditions_id=json_decode($ticket->product_condition_id); 
            @endphp

            @if ($ticket->fault_description_id)
            <td>
                @foreach($allFaults as $fault)
                    @if ($fault != null && $faults !=null)
                        @if(in_array($fault->id, $faults))
                            <span class="badge badge-warning">{{$fault->name}}</span><br>
                        @endif
                    @endif
                @endforeach
            </td>
            @else
            <td>Not Found</td>
            @endif
            @if ($ticket->fault_description_note)
                <td>{{ $ticket->fault_description_note }}</td>
            @else
                <td>--</td>
            @endif
            <td>
                {{ $ticket->purchase->purchase_date->format('m/d/Y') }}
            </td>
            @isset ($ticket->product_condition_id)
            <td>
                @foreach($product_conditions as $product_condition)
                    @if ($product_condition != null && $product_conditions_id != null)
                        @if(in_array($product_condition->id, $product_conditions_id))
                            <span class="badge badge-warning">{{$product_condition->product_condition}}</span><br>
                        @endif
                    @endif
                @endforeach
            </td>
            @endisset
            @if ($ticket->accessories_list_id)
                <?php $accessories=json_decode($ticket->accessories_list_id)?>
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
            <td>Part No</td>
            <td>Description</td>
            <td>Qty</td>
            <td>Price (Including VAT)</td>
        </tr>
        <tr>
            <td>
                <p>Name:</p>
                <p>Sign:</p>
                <p>Date:</p>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="vertical-align: top; ">
                Repair Description
            </td>
            <td>Returend Items</td>
            <td>Customer Signature</td>
            <td colspan="2">Total Cost of spares:</td>
        </tr>
        <tr>
            <td rowspan="3"></td>
            <td rowspan="3"></td>
            <td rowspan="3">{{ $ticket->purchase->customer->name }}</td>
            <td colspan="2">Repair Charge</td>
        </tr>
        <tr>
            <td  colspan="2">Other Charge:</td>
        </tr>
        <tr>
            <td  colspan="2">Total</td>
        </tr>
    </table>
</body>
</html>