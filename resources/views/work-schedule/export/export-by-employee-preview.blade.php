<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Document</title>
</head>

<body>
    <table class="table">
        <thead>
            <tr>
                <td rowspan="2">Pegawai</td>
                <td rowspan="2">Jabatan</td>
                @foreach($periods as $date)
                <td colspan="2">{{ \Carbon\Carbon::parse($date)->isoFormat('LL') }}</td>
                @endforeach
                <td rowspan="2">Total Hari</td>
                <td rowspan="2">Rate / Hari</td>
                <td rowspan="2">Total Pendapatan</td>
            </tr>
            <tr>
                @foreach($periods as $date)
                <td>Shift</td>
                <td>Outlet</td>
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
                @else
                <?php
                $isOff = $schedule->is_off ?? 0;
                ?>
                @if($isOff != 0)
                <td colspan="2" style="background-color: red; text-align: center">Off</td>
                @else
                <?php
                $totalWorkDays += 1;
                ?>
                <td>{{ $schedule->workScheduleWorkingPattern->name ?? '-' }}</td>
                <td>{{ $schedule->office->name ?? '-' }}</td>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>