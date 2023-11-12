<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill</title>
    <style>
        table {
            width: 100%;
        }

        .bold {
            font-weight: bolder;
        }

        .t-details {
            border-collapse: collapse;
            border: 0px;
        }

        .t-details td {
            padding: 5px;
            border: 1px solid #000;
        }

        .emptyborder {
            width: 100px;
            border: 0px !important;
        }

        .memo-body {
            border-collapse: collapse;
        }

        .memo-body td {
            padding: 5px;
        }

        .mfoot {
            text-align: right;
            padding-right: 10px;
        }

        .heading {
            text-align: center;
            margin-top: -33px;
        }

        .bill {
            text-align: center;
        }

        .right-box {
            text-align: right;
            float: right;
        }

        .branch {
            width: 226px;
            text-align: left;
        }

        .signature {
            float: right;
            padding-right: 30px;
        }
    </style>
</head>

<body onload="window.print();">
    <div class="logo">
        <img src="{{ asset('img/rangs_red.png') }}" alt="brand-logo" height="35px" width="120px">
    </div>
    <div class="heading">
        <h1 style="margin:0px;">RANGS Electronics Limited</h1>
        <h2 style="margin:0px;">SERVICE CENTER</h2>
    </div>
    <div style="margin-top:10px;" class="bill">
        <strong style="border:1px solid #000;padding:7px 45px;">Bill</strong>
    </div>
    <div class="right-box">
        <div style="margin-top: -27px;" class="branch">
            <p><strong>Branch</strong></p>
            <p style="border: 1px solid #000;padding: 7px;">
                <span>{{ $jobSubmission->job->ticket->outlet->name }}</span> <br>
                <span>Tel : {{ $jobSubmission->job->ticket->outlet->mobile }}</span>

            </p>
        </div>
    </div>
    <table border="1" class="t-details">
        <tr>
            <td>Name :</td>
            <td>{{ $jobSubmission->job->ticket->purchase->customer->name }}</td>
            <td class="emptyborder"></td>
            <td>Bill No:</td>
            <td>{{ $jobSubmission->id }}</td>
            <td>{{ $jobSubmission->created_at->format('m/d/Y') }}</td>
        </tr>
        <tr>
            <td rowspan="4">Address :</td>
            <td rowspan="4">{{ $jobSubmission->job->ticket->purchase->customer->address }}</br>
                {{ $jobSubmission->job->ticket->purchase->customer->mobile }}
            </td>
            <td class="emptyborder"></td>
            <td>Item :</td>
            <td>{{ $jobSubmission->job->ticket->category->name }}</td>
            <td>Brand: {{ $jobSubmission->job->ticket->purchase->brand->name }}</td>
        </tr>
        <tr>
            <td class="emptyborder"></td>
            <td>Model No :</td>
            <td colspan="2">{{ $jobSubmission->job->ticket->purchase->modelname->model_name }}</td>
        </tr>
        <tr>
            <td class="emptyborder"></td>
            <td>Serial No :</td>
            <td colspan="2"> {{ $jobSubmission->job->ticket->purchase->product_serial }}</td>
        </tr>
        <tr>
            <td class="emptyborder"></td>
            <td>Job No :</td>
            <td colspan="2">JSL-{{ $jobSubmission->job->id }} & TSL-{{ $jobSubmission->job->ticket->id }}</td>
        </tr>
    </table>
    <table style="margin-top: 15px;" class="memo-body" border="1">
        <tr>
            <th>SL No</th>
            <th>Part No</th>
            <th>Description</th>
            <th>Rate</th>
            <th>Qty.</th>
            <th>Amount in Taka</th>
        </tr>
        <?php
        $sl = 0;
        $parts_total = 0;
        ?>
        @if (!$jobSubmissionDetails->isEmpty())

            @foreach ($jobSubmissionDetails as $item)
                <tr>
                    <td>{{ ++$sl }}</td>
                    <td>{{ $item->part->code ?? null }}</td>
                    <td>{{ $item->part->name ?? null }}</td>
                    <td>{{ number_format($item->selling_price_bdt, 2) }}</td>
                    <td>{{ $item->used_quantity }}</td>
                    <td>{{ number_format($item->selling_price_bdt * $item->used_quantity, 2) }}
                        @php
                            $parts_total += $item->selling_price_bdt * $item->used_quantity;
                        @endphp
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td>{{ __('label.DATA_NOT_FOUND') }}</td>
            </tr>
        @endif
        @php
            $vat_on_parts = ($parts_total * 5) / 100;
            $partsamount_with_vat = $parts_total + $vat_on_parts;
            $total_service_amount=$jobSubmission->fault_finding_charges+$jobSubmission->repair_charges+$jobSubmission->other_charges;
            $vat_on_service = $total_service_amount * 10 / 100;
            $serviceamount_with_vat =$total_service_amount + $vat_on_service;
            $total_bill = $partsamount_with_vat + $serviceamount_with_vat;
            $subtracting=$jobSubmission->discount + $jobSubmission->advance_amount;
            $payable_amount = $total_bill - $subtracting;
        @endphp
        <tr>
            <td class="mfoot" colspan="5">Parts Amount</td>
            <td style="text-align: center;">{{ $parts_total }}</td>
        </tr>
        <tr>
            <td class="mfoot" colspan="5">VAT (5%)</td>
            <td style="text-align: center;">{{ $vat_on_parts }}</td>
        </tr>
        <tr>
            <td class="mfoot bold" colspan="5">Parts Amount with VAT</td>
            <td class="bold" style="text-align: center;">{{ $partsamount_with_vat }}</td>
        </tr>
        <tr>
            <td class="mfoot" colspan="5">Fault Finding Charge</td>
            <td style="text-align: center;">{{ number_format($jobSubmission->fault_finding_charges, 2) }}</td>
        </tr>
        <tr>
            <td class="mfoot" colspan="5">Service Charge</td>
            <td style="text-align: center;">{{ number_format($jobSubmission->repair_charges, 2) }}</td>
        </tr>
        <tr>
            <td class="mfoot" colspan="5">Other Charge</td>
            <td style="text-align: center;">{{ number_format($jobSubmission->other_charges, 2) }}</td>
        </tr>
        <tr>
            <td class="mfoot" colspan="5">VAT (10%)</td>
            <td style="text-align: center;">
                {{ $vat_on_service }}
            </td>
        </tr>
        <tr>
            <td class="mfoot bold" colspan="5">Service Amount with VAT</td>
            <td class="bold" style="text-align: center;">
                {{ $serviceamount_with_vat}}
            </td>
        </tr>
        <tr>
            <td class="mfoot bold" colspan="5">Total Bill</td>
            <td class="bold" style="text-align: center;">
                {{ $total_bill }}
            </td>
        </tr>
        <tr>
            <td class="mfoot" colspan="5">Discount</td>
            <td style="text-align: center;">{{ number_format($jobSubmission->discount, 2) }}</td>
        </tr>
        <tr>
            <td class="mfoot" colspan="5">Advanced</td>
            <td style="text-align: center;">{{ number_format($jobSubmission->advance_amount, 2) }}</td>
        </tr>
        {{-- @if (isset($jobSubmission->vat))
            <tr>
                <td class="mfoot" colspan="5">Vat</td>
                <td style="text-align: center;">{{ number_format($jobSubmission->vat, 2) }}</td>
            </tr>
        @endif --}}
        <tr>
            <th class="mfoot" colspan="5">Total</th>
            <?php
            $toatl_bill = $parts_total + $jobSubmission->fault_finding_charges + $jobSubmission->repair_charges + $jobSubmission->other_charges + $jobSubmission->discount + $jobSubmission->advance_amount;
            $toatl_sub = $jobSubmission->discount;
            $current_bill = $toatl_bill - $toatl_sub;
            ?>
            <th>{{ number_format($payable_amount, 2) }}</th>
        </tr>
        <tr style="border: 0px;text-align:left;">
            @if ($jobSubmission->total_amount)
                <?php $get_amount = numberTowords($payable_amount); ?>
                <th colspan="6">Total Amount in Word: {{ $get_amount }} Taka Only</th>
            @else
                <th colspan="6">Total Amount in Word: Null</th>
            @endif

        </tr>
    </table>
    <div class="signature">
        <div style="margin-top: 30px;">
            <h2 style="text-align: center;">For RANGS Electronics Limited</h2>
            <p style="margin-top: 40px;border-top: 1px solid #000;text-align: center;">Signature</p>
        </div>
    </div>
    <?php
    // Create a function for converting the amount in words
    function numberTowords(float $amount)
    {
        $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
        // Check if there is any number after decimal
        $amt_hundred = null;
        $count_length = strlen($num);
        $x = 0;
        $string = [];
        $change_words = [0 => 'Zero ', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'];
        $here_digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
        while ($x < $count_length) {
            $get_divider = $x == 2 ? 10 : 100;
            $amount = floor($num % $get_divider);
            $num = floor($num / $get_divider);
            $x += $get_divider == 10 ? 1 : 2;
            if ($amount) {
                $add_plural = ($counter = count($string)) && $amount > 9 ? 's' : null;
                $amt_hundred = $counter == 1 && $string[0] ? ' and ' : null;
                $string[] =
                    $amount < 21
                        ? $change_words[$amount] .
                            ' ' .
                            $here_digits[$counter] .
                            $add_plural .
                            '
             ' .
                            $amt_hundred
                        : $change_words[floor($amount / 10) * 10] .
                            ' ' .
                            $change_words[$amount % 10] .
                            '
             ' .
                            $here_digits[$counter] .
                            $add_plural .
                            ' ' .
                            $amt_hundred;
            } else {
                $string[] = null;
            }
        }
        $implode_to_Rupees = implode('', array_reverse($string));
        $get_paise =
            $amount_after_decimal > 0
                ? 'And Taka ' .
                    ($change_words[$amount_after_decimal / 10] .
                        "
       " .
                        $change_words[$amount_after_decimal % 10]) .
                    'Only'
                : '';
        return ($implode_to_Rupees ? $implode_to_Rupees . '' : '') . $get_paise;
    }
    
    ?>
</body>

</html>
