<table id="datatable" class="table">
    <thead>
        <tr>
            <th>Parts Info</th>
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
                    <input type="text" class="form-control" value="{{ $partInfo_collect[$key]->code }} -[{{ $partInfo_collect[$key]->name }}]" readonly>
                </td>
                <td>
                    <select name="rack_id[]" id="rack_{{$partInfo_collect[$key]->code}}" class="form-control" onchange="getBin({{$partInfo_collect[$key]->code}})">
                        <option value="">Select Rack</option>
                        @foreach ($racks as $rack)
                            <option value="{{$rack->id}}" 
                                @if(isset($selectedRackIds[$key]->rack_id))
                                    @if($selectedRackIds[$key]->rack_id==$rack->id)
                                        selected
                                    @endif
                                @endif
                                >
                                {{$rack->name}}
                            </option>    
                        @endforeach
                    </select>
                </td>
                <td width="100">
                    <select name="bin_id_{{$key}}[]" id="bin_{{$partInfo_collect[$key]->code}}" class="form-control multi_bin" multiple="multiple">
                        <option value="">Select Bin</option>
                        @foreach($bins as $bin)
                            <option value="{{$bin->id}}"
                                @if(isset($selectedBinIds[$key]->bin_id))
                                <?php $binIds=json_decode($selectedBinIds[$key]->bin_id)?>
                                
                                    @if(in_array($bin->id, $binIds))
                                        selected
                                    @endif
                                @endif
                                >
                                {{$bin->name}}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="hidden" name="price_management_id[]" id="price_management_id_{{$partInfo_collect[$key]->code}}" value="{{$priceInfo[$key]->id}}">
                    <input type="number" name="bdt[]" id="cost_price_bdt_{{$partInfo_collect[$key]->code}}" value="{{$priceInfo[$key]->cost_price_bdt}}" class="form-control">
                </td>
                <td>
                    <input type="number" name="usd[]" id="cost_price_usd_{{$partInfo_collect[$key]->code}}" value="{{$priceInfo[$key]->cost_price_usd}}" class="form-control">
                </td>
                <td>
                    <input type="number" name="selling_price[]" id="selling_price_bdt_{{$partInfo_collect[$key]->code}}" value="{{$priceInfo[$key]->selling_price_bdt}}" class="form-control">
                </td>
                <td><input type="number" class="form-control" name="quantity[]" @if(isset($selectedReceivingQuantity[$key]->stock_in)) value="{{$selectedReceivingQuantity[$key]->stock_in}}" @endif min="0">
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