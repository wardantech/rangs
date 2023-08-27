<table id="datatable" class="table">
    <thead>
        <tr>
            <th>Parts Info</th>
            <th>Stock In Hand</th>
            <th>Used Quantity</th>
            <th>Unit Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stock_collect as $key=>$stock)
            <tr>
                <td> <input type="text" class="form-control" value="{{ $partInfo_collect[$key]->part->name }} -[{{ $partInfo_collect[$key]->name }}]" readonly></td>
                <td><input type="number" class="form-control" id="stock_in_hand" name="stock_in_hand[]" value="{{ $stock }}" min="0" readonly><input type="hidden" name="model_id[]" value="{{ $partInfo_collect[$key]->id }}"></td>
                <td><input type="number" class="form-control" id="used_quantity" name="used_quantity[]" min="0"><input type="hidden" name="part_id[]" value="{{ $partInfo_collect[$key]->part->id }}"></td>
                {{-- <td><input type="number" class="form-control" id="unit_price" name="selling_price_bdt[]" value="{{ $priceInfo[$key]->selling_price_bdt ? : '' }}" readonly><input type="hidden" name="price_management[]" value="{{ $partInfo_collect[$key]->id }}"></td> --}}
                <td><input type="number" class="form-control" id="unit_price" name="selling_price_bdt[]" value="{{ $partInfo_collect[$key]->part->price ? : '' }}" readonly><input type="hidden" name="price_management[]" value="{{ $partInfo_collect[$key]->id }}"></td>
                {{-- <td><input type="number" class="form-control" id="amount" name="required_quantity[]" value="{{ $partInfo_collect[$key]->part->price }} -[{{ $partInfo_collect[$key]->name }}]"><input type="hidden" name="part_id[]" value="{{ $partInfo_collect[$key]->part->id }}"></td> --}}
            </tr>
        @endforeach
    </tbody>
</table>