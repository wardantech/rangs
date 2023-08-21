<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Pass</title>
    <style>
        table{
            width:100%;
        }
        table td{
            padding:7px;
        }
        .m-body{
            border-collapse: collapse;
            margin-top:20px;  
        }
    </style>
</head>
<body onload="window.print();">
    <table>
        <tr>
            <th style="width: 200px;"></th>
            <th style="text-align: center;"><h2>Rangs Electronics Ltd.</h2>
                <span  style="border: 1px solid #000;padding: 10px 20px;margin-top:20px;">Gate Pass</span>
            </th>
            <th>Print date: {{ $current_date->format('m/d/Y') }}</th>
        </tr>
    </table>
    <table style="border: 1px solid #000;margin-top:40px;">
        <tr>
            <td>RFF# {{ $allocation->requisition->senderStore->name }} / B-RSL-{{ $allocation->requisition_id }}</td>
            <td  style="text-align: right;">B-RSL : {{ $allocation->created_at->format('m/d/yy H:i:s') }}</td>
        </tr>
        <tr>
            <td>BR Name: {{ $allocation->requisition->senderStore->outlet->name }}</td>
            <td  style="text-align: right;">R/Q Date : {{ $allocation->requisition->created_at->format('m/d/yy H:i:s') }}</td>
        </tr>
    </table>
    <table class="m-body" border="1">
        <tr>
            <th>Sl</th>
            <th>Part</th>
            <th>Description</th>
            <th>Model</th>
            <th>Purpose</th>
            <th>Job</th>
            <th>Qnty</th>
            <th>Unit Price</th>
            <th>Amount</th>
        </tr>
        @php
            $total_quantity=null;
            $total_amount=null;
        @endphp
        @foreach ($allocation_details as $key=> $detail)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>
                {{ $detail['code'] }}
            </td>
            <td>{{ $detail['part_name'] }}</td>
            <td>{{ $detail['part_model'] }}</td>
            <td>Null</td>
            <td>Null</td>
            <td>{{ $detail['issued_quantity'] }}</td>
            <td>{{ $detail['price'] }}</td>
            <td>{{ $detail['amount'] }}</td>
            @php
                $total_quantity+=$detail['issued_quantity'];
                $total_amount+=$detail['amount'];
            @endphp
        </tr>
    @endforeach
        <tr>
            <td colspan="6" style="text-align: right;">Total</td>
            <td>{{ $total_quantity }}</td>
            <td></td>
            <td>{{ $total_amount }}</td>
        </tr>
    </table>
    <div>
        <p>Note:</p>
        <p>R/Q BY : {{ $allocation->requisition->createdBy->name }}</p>
        <p>Issue BY : {{ $allocation->createdBy->name }}</p>
    </div>
    <div style="width:300px;text-align:center;margin-top: 40px;">
        <p>__________________________</p>
        <p>(Assistance Manager (Store))</p>
    </div>
</body>
</html>