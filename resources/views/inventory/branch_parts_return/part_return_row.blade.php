<table id="datatable" class="table">
    <thead>
        <tr>
            <th>Parts Info</th>
            <th>Stock In Hand</th>
            <th>Rack</th>
            <th>Bin</th>
            <th>Return Quantity</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($partInfo_collect as $key=>$stock)
            <tr>
                <td>
                    <input type="text" class="form-control" value="{{ $partInfo_collect[$key]->code }}-[{{ $partInfo_collect[$key]->name }}]" readonly>
                </td>
                <td>
                    <input type="number" class="form-control" data-id="{{$key}}" id='stock_in_hand-{{$partInfo_collect[$key]->code}}' name="stock_in_hand[]" value="{{ $stock_collect[$key] }}" min="0" readonly>
                </td>
                <td>
                    <input type="text" class="form-control" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->rack->name : '' }}" readonly>
                    <input type="hidden" class="form-control" name="rack_id[]" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->rack_id : '' }}" readonly>
                </td>
                <td>
                    <input type="text" class="form-control" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->bin->name : '' }}" readonly>
                    <input type="hidden" class="form-control" name="bin_id[]" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->bin_id : '' }}" readonly>
                </td>
                <?php $code= "code_".$partInfo_collect[$key]->code?>
                <td><input type="text" class="form-control" name="quantity[]" id="quantity_{{$partInfo_collect[$key]->code}}" min="0" onInput="warning(`{{$partInfo_collect[$key]->code}}`)" min="0" required>
                    <input type="hidden" name="part_id[]" value="{{ $partInfo_collect[$key]->id }}">
                </td>
            </tr>
        @endforeach

    </tbody>
</table>

<script>
    function warning(key){
            var stock_in_hand=$('#stock_in_hand-'+key).val();
            var issue_quantity=$('#quantity_'+key).val();

            var stock_qnty=parseInt(stock_in_hand);
            var issue_qnty=parseInt(issue_quantity);

            if(issue_qnty>stock_qnty ) {
                alert('Whoops! Return Quantity Is More Than Current Stock');
                $('#quantity_'+key).val(null);
            }
        }
</script>
