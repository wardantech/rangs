<table id="datatable" class="table">
    <thead>
        <tr>
            <th>{{ __('label.SERVICE')}}</th>
            <th>{{ __('label.PRODUCT_SERIAL')}}</th>
            <th>{{ __('label.PRODUCT_DATE')}}</th>
            <th>{{ __('label.PRODUCT_NAME')}}</th>
            <th>{{ __('label.PRODUCT_CODE')}}</th>
            <th>{{ __('label.BRAND_NAME')}}</th>
            <th>{{ __('label.MODEL_NAME')}}</th>
            <th>{{ __('label.LOCATION')}}</th>
            <th>{{ __('Action')}}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><input type="radio" id="html" name="fav_language" value=""></td>
            <td>1</td>
            <td>17 Dec 2021</td>
            <td>Tv</td>
            <td>P005</td>
            <td>Toshiba</td>
            <td>12541245</td>
            <td>Dhaka</td>
            <td> <a href="{{URL::to('tickets/ticket-create?customer_phone_number=1&product_code=P005')}}" class="btn btn-primary">  @lang('label.CREATE_NEW_TICKET')</a></td>
        
    </tbody>
</table>