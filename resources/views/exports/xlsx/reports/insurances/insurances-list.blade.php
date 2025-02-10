<table>
    <thead>
        <tr>
            <th>Daftar Asuransi Periode Thn {{ $year }}</th>
            @for($i = 0; $i < 5; $i++) <th>
                </th>
                @endfor
                <th>{{ $company->name ?? '' }}</th>
        </tr>
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>Nama Pemegang Polis</th>
            <th>Polis</th>
            <th>Tahun Mulai Polis</th>
            <th>Jenis Polis</th>
            <th>Nominal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($divisions as $division)
        <tr>
            <td colspan="7">{{ $division->name }} - {{ $division->company->name ?? '' }}</td>

        </tr>
        @foreach($division->employees as $employee)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $employee->number }}</td>
            <td>{{ $employee->name }}</td>
            @if(isset($employee->insurance_values[0]))
            <td>{{ $employee->insurance_values[0]['number'] ?? '' }}</td>
            <td>{{ $employee->insurance_values[0]['start_year'] ?? '' }}</td>
            <td>{{ $employee->insurance_values[0]['name'] ?? '' }}</td>
            <td data-format="#,##0_-">{{ ($employee->insurance_values[0]['value'] ?? 0) }}</td>
            @else
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            @endif
        </tr>
        @if(count($employee->insurance_values) > 1)
        @foreach($employee->insurance_values as $index => $insuranceValue)
        @if($index > 0)
        <tr>
            <td colspan="3"></td>
            <td>{{ $insuranceValue['number'] ?? '' }}</td>
            <td>{{ $insuranceValue['start_year'] ?? '' }}</td>
            <td>{{ $insuranceValue['name'] ?? '' }}</td>
            <td data-format="#,##0_-">{{ ($insuranceValue['value'] ?? 0) }}</td>
        </tr>
        @endif
        @endforeach
        @endif
        @endforeach
        <tr>
            <td colspan="6">Sub-Total</td>
            <td data-format="#,##0_-">{{ ($division->subtotal ?? 0) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6">Grand-Total</td>
            <td data-format="#,##0_-">{{ ($grand_total ?? 0) }}</td>
        </tr>
    </tfoot>
</table>