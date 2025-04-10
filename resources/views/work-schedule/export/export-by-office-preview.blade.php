<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Document</title>
</head>

<body>
    @foreach($offices as $office)
    <table class="table">
        <thead>
            <tr style="background-color: blue;">
                <td rowspan="2">{{ $office->name }}</td>
                @foreach($work_schedule_working_patterns as $work_schedule_working_pattern)
                <td style="text-align: center;" colspan="2">{{ $work_schedule_working_pattern->name }} ({{ $work_schedule_working_pattern->start_time }} - {{ $work_schedule_working_pattern->end_time }})</td>
                @endforeach
            </tr>
            <tr>
                @foreach($work_schedule_working_patterns as $work_schedule_working_pattern)
                <td>Pegawai</td>
                <td>Jabatan</td>
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
                @endforeach
                </tr>
                @endfor
                @endforeach
        </tbody>
        <tbody>

        </tbody>
    </table>
    @endforeach
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>