<table id="datatable" class="table">
    <thead>
        <tr>
            <th>Parts Info</th>
            <th>Stock In Hand</th>
            <th>Rack</th>
            <th>Bin</th>
            <th>Required Quantity</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stock_collect as $key=>$stock)
        <tr>
            <td> <input type="text" class="form-control" value="{{ $partInfo_collect[$key]->code }} -[{{ $partInfo_collect[$key]->name }}]" readonly></td>
            <td><input type="number" class="form-control" id="stock_in_hand-{{$key}}" name="stock_in_hand[]" value="{{ $stock }}" min="0" readonly></td>
            <td>
                <input type="text" class="form-control" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->rack->name : '' }}" readonly>
                <input type="hidden" class="form-control" name="rack_id[]" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->rack_id : '' }}" readonly>
            </td>
            <td>
                <input type="text" class="form-control" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->bin->name : '' }}" readonly>
                <input type="hidden" class="form-control" name="bin_id[]" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->bin_id : '' }}" readonly>
            </td>
            <td>
                <input type="number" class="form-control" id="required_quantity-{{$key}}" name="required_quantity[]" min="0" required>
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

</script>
