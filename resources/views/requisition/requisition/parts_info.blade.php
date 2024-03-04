<table id="datatable" class="table">
    <thead>
        <tr>
            <th>Parts Info</th>
            <th>Model No</th>
            <th>Stock In Hand</th>
            <th>Required Quantity</th>
            <th>TSL No</th>
            <th>Purpose</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stock_collect as $key=>$stock)
        <tr>
            <td> <input type="text" class="form-control" value="{{ $partInfo_collect[$key]->code }} -[{{ $partInfo_collect[$key]->name }}]" readonly></td>
            <td>
                <input type="text" class="form-control" name="model_no[]" value="" >
            </td>
            <td><input type="number" class="form-control" id="stock_in_hand-{{$key}}" name="stock_in_hand[]" value="{{ $stock }}" min="0" readonly></td>
            <td>
                <input type="number" class="form-control" id="required_quantity-{{$key}}" name="required_quantity[]" min="0" required>
                <input type="hidden" name="part_id[]" value="{{ $partInfo_collect[$key]->id }}">
                @error('required_quantity[]')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </td>
            <td><input type="text" name="tsl_no[]" id="" class="form-control" value=""></td>
            <td>
                <select name="purpose[]" id="" class="form-control">
                    <option value="1">On Payment</option>
                    <option value="2">Under Warranty</option>
                    <option value="3">Stock</option>
                </select>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>

</script>
