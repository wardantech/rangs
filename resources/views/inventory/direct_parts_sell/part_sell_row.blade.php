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
                <?php $code= "code_".$partInfo_collect[$key]->code?>
                <td><input type="text" class="form-control" name="quantity[]" id="quantity_{{$partInfo_collect[$key]->code}}" min="0" onkeyup="getAmount(`{{$partInfo_collect[$key]->code}}`)" onInput="warning(`{{$partInfo_collect[$key]->code}}`)">
                    <input type="hidden" name="part_id[]" value="{{ $partInfo_collect[$key]->id }}">
                </td>
                <td>
                    <input type="number" name="selling_price[]" id="selling_price_bdt_{{$partInfo_collect[$key]->code}}" value="{{$priceInfo[$key]->selling_price_bdt ?? null}}" class="form-control" readonly>
                </td>
                <td>
                    <input type="number" name="amount[]" id="amount_{{$partInfo_collect[$key]->code}}" class="amount form-control" value="0" readonly>
                </td>
            </tr>
        @endforeach
            <tr>
                <td>Spare Parts Amount:</td>
                <td><input type="number" name="spare_parts_amount" id="spare-parts-amount" class="form-control" readonly></td>
            </tr>
            <tr>
                <td>Discount: </td>
                <td><input type="text" name="discount" id="discount" class="form-control" value="0" onkeyup="getNetDiscountAmount()"></td>
            </tr>
            <tr>
                <td>Net Amount: </td>
                <td><input type="number" name="net_amount" id="net-amount" class="form-control" readonly></td>
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
            var issue_quantity=$('#quantity_'+key).val();

            var stock_qnty=parseInt(stock_in_hand);
            var issue_qnty=parseInt(issue_quantity);

            if(issue_qnty>stock_qnty ) {
                alert('Whoops! Selling Quantity is more than current stock');
                $('#quantity_'+key).val(null);
            }
        }

    var netAmount=[];
    var sum = 0;


    function getAmount(part_code){

        var quantity= $('#quantity_'+part_code).val();
        var unitPrice= $('#selling_price_bdt_'+part_code).val();

        $('#amount_'+part_code).val(quantity*unitPrice);

        var sum=0;
        $(".amount").each(function(){
            if($(this).val() !== "")
            sum += parseInt($(this).val(), 10);
        });


        var discount= $('#discount').val();

        $('#spare-parts-amount').val(sum);
        $('#net-amount').val(sum - discount);
        $('#net-amount').val(sum);
    }

    function getNetDiscountAmount(){
        var sum=0;
        $(".amount").each(function(){
            if($(this).val() !== "")
            sum += parseInt($(this).val(), 10);
        });

        var discount= $('#discount').val();

        $('#net-amount').val(sum - discount);
    }


</script>
