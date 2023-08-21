<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill</title>
    <style>
        table{
            width: 100%;
        }
        .t-details{
            border-collapse: collapse;
            border: 0px;
        }
        .t-details td{
            padding: 5px;
            border: 1px solid #000;
        }
        .emptyborder{
            width: 200px;
            border: 0px !important;
        }
        .memo-body{
            border-collapse: collapse;
        }
        .memo-body td{
            padding: 5px;
        }
        .mfoot{
            text-align: right;
            padding-right:10px;
        }
    </style>
</head>
<body onload="window.print();">
    <table>
        <tr>
            <th>
                <img src="{{asset('img/Sony.png')}}" alt="brand-logo" height="35px" width="120px">
            </th>
            <th>
                <h1>Rangs Eceltronics Limited</h1>
                <h2>SERVICE CENTER</h2>
            </th>
            <th></th>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center;">
                <strong style="border:1px solid #000;padding:7px;">Bill</strong>
            </td>
            <td>
                <p>Branch</p>
                <p style="border: 1px solid #000;padding: 7px;">
                    <span>KUSTIA</span> <br>
                    <span>Tel:</span>
                    
                </p>
            </td>
        </tr>
    </table>
    <table border="1" class="t-details">
        <tr>
            <td>Name :</td>
            <td>Ziaur Rahaman</td>
            <td  class="emptyborder"></td>
            <td>Bill No:</td>
            <td>1</td>
            <td>Date: 12-12-12</td>
        </tr>
        <tr>
            <td rowspan="4">Address :</td>
            <td rowspan="4">213213213213</td>
            <td class="emptyborder"></td>
            <td>Item :</td>
            <td>CTV</td>
            <td>Brand: Sony</td>
        </tr>
        <tr>
            <td  class="emptyborder"></td>
            <td colspan="3">Model No :</td>
        </tr>
        <tr>
            <td  class="emptyborder"></td>
            <td colspan="3">Serial No :</td>
        </tr>
        <tr>
            <td  class="emptyborder"></td>
            <td colspan="3">Job No :</td>
        </tr>
    </table>
    <table style="margin-top: 50px;" class="memo-body" border="1">
        <tr>
            <th>SL No</th>
            <th>Part No</th>
            <th>Description</th>
            <th>Rate</th>
            <th>Qty.</th>
            <th>Amount in Taka</th>
        </tr>
        <tr>
            <td>1</td>
            <td>1212188</td>
            <td>SOCKET CRT</td>
            <td>800</td>
            <td>1</td>
            <td  style="text-align: center;">800</td>
        </tr>
        <tr>
            <td class="mfoot" colspan="5">Total Part Amount</td>
            <td style="text-align: center;">800</td>
        </tr>
        <tr>
            <td  class="mfoot" colspan="5">Fault Finding Charge</td>
            <td  style="text-align: center;">800</td>
        </tr>
        <tr>
            <td  class="mfoot" colspan="5">Service Charge</td>
            <td  style="text-align: center;">800</td>
        </tr>
        <tr>
            <td  class="mfoot" colspan="5">Other Charge</td>
            <td  style="text-align: center;">800</td>
        </tr>
        <tr>
            <td  class="mfoot" colspan="5">Adjustment</td>
            <td  style="text-align: center;">800</td>
        </tr>
        <tr>
            <td  class="mfoot" colspan="5">Advanced</td>
            <td  style="text-align: center;">800</td>
        </tr>
        <tr>
            <th  class="mfoot" colspan="5">Total</th>
            <th>8000000</th>
        </tr>
    </table>
    <table>
        <tr>
           <td> <p>Total Amount in Word: Taka one thousand one taka only </p></td>
           <td>
            <div style="margin-top: 50px;">
                <h2  style="text-align: center;">For Rangs Electronics Limited</h2>
                <p style="margin-top: 40px;border-top: 1px solid #000;text-align: center;">Signature</p>
            </div>
           </td>
        </tr>
    </table>
</body>
</html>