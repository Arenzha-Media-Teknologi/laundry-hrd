<table>
    <thead>
        <tr>
            <th>Daftar Karyawan - Periode {{ \Carbon\Carbon::parse($start_date)->isoFormat('LL') }} sd {{ \Carbon\Carbon::parse($end_date)->isoFormat('LL') }}</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>{{ $company->name ?? '' }}</th>
        </tr>
        <tr>
            <th>No.</th>
            <th>ID</th>
            <th>Perusahaan</th>
            <th>Divisi</th>
            <th>Nama</th>
            <th>Jenis Kelamin</th>
            <th>Tgl. Masuk</th>
            <th>NPWP</th>
            <th>Status PTKP</th>
            <th>Alamat Domisili</th>
            <th>HP</th>
            <th>Kontak Darurat</th>
        </tr>
    </thead>
    <tbody>
        @foreach($divisions as $division)
        <!-- <tr>
            <td colspan="10">{{ $division->name }} - {{ $division->company->name ?? '' }}</td>
        </tr> -->
        @foreach($division->employees as $employee)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $employee->number }}</td>
            <td>{{ $employee->office->division->company->name ?? '' }}</td>
            <td>{{ $employee->office->division->name ?? '' }}</td>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->gender == 'male' ? 'L' : 'P' }}</td>
            <td>{{ $employee->start_work_date }}</td>
            <td>{{ $employee->npwp_number }}</td>
            <td>{{ $employee->npwp_status }}</td>
            <td>{{ $employee->address }}</td>
            <td>{{ $employee->phone }}</td>
            <td>{{ $employee->emergency_contact_phone . '' }}</td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>