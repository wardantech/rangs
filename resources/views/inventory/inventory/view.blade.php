<div class="modal-header">
    <h5 class="modal-title" id="demoModalLabel">{{__('label.INVENTORY_DETAILS')}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <table class="table table-striped table-bordered table-hover">
        
        <tr>
            <th><strong>{{trans('label.STORE')}}</strong></th>
            <td>{{$target->storename}}</td>
        </tr> 
        
        <tr>
            <th><strong>{{trans('label.PART')}}</strong></th>
            <td>{{$target->partname}}</td>
        </tr>
        <tr>
            <th><strong>{{trans('label.ORDER_DATE')}}</strong></th>
            <td>{{$target->order_date->format('m/d/Y')}}</td>
        </tr>
        <tr>
            <th><strong>{{trans('label.RECEIVE_DATE')}}</strong></th>
            <td>{{$target->receive_date->format('m/d/Y')}}</td>
        </tr>
        <tr>
            <th><strong>{{trans('label.USD')}}</strong></th>
            <td>{{$target->usd}}</td>
        </tr>
        <tr>
            <th><strong>{{trans('label.BDT')}}</strong></th>
            <td>{{$target->bdt}}</td>
        </tr>

        <tr>
            <th><strong>{{trans('label.SELLING_PRICE')}}</strong></th>
            <td>{{$target->selling_price}}</td>
        </tr>
        <tr>
            <th><strong>{{trans('label.BIN')}}</strong></th>
            <td>{{$target->binname}}</td>
        </tr>
        <tr>
            <th><strong>{{trans('label.RACK')}}</strong></th>
            <td>{{$target->rackname}}</td>
        </tr> 
        <tr>
            <th><strong>{{trans('label.VENDOR')}}</strong></th>
            <td>{{$target->vendorsname}}</td>
        </tr>
       
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>