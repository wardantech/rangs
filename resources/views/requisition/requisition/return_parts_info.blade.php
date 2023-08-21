<table id="datatable" class="table">
    <thead>
        <tr>
            <th>Parts Info</th>
            <th>Stock In Hand</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stock_collect as $key=>$stock)
            <tr>
                <td> <input type="text" class="form-control" value="{{ $partInfo_collect[$key]->part->name }} -[{{ $partInfo_collect[$key]->name }}]" readonly></td>
                <td><input type="number" class="form-control" name="stock_in_hand[]" value="{{ $stock }}" min="0" readonly><input type="hidden" name="model_id[]" value="{{ $partInfo_collect[$key]->id }}"></td>
                <td><input type="number" class="form-control" name="quantity[]" min="0"><input type="hidden" name="part_id[]" value="{{ $partInfo_collect[$key]->part->id }}"></td>
            </tr>
        @endforeach
    </tbody>
</table>