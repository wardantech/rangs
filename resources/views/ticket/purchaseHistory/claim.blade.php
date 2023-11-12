<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Claim Slip</title>
</head>
<style>
    table{
        width: 100%;
    }
    .custom-head{
        border:1px solid #000;
        border-collapse: collapse;
    }
    .custom-complainment{
        border-collapse: collapse; 
    }
    .information p{
        margin: 0px;

    }
    .charge p{
        margin: 0px;
    }
    td{
        border:1px solid #000;
        padding: 6px;  
    }
    tr{
        padding: 7px;
    }
    @media print{
        table{
        width: 100%;
    }
    li{
        font-size: 12px !important;
    }
    .custom-head{
        border:1px solid #000;
        border-collapse: collapse;
    }
    .custom-complainment{
        border-collapse: collapse; 
    }  
    th{
        font-size: 14px !important;
    }
    td{
        border:1px solid #000;
        padding: 6px;  
    }
    tr{
        padding: 7px;
    }
    }
</style>
<body onload="window.print();">
    <table>
        <tr>
            <th><img src="{{asset('img/rangs_red.png')}}" alt="brand-logo" height="35px" width="120px"></th>
            <th style="text-align: center">
                <h2 style="font-size: 28px;">Claim Slip</h2>
                <span style="padding: 5px; border:1px solid rgb(74, 74, 74)">TSL-{{ $ticket->id }}</span>
            </th>
            <th>
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
        <tr class="information">
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
            <th colspan="2">Accessories</th>
        </tr>
        <tr>
            @if ($ticket->fault_description_id)
            @php
               $faults=json_decode($ticket->fault_description_id); 
            @endphp
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

            @if ($ticket->product_condition_id)
                @php
                $product_conditionId = json_decode($ticket->product_condition_id);
                @endphp
                <td>
                @foreach($product_conditions as $product_condition)
                        @if ($product_condition != null && $product_conditionId !=null)
                            @if(in_array($product_condition->id, $product_conditionId))
                                <span class="badge badge-warning">{{$product_condition->product_condition}}</span><br>
                            @endif
                        @endif
                @endforeach
                </td>
            @else
                <td>Not Found</td> 
            @endif


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
            {{-- <td></td> --}}
        </tr>
        <tr>
            <td colspan="6">
                <h3 style="font-size:12px; text-align: center; margin: 0px;">Terms and condition</h3>
                {{-- <ol type="1">
                    <li>The article received for repair will be delivered to the person presenting this receipt and RANGS Electronics Ltd. will not be liable for any loss to occurring the customer in the event of delivery made to any person on presenting of this receipt.</li>
                    <li>If any article is left for repair and is not reclaimed by the customer within 3 months, storage charge will be imposed to the job. After 6 months it may be disposed by RANGS Electronics Ltd. in any deemed suitable. After 1 year no claim will be considered for delivery of the goods.</li>
                    <li>When the customer doesn’t want to complete the repair for any reason, product symptoms may change and the customer shall pay at least 50% of Technical Charge.</li>
                </ol> --}}
                <ol type="1">
                    <li>The product received with particular symptoms may change during the event of fault finding or any other valid reason. When the product is delivered without repair for any reason, the customer should receive the product in the existing condition/symptom.</li>
                    <li>RANGS Electronics Ltd. reserves the right to open, repair or transfer the product to the head office or any branch if required. Replaced old parts usually returnable, however, if any modification required in the circuitry or the parts are tiny, heavy or bulky those may not be possible to return.</li>
                    <li>The product received for repair will be delivered to the person presenting this receipt and RANGS Electronics Ltd. will not be liable for any loss occurring to the customer in the event of delivery made to any person on presenting of this receipt.</li>
                    <li>If any product is left for repair and is not reclaimed by the customer within 2 months, storage charge will be imposed to the product and RANGS Electronics Ltd. will not be liable for any subsequent damage or loss. After 6 months no claim will be considered for the product.</li>
                    <li>All transactions should have proper Bill and Money Receipt.</li>
                    <li>When the customer does not want to complete the repair for any reason whatsoever, shall pay the minimum charge as a registration fee.</li>
                    <li>RANGS Electronics Ltd. reserves the right to refuse to accept phase out or tampered products.</li>
                  </ol>
                  
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <h3 style="font-size:12px; text-align: center; margin: 0px;">শর্তাবলী</h3>
                {{-- <ol type="1">
                    <li>পরবর্তীতে এই রশিদ উপস্থাপনকারীকে পণ্যের মালিক হিসাবে গণ্য করা হবে। পণ্যটি ৩ মাসের মধ্যে ফেরত না নিলে এবং ঐ সময়ে পণ্যের কোন ক্ষতি হলে কর্তৃপক্ষ দায়ী হবে না। পণ্যটি ডেলিভারি নেওয়ার সময় অবশ্যই আপনার পণ্যের বর্তমান অবস্থা দেখে, বুঝে নিবেন।</li>
                    <li>৯০ দিনের পর থেকে পণ্যের উপর স্টোরেজ চার্জ আরোপ করা হবে।  ৬ মাসের মধ্যে ফেরত না নিলে আপনার পণ্য হারানো বা নষ্ট হওয়ার জন্য কর্তৃপক্ষ কোন অবস্থাতেই দায়ী থাকবে না। ১২ মাসের পরে পণ্যের উপর গ্রাহকের সকল প্রকার দাবি অগ্রাহ্য হবে। </li>
                    <li>বিনা মেরামতে ফেরত নিলে পণ্যের ত্রুটির ধরণ পরিবর্তিত হতে পারে ও নূন্যতম অর্ধেক চার্জ প্রদান আবশ্যক।</li>
                </ol> --}}
                <ol type="1">
                    <li>গৃহীত সামগ্রীটিতে পরীক্ষণের সময় অথবা যে কোন সংগত কারণে ত্রুটির ধরণ পরিবর্তন হতে পারে। অতএব, কোন কারণে বিনা মেরামতে সামগ্রীটি ফেরৎ নেওয়ার ক্ষেত্রে ঐ অবস্থাতেই গ্রহণ করা আবশ্যক।</li>
                    <li>গৃহীত সামগ্রীটি খোলা, মেরামত ও প্রয়োজনে আমাদের প্রধান সার্ভিস সেন্টার বা অন্য সেন্টারে প্রেরণের অধিকার কোম্পানি সংরক্ষণ করে। সাধারণভাবে বদলিকৃত পুরাতন পার্টস ফেরতযোগ্য, তবে বিশেষ ব্যবস্থায় যন্ত্রাংশ বা মডিউল সংযোজনের ক্ষেত্রে অথবা অতি ক্ষুদ্র অথবা ভারী অথবা বৃহৎ যন্ত্রাংশ অনেক সময় ফেরৎ দেওয়া সম্ভব হয় না।</li>
                    <li>গৃহীত সামগ্রীটি ফেরৎ নেওয়ার সময় এই রশিদ উপস্থাপনকারীকেই হস্তান্তর করা হবে এবং এতে সামগ্রীটি হারালে বা ক্ষতি হলে কোম্পানি দায়ী হবে না।</li>
                    <li>২ মাসের মধ্যে সামগ্রীটি ফেরৎ না নিলে স্টোরেজ চার্জ যোগ হবে এবং কোন ক্ষতি (হারানো বা নষ্ট) হলে কর্তৃপক্ষ দায়ী হবে না। ৬ মাস অতিক্রান্ত হলে গৃহীত সামগ্রীর উপর গ্রাহকের সকল দাবি অগ্রাহ্য হবে।</li>
                    <li>আর্থিক লেনদেনে বিল ও মানিরিসিট গ্রহণ একান্ত আবশ্যক।</li>
                    <li>বিনা মেরামতে সামগ্রী ফেরৎ নেওয়ার ক্ষেত্রে ধার্যকৃত ন্যূনতম চার্জ প্রদান আবশ্যক।</li>
                    <li>প্রত্যাহারকৃত বা অনেক পুরোনো মডেল সামগ্রী বা ক্ষতিগ্রস্ত সামগ্রী মেরামতের জন্য গ্রহণ না করার অধিকার কোম্পানি সংরক্ষণ করে।</li>
                  </ol>
                  
            </td>
        </tr>
        <tr style="padding: 6px;">
            <th>Attented By</th>
            <th>Returned Items</th>
            <th>Charge Break Up</th>
            <th>Accept Above Conditions</th>
            <th>Delivery Taken By</th>
        </tr>
        <tr class="charge" style="padding: 6px;">
            <td>{{ $ticket->createdBy->name }}</td>
            <td rowspan="2"></td>
            <td>
                <p>Technical Charge Tk : -</p>
                <p>Transportation Charge Tk : -</p>
                <p>Spare Parts as Required + vat: -</p>
            </td>
            <td>{{ $ticket->purchase->customer->name }}</td>
            <td>Signature</td>
        </tr>
        <tr style="padding: 6px;">
            <td></td>
            <td>Advance Payment: Tk -</td>
            <td colspan="3"> </td>
        </tr>
    </table>
</body>
</html>