<table id="datatable" class="table">
    <thead>
        <tr>
            <th>Parts Info</th>
            <th>Stock In Hand</th>
            <th>Required Quantity</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stock_collect as $key=>$stock)
        <tr>
            <td> <input type="text" class="form-control" value="{{ $partInfo_collect[$key]->code }} -[{{ $partInfo_collect[$key]->name }}]" readonly></td>
            <td><input type="number" class="form-control" id="stock_in_hand-{{$key}}" name="stock_in_hand[]" value="{{ $stock }}" min="0" readonly></td>
            <td>
                <input type="number" class="form-control" id="required_quantity-{{$key}}" name="required_quantity[]" onInput="warning({{$key}})" min="0" required>
                <input type="hidden" name="part_id[]" value="{{ $partInfo_collect[$key]->id }}">
                @error('required_quantity[]')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    function warning(key){
            var stock_in_hand=$('#stock_in_hand-'+key).val();
            var required_quantity=$('#required_quantity-'+key).val();

            var stock_qnty=parseInt(stock_in_hand);
            var required_qnty=parseInt(required_quantity);

            if(required_qnty > stock_qnty ) {
                alert('Whoops! Return Quantity is more than current stock');
                $('#required_quantity-'+key).val(null);
            }
        }
</script>
