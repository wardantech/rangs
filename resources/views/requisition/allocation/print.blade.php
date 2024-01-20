<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Pass</title>
    <style>
        body {
            margin: 0; /* Reset default margin */
            padding: 0; /* Reset default padding */
            size: A4; /* Set page size to A4 */
            margin-top: 2.54cm; /* 1 inch in centimeters */
            margin-bottom: 2.54cm;
            margin-left: 2.54cm;
            margin-right: 2.54cm;
        }
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
        .tr-font-size {
        font-size: 12px;
        span{

        }
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
            <th class="p-2">Print date: {{ $current_date->format('m/d/Y') }}</th>
        </tr>
    </table>
    <table style="border: 1px solid #000;margin-top:40px;">
        <tr>
            <td><span style="font-weight: bold">RFF#</span> B-RSL-{{ $allocation->requisition_id }}</td>
            <td  style="text-align: right;"> <span style="font-weight: bold">Issue Date :</span> {{ $allocation->created_at->format('m/d/Y h:i:s A') }}</td>
        </tr>
        <tr>
            <td><span style="font-weight: bold">Store Name:</span> {{ $allocation->requisition->senderStore->name }} </td>
            <td  style="text-align: right;"> <span style="font-weight: bold">R/Q Date :</span> {{ $allocation->requisition->created_at->format('m/d/Y h:i:s A') }}</td>
        </tr>
    </table>
    <table class="m-body" border="1">
        <tr>
            <th>Sl</th>
            <th>Part</th>
            <th>Description</th>
            <th>Model</th>
            <th>Purpose</th>
            <th>TSL</th>
            <th>Qnty</th>
            <th style="text-align: right">Unit Price</th>
            <th style="text-align: right">Amount</th>
        </tr>
        @php
            $total_quantity=null;
            $total_amount=null;
        @endphp
        @foreach ($allocation_details as $key=> $detail)
        <tr class="tr-font-size">
            <td>{{ $key+1 }}</td>
            <td style="text-align: center">
                {{ $detail['code'] }}
            </td>
            <td style="text-align: center">{{ $detail['part_name'] }}</td>
            <td style="text-align: center">{{ $detail['part_model'] }}</td>
            <td></td>
            <td></td>
            <td>{{ $detail['issued_quantity'] }}</td>
            <td style="text-align: right">{{ $detail['price'] }}</td>
            <td style="text-align: right">{{ $detail['amount'] }}</td>
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
            <td style="text-align: right">{{ $total_amount }}</td>
        </tr>
    </table>
    <div>
        <p style="font-weight: bold">Note:</p>
        <p> <span style="font-weight: bold">R/Q BY :</span> {{ $allocation->requisition->createdBy->name }}</p>
        <p> <span style="font-weight: bold">Issue BY :</span> {{ $allocation->createdBy->name }}</p>
    </div>
    <div style="width:300px;text-align:center;margin-top: 40px;">
        <p>__________________________</p>
        <p>(Assistance Manager (Store))</p>
    </div>
</body>
</html>