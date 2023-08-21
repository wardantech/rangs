{{-- <tr>
    <td>
        <select name="" id="part_{{$row_id}}" data-row_id={{$row_id}} onchange="changeProduct({{$row_id}})" class="form-control col-sm-4">
            <option value="">Select Part</option>
            @foreach ($parts as $key=>$part)
                <option value="{{$part->id}}">{{$part->name}}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select name="" id="model_id_{{$row_id}}" class="form-control col-sm-4">

        </select>
    </td>
    <td>
        <select name="" id="rack_{{$row_id}}" class="form-control" onchange="getBin({{$row_id}})">
            @foreach ($racks as $rack)
                <option value="{{$rack->id}}">{{$rack->name}}</option>    
            @endforeach
        </select>
    </td>
    <td>
        <select name="" id="bin_{{$row_id}}" class="form-control col-sm-4">

        </select>
    </td>
    <td>
        <input type="number" class="form-control">
    </td>
</tr> --}}
{{-- <div class="ml-25">
    <label for="">SL</label>
    <div>{{$row_id}}</div>
</div> --}}
<div class="row ml-1">
    <div class="row col-sm-12">
        <div class="col-sm-3">
            <label for="" class="text-dark">Part Category</label>
            <select name="part_category_id[]" id="part_category_{{$row_id}}" data-row_id={{$row_id}} onchange="changePartCategory({{$row_id}})" class="form-control">
                <option value="">Select Part</option>
                @foreach ($partCategories as $key=>$partCategory)
                    <option value="{{$partCategory->id}}">{{$partCategory->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3">
            <label for="" class="text-dark">Part Model</label>
            <select name="model_id[]" id="model_id_{{$row_id}}" class="form-control" onchange="changeModel({{$row_id}})">
                <option value="">Select Part Model</option>
            </select>
        </div>
        <div class="col-sm-3">
            <label for="" class="text-dark">Part</label>
            <select name="part_id[]" id="part_id_{{$row_id}}" class="form-control" onchange="getPrice({{$row_id}})">
                <option value="">Select Part</option>
            </select>
        </div>
        <div class="col-sm-3">
            <label for="" class="text-dark">Rack</label>
            <select name="rack_id[]" id="rack_{{$row_id}}" class="form-control" onchange="getBin({{$row_id}})">
                <option value="">Select Rack</option>
                @foreach ($racks as $rack)
                
                    <option value="{{$rack->id}}">{{$rack->name}}</option>    
                @endforeach
            </select>
        </div>
    </div>
    <div class="row col-sm-12">
        <div class="col-sm-3">
            <label for="" class="text-dark">Bin</label>
            <select name="bin_id[]" id="bin_{{$row_id}}" class="form-control select2" multiple="multiple">
                <option value="">Select Bin</option>
            </select>
        </div>
        <div class="col-sm-2">
            <input type="hidden" name="price_management_id[]" id="price_management_id_{{$row_id}}">
            <label for="" class="text-dark">Cost Price (BDT)</label>
            <input type="number" name="bdt[]" id="cost_price_bdt_{{$row_id}}" class="form-control">
        </div>
        <div class="col-sm-2">
            <label for="" class="text-dark">Cost Price (USD)</label>
            <input type="number" name="usd[]" id="cost_price_usd_{{$row_id}}" class="form-control">
        </div>
        <div class="col-sm-2">
            <label for="" class="text-dark">Selling Price (BDT)</label>
            <input type="number" name="selling_price[]" id="selling_price_bdt_{{$row_id}}" class="form-control">
        </div>
        
        <div class="col-sm-3">
            <label for="" class="text-info">Receiving Quantity</label>
            <input type="number" name="quantity[]" class="form-control">
        </div>
    </div>
</div>
<hr>

<script>
    var row_id=$('#demo').val();
    $('#part_'+row_id).on('change', function(e){
        
            });

            //------------rack depedency--------------

            // function getRack(row_id){
            //     $('#store_'+row_id).on('change', function(){
            //     var store_id=$(this).val();
            //     if(store_id){
            //         $.ajax({
            //             url: "{{url('inventory/get/rack/')}}/"+store_id,
            //             type: 'GET',
            //             dataType: "json",
            //             success: function(data){
            //                 console.log(data);
            //                 var html = "<option value="+null+">Select Rack</option>";
            //                 $('#rack_'+row_id).empty();
            //                 $.each(data, function(key, value){
            //                     html += "<option value="+value.id+">"+value.name+"</option>";
            //                 });
            //                 $("#rack_"+row_id).append(html);
            //                 html = "";
            //             }
            //         });
            //     }
            // });
            // }

        //-----------Bin Depedency-------------

        // $('#rack_'+row_id).on('change', function(){
                
        //     });
        
        function getBin(row_id){
            var rack_id=$("#rack_"+row_id).val();
            
            // alert(rack_id);
                if(rack_id){
                    $.ajax({
                        url: "{{url('inventory/get/bin/')}}/"+rack_id,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
                            console.log(data);
                            var html = "<option value="+null+">Select Bin</option>";
                            $('#bin_'+row_id).empty();
                            $.each(data, function(key, value){
                                // $('#bin').append("<option value="+value.id+">"+value.name+"</option>");
                                html += "<option value="+value.id+">"+value.name+"</option>";
                            });
                            $("#bin_"+row_id).append(html);
                            html = "";
                        }
                    });
                }
        }


    function changePartCategory(row_id){
        var id = $('#part_'+row_id).val();
                // e.preventDefault();
                var part_category_id = $("#part_category_"+row_id).val();
                var drow_id=$(this).data('row_id');
                var url = "{{ url('inventory/model') }}";
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        id: part_category_id,
                    },
                    success: function(data) {
                        // $("#available_qty").val(data.stock);
                    var html = "<option value="+null+">Select Model</option>";
                    $("#model_id_"+row_id).empty();
                    $.each(data.partModels, function(key) {
                    //   console.log(data.recYarn_name[key].brand);
                        
                        html += "<option value="+data.partModels[key].id+">"+data.partModels[key].name+"</option>";
                    })
                    $("#model_id_"+row_id).append(html);
                    html = "";
                    }
                })
    }

    function changeModel(row_id){
        var id = $('#part_'+row_id).val();
                // e.preventDefault();
                var part_model_id = $("#model_id_"+row_id).val();
                var drow_id=$(this).data('row_id');
                var url = "{{ url('inventory/get-part') }}";
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        id: part_model_id,
                    },
                    success: function(data) {
                        // $("#available_qty").val(data.stock);
                    var html = "<option value="+null+">Select Model</option>";
                    $("#part_id_"+row_id).empty();
                    $.each(data.parts, function(key) {
                    //   console.log(data.recYarn_name[key].brand);
                        
                        html += "<option value="+data.parts[key].id+">"+data.parts[key].code+"</option>";
                    })
                    $("#part_id_"+row_id).append(html);
                    html = "";
                    }
                })
    }

    function getPrice(row_id){
        var part_id = $("#part_id_"+row_id).val();
        var model_id=$('#model_id_'+row_id).val();
        // alert("part: "+part_id+"model: "+model_id)
        $.ajax({
            type: "get",
            url: "{{url('get/price/')}}/"+part_id+"/"+model_id,
            data: {
                part_id: part_id,
                model_id: model_id,
            },
            success: function(data){
                // console.log(data)
                    $("#cost_price_bdt_"+row_id).val(data.cost_price_bdt);
                    $("#cost_price_usd_"+row_id).val(data.cost_price_usd);
                    $("#selling_price_bdt_"+row_id).val(data.selling_price_bdt);
                    $("#price_management_id_"+row_id).val(data.id);
                
            }
        })
    }

</script>