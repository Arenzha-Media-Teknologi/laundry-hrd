<table>
    <thead>
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>Nama Pemegang Polis</th>
            <th>Polis</th>
        </tr>
    </thead>
    <tbody>
        @foreach($divisions as $division)
        <tr>
            <td colspan="4">{{ $division->name }} - {{ $division->company->name ?? '' }}</td>
        </tr>
        @foreach($division->employees as $employee)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $employee->number }}</td>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->bpjs->mandiri_number ?? '' }}</td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>