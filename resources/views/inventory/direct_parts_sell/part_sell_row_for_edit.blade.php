<table id="datatable" class="table">
    <thead>
        <tr>
            <th>Parts Info</th>
            <th>Stock In Hand</th>
            <th>Selling Quantity</th>
            <th>Unit Price</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($partInfo_collect as $key=>$stock)
            <tr>
                <td>
                    <input type="text" class="form-control" value="{{ $partInfo_collect[$key]->code }} -[{{ $partInfo_collect[$key]->name }}]" readonly>
                </td>
                <td>
                    <input type="number" class="form-control" data-id="{{$key}}" id='stock_in_hand-{{$partInfo_collect[$key]->code}}' name="stock_in_hand[]" value="{{ $stock_collect[$key] }}" min="0" readonly>
                </td>
                <td><input type="text" class="form-control" name="quantity[]" id="quantity_{{$partInfo_collect[$key]->code}}" @if(isset($selectedQuantity[$key]->quantity)) value="{{$selectedQuantity[$key]->quantity}}" @endif min="0" onkeyup="getAmount(`{{$partInfo_collect[$key]->code}}`)" onInput="warning(`{{$partInfo_collect[$key]->code}}`)">
                    <input type="hidden" name="part_id[]" value="{{ $partInfo_collect[$key]->id }}">
                </td>
                <td>
                    <input type="number" name="selling_price[]" id="selling_price_bdt_{{$partInfo_collect[$key]->code}}" value="{{$priceInfo[$key]->selling_price_bdt}}" class="form-control">
                </td>
                <td>
                    <input type="number" name="amount[]" id="amount_{{$partInfo_collect[$key]->code}}" class="amount form-control" @if(isset($amount[$key]->amount)) value="{{$amount[$key]->amount}}" @endif readonly>
                </td>
            </tr>
        @endforeach
            <tr>
                <td>Spare Parts Amount:</td>
                <td><input type="number" name="spare_parts_amount" id="spare-parts-amount" class="form-control" value="{{$partSell->spare_parts_amount}}" readonly></td>
            </tr>
            <tr>
                <td>Discount: </td>
                <td><input type="text" name="discount" id="discount" class="form-control" value="{{$partSell->discount}}" onkeyup="getNetDiscountAmount()"></td>
            </tr>
            <tr>
                <td>Net Amount: </td>
                <td><input type="number" name="net_amount" id="net-amount" class="form-control" value="{{$partSell->net_amount}}" readonly></td>
            </tr>
    </tbody>
</table>

<script>
    $(document).ready(function () {
        $('.multi_bin').select2({
            placeholder: "seeee",
        });
    });

    function warning(key){
            var stock_in_hand=$('#stock_in_hand-'+key).val();
            // var required_quantity=$('#required_quantity-'+key).val();
            var issue_quantity=$('#quantity_'+key).val();

            var stock_qnty=parseInt(stock_in_hand);
            // var required_qnty=parseInt(required_quantity);
            var issue_qnty=parseInt(issue_quantity);
            console.log(stock_in_hand);

            if(issue_qnty>stock_qnty ) {
                alert('Whoops! Selling Quantity is more than current stock');
                $('#quantity_'+key).val(null);

            }
            // else if(issue_qnty>required_qnty){
            //     alert('Whoops! Issuing Quantity is more than Required Quantity');
            //     $('#issue_quantity-'+key).val(null);
            // }
        }

    var netAmount=[];
    var sum = 0;


    function getAmount(part_code){
        var quantity= $('#quantity_'+part_code).val();
        var unitPrice= $('#selling_price_bdt_'+part_code).val();
        // console.log(unitPrice);
        $('#amount_'+part_code).val(quantity*unitPrice);
        // netAmount.push( $('#amount_'+part_code).val(quantity*unitPrice));
        // console.log($('#amount_'+part_code).val());
        // console.log(netAmount);
        // console.log($("#amount_"+part_code).val());
        // var sum= $("#amount_"+part_code).val();
        // netAmount.push(sum);

        var sum=0;
        $(".amount").each(function(){
            if($(this).val() !== "")
            sum += parseInt($(this).val(), 10);
        });

        // $("#result").html(sum);
        var discount= $('#discount').val();
        console.log(sum);
        $('#spare-parts-amount').val(sum);
        // $('#net-amount').val(sum);
        $('#net-amount').val(sum - discount);
    }

    function getNetDiscountAmount(){
        var sum=0;
        $(".amount").each(function(){
            if($(this).val() !== "")
            sum += parseInt($(this).val(), 10);
        });
        // var netAmount= $('#net-amount').val();
        var discount= $('#discount').val();
        // $('#net-amount').val(netAmount - discount);
        $('#net-amount').val(sum - discount);
    }

    // function getNetAmount(part_code){
    //     var amount= $('#amount_'+part_code).val();
    //     netAmount.push(amount);
    //     for (let i = 0; i < netAmount.length; i++) {
    //     sum += netAmount[i];
    // }
    //     console.log(sum);
    // }

    // $(".amount").on("blur", function(){
    //     var sum=0;
    //     $(".amount").each(function(){
    //         if($(this).val() !== "")
    //         sum += parseInt($(this).val(), 10);
    //     });

    //     // $("#result").html(sum);
    //     console.log(sum);
    // });


</script>
