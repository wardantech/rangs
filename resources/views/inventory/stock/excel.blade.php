<table>
	<thead>
		<tr>
			<th colspan="6">Improve Accessories Limited</th>
		</tr>
		<tr>
			<th colspan="6">
				Plot# 529/1, Jazor Bazar, Bissho Road, Bypass, National University, Gazipur, Bangladesh
			</th>
		</tr>
		<tr>
			<th colspan="6">Quotation List of Improve Poly</th>
		</tr>		
		<tr>
            <th>{{ __('label.SL')}}</th>
            <th>{{ __('label.PARTS CODE')}}</th>
            <th>{{ __('Part Decription')}}</th>
            <th>{{ __('label.MODEL')}}</th>
            <th>{{ __('label.PRESENT_BALANCE_QNTY')}}</th>
        </tr>
	</thead>
	<tbody>
		@foreach($raws as $item)
		<tr>
            <td>$loop->iteration</td>
            <td>$item->code</td>
            <td>$item->name</td>
            <td>$item->partModel->name</td>
            <td>balance</td>
		</tr>
		@endforeach
	</tbody>
	<tfoot>
		<tr>
            <th colspan="6">report generated on: {{ $date }}</th>
        </tr>
	</tfoot>
</table>
