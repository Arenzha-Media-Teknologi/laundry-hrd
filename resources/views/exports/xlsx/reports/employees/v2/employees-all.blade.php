<table>
  <thead>
    <tr>
      <th><strong>Daftar Karyawan - Periode {{ \Carbon\Carbon::parse($start_date)->isoFormat('LL') }} sd {{ \Carbon\Carbon::parse($end_date)->isoFormat('LL') }}</strong></th>
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
      <th><strong>{{ $company->name ?? '' }}</strong></th>
    </tr>
  </thead>
</table>
@foreach($divisions as $division)
<table>
  <thead>
    <tr>
      <th><strong><em>{{ !isset($company) ? $division->company->name . ' - ' . $division->name : $division->name }}</em></strong></th>
    </tr>
    <tr>
      <th rowspan="2">No.</th>
      <th rowspan="2">ID</th>
      <th rowspan="2">Perusahaan</th>
      <th rowspan="2">Divisi</th>
      <th rowspan="2">Departemen</th>
      <th rowspan="2">Bagian</th>
      <th rowspan="2">Job Title</th>
      <th rowspan="2">Kantor</th>
      <th rowspan="2">Nama</th>
      <th rowspan="2">Tgl. Lahir</th>
      <th rowspan="2">Jenis Kelamin</th>
      <th rowspan="2">Tgl. Masuk</th>
      <th rowspan="2">NPWP</th>
      <th rowspan="2">Status PTKP</th>
      <th rowspan="2">Alamat Domisili</th>
      <th rowspan="2">HP</th>
      <th rowspan="2">Kontak Darurat</th>
      <th rowspan="2">Status</th>
      <th rowspan="2">Rek Bank</th>
      <th rowspan="2">Nama Pemilik Rek Bank</th>
      <th rowspan="2">Nama Bank</th>
      <th rowspan="2">Username</th>
      <th colspan="8" style="text-align: center;">Nilai Gaji</th>
    </tr>
    <tr>
      <td>Gaji Pokok</td>
      <td>Tunjangan</td>
      <td>Uang Harian</td>
      <td>Koef. Uang Harian</td>
      <td>Lembur</td>
      <td>Koef. Lembur</td>
      <td>Tunjangan Harian</td>
      <td>Uang Makan</td>
    </tr>
  </thead>
  <tbody>
    <?php $columnsCount = 15; ?>
    @if(count($division->employees) > 0)
    @foreach($division->employees as $employee)
    <tr>
      <td>{{ $loop->iteration }}</td>
      <td>{{ $employee->number }}</td>
      <td>{{ $employee->office->division->company->name ?? '' }}</td>
      <td>{{ $employee->office->division->name ?? '' }}</td>
      <td>{{ $employee->activeCareer->jobTitle->designation->department->name ?? '' }}</td>
      <td>{{ $employee->activeCareer->jobTitle->designation->name ?? '' }}</td>
      <td>{{ $employee->activeCareer->jobTitle->name ?? '' }}</td>
      <td>{{ $employee->office->name ?? '' }}</td>
      <td>{{ $employee->name }}</td>
      <td>{{ $employee->date_of_birth }}</td>
      <td>{{ $employee->gender == 'male' ? 'L' : 'P' }}</td>
      <td>{{ $employee->start_work_date }}</td>
      <td>{{ $employee->npwp_number }}</td>
      <td>{{ $employee->npwp_status }}</td>
      <td>{{ $employee->address }}</td>
      <td>{{ $employee->phone }}</td>
      <td>{{ $employee->emergency_contact_phone . '' }}</td>
      <td>{{ $employee->active == 1 ? 'Aktif' : 'Nonaktif' }}</td>
      <td>{{ $employee->bankAccounts[0]->account_number ?? '' }}</td>
      <td>{{ $employee->bankAccounts[0]->account_owner ?? '' }}</td>
      <td>{{ $employee->bankAccounts[0]->bank_name ?? '' }}</td>
      <td>{{ $employee->credential->username ?? '' }}</td>
      <?php
      $salaryTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'lembur', 'tunjangan_harian', 'uang_makan'];
      $employeeSalaryValues = collect($salaryTypes)->mapWithKeys(function ($salaryType) use ($employee) {
        $component = collect($employee->salaryComponents)->where('salary_type', $salaryType)->first();
        $value = $component->pivot->amount ?? 0;
        $coefficient = $component->pivot->coefficient ?? 1;
        return [
          $salaryType => [
            'value' => $value,
            'coefficient' => $coefficient,
          ],
        ];
      })->all();
      ?>
      <td data-format="#,##0_-">{{ $employeeSalaryValues['gaji_pokok']['value'] }}</td>
      <td data-format="#,##0_-">{{ $employeeSalaryValues['tunjangan']['value'] }}</td>
      <td data-format="#,##0_-">{{ $employeeSalaryValues['uang_harian']['value'] }}</td>
      <td>{{ $employeeSalaryValues['uang_harian']['coefficient'] }}</td>
      <td data-format="#,##0_-">{{ $employeeSalaryValues['lembur']['value'] }}</td>
      <td>{{ $employeeSalaryValues['lembur']['coefficient'] }}</td>
      <td data-format="#,##0_-">{{ $employeeSalaryValues['tunjangan_harian']['value'] }}</td>
      <td data-format="#,##0_-">{{ $employeeSalaryValues['uang_makan']['value'] }}</td>
    </tr>
    @endforeach
    @else
    <tr>
      <td colspan="{{ $columnsCount }}" style="text-align: center;"><strong><em>Tidak Ada Data</em></strong></td>
    </tr>
    @endif
  </tbody>
</table>
@endforeach