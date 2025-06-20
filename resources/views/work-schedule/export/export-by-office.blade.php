<table>
    <tr>
        <td><strong>REKAPITULASI JADWAL KERJA PER OUTLET</strong></td>
    </tr>
    <tr>
        <td><strong>PERIODE {{ \Carbon\Carbon::parse($start_date)->isoFormat('LL') }} - {{ \Carbon\Carbon::parse($end_date)->isoFormat('LL') }}</strong></td>
    </tr>
    <tr>
        <td></td>
    </tr>
</table>
@foreach($offices as $office)
<table>
    <thead>
        <tr>
            <td rowspan="2" style="background-color: #B2DFDB;">{{ $office->name }}</td>
            @foreach($work_schedule_working_patterns as $work_schedule_working_pattern)
            <td style="text-align: center; background-color: #B2DFDB;" colspan="4">{{ $work_schedule_working_pattern->name }} ({{ $work_schedule_working_pattern->start_time }} - {{ $work_schedule_working_pattern->end_time }})</td>
            @endforeach
        </tr>
        <tr>
            @foreach($work_schedule_working_patterns as $work_schedule_working_pattern)
            <td style="background-color: #B2DFDB;">Pegawai</td>
            <td style="background-color: #B2DFDB;">Jabatan</td>
            <td style="background-color: #B2DFDB;">Status</td>
            <td style="background-color: #B2DFDB;">Keterlambatan</td>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($office->periods as $period)
        <?php
        $maxChildrenCount = collect($period['schedules'])->map(function ($schedule) {
            return collect($schedule)->count();
        })->max();
        ?>
        @for($i = 0; $i < $maxChildrenCount; $i++)
            <tr>
            @if($i == 0)
            <td>{{ $period['date'] }}</td>
            @else
            <td></td>
            @endif
            <!-- <td>{{ $maxChildrenCount }}</td> -->
            @foreach($period['schedules'] as $schedule)
            <td>{{ $schedule[$i]->employee->name ?? '' }}</td>
            <td>{{ $schedule[$i]->employee->activeCareer->jobTitle->name ?? '' }}</td>
            <?php
            $attendance = $attendances[$schedule[$i]->date ?? -1][$schedule[$i]->employee_id ?? -1][0] ?? null;
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
            @endforeach
            </tr>
            @endfor
            @endforeach
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
    </tbody>
</table>
@endforeach