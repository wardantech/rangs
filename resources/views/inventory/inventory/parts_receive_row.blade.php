<table id="datatable" class="table">
    <thead>
        <tr>
            <th>Parts Information</th>
            <th>Rack</th>
            <th>Bin</th>
            <th>Cost Price (BDT)</th>
            <th>Cost Price (USD)</th>
            <th>Selling Price (BDT)</th>
            <th>Receiving Quantity</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($partInfo_collect as $key=>$stock)
            <tr>
                <td>
                    <input type="text" class="form-control" value="{{ $partInfo_collect[$key]->code }}-{{ $partInfo_collect[$key]->name }}" readonly>
                </td>
                <td>
                    <input type="text" class="form-control" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->rack->name : '' }}" readonly>
                    <input type="hidden" class="form-control" name="rack_id[]" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->rack_id : '' }}" readonly>
                </td>
                <td>
                    <input type="text" class="form-control" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->bin->name : '' }}" readonly>
                    <input type="hidden" class="form-control" name="bin_id[]" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->bin_id : '' }}" readonly>
                </td>
                <td>
                    <input type="hidden" name="price_management_id[]" id="price_management_id_{{$partInfo_collect[$key]->code}}" value="{{$priceInfo[$key]->id ?? null}}">
                    <input type="number" name="cost_price_bdt[]" id="cost_price_bdt_{{$partInfo_collect[$key]->code}}" value="{{$priceInfo[$key]->cost_price_bdt ?? null}}" class="form-control" min="0" step="any">
                </td>
                <td>
                    <input type="number" name="cost_price_usd[]" id="cost_price_usd_{{$partInfo_collect[$key]->code}}" value="{{$priceInfo[$key]->cost_price_usd ?? null}}" class="form-control" min="0" step="any">
                </td>
                <td>
                    <input type="number" name="selling_price_bdt[]" id="selling_price_bdt_{{$partInfo_collect[$key]->code}}" value="{{$priceInfo[$key]->selling_price_bdt ?? null}}" class="form-control" min="0" step="any">
                </td>
                <td><input type="number" class="form-control" name="quantity[]" min="0" required>
                    <input type="hidden" name="part_id[]" value="{{ $partInfo_collect[$key]->id }}"></td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    $(document).ready(function () {
        $('.multi_bin').select2({
            placeholder: "seeee",
        });
    });
        function getBin(row_id){
            var rack_id=$("#rack_"+row_id).val();
                if(rack_id){
                    $.ajax({
                        url: "{{url('inventory/get/bin/')}}/"+rack_id,
                        type: 'GET',
                        
                        dataType: "json",
                        success: function(data){
                            var html = "<option value="+null+">Select Bin</option>";
                            $('#bin_'+row_id).empty();
                            $.each(data, function(key, value){
                                html += "<option value="+value.id+">"+value.name+"</option>";
                            });
                            $("#bin_"+row_id).append(html);
                            html = "";
                        }
                    });
                }
        }
</script>