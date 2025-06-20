<table class="table">
    <thead>
        <tr>
            <td><strong>REKAPITULASI JADWAL PEGAWAI</strong></td>
        </tr>
        <tr>
            <td><strong>PERIODE {{ \Carbon\Carbon::parse($start_date)->isoFormat('LL') }} - {{ \Carbon\Carbon::parse($end_date)->isoFormat('LL') }}</strong></td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2">Pegawai</td>
            <td rowspan="2">Jabatan</td>
            @foreach($periods as $date)
            <td colspan="4" style="text-align: center;">{{ \Carbon\Carbon::parse($date)->isoFormat('LL') }}</td>
            @endforeach
            <td rowspan="2">Total Hari</td>
            <td rowspan="2">Rate / Hari</td>
            <td rowspan="2">Total Pendapatan</td>
        </tr>
        <tr>
            @foreach($periods as $date)
            <td>Shift</td>
            <td>Outlet</td>
            <td>Status</td>
            <td>Keterlambatan (Menit)</td>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)
        <?php
        $totalWorkDays = 0;
        ?>
        <tr>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->activeCareer->jobTitle->name ?? '-' }}</td>
            @foreach($employee->schedules as $schedule)
            @if($schedule == null)
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            @else
            <?php
            $isOff = $schedule->is_off ?? 0;
            ?>
            @if($isOff != 0)
            <td colspan="4" style="background-color: red; text-align: center">Off</td>
            @else
            <?php
            $totalWorkDays += 1;
            ?>
            <td>{{ $schedule->workScheduleWorkingPattern->name ?? '-' }}</td>
            <td>{{ $schedule->office->name ?? '-' }}</td>
            <?php
            $attendance = $attendances[$schedule->date][$schedule->employee_id][0] ?? null;
            ?>
            @if($attendance != null)
            <td>
                @if($attendance->status == "hadir")
                Hadir
                @elseif($attendance->status == "izin")
                Izin
                @elseif($attendance->status == "sakit")
                Sakit
                @elseif($attendance->status == "cuti")
                Cuti
                @endif
            </td>
            <td>
                @if($attendance->time_late > 0)
                {{ $attendance->time_late }}
                @endif
            </td>
            @else
            <td></td>
            <td></td>
            @endif
            @endif
            @endif
            @endforeach
            <td>{{ $totalWorkDays }}</td>
            <td data-format="#,##0_-">{{ $employee->daily_wage }}</td>
            <td data-format="#,##0_-">{{ $totalWorkDays * $employee->daily_wage }}</td>
        </tr>
        @endforeach
    </tbody>
</table>