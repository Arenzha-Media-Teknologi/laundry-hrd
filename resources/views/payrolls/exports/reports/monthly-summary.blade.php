<table>
    <thead>
        <tr>
            <th>Perusahaan</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($summaries as $summary)
        <tr>
            @if(isset($summary['company']['name']))
            <td>{{ $summary['company']['name'] }}</td>
            @else
            <td></td>
            @endif
            <td data-format="#,##0_-">{{ $summary['total'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td><strong>TOTAL</strong></td>
            <td data-format="#,##0_-"><strong>{{ collect($summaries)->sum('total') }}</strong></td>
        </tr>
    </tbody>
</table>