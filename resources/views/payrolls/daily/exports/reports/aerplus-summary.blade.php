<?php
$columnsCount = 2;
?>
<table>
    <thead>
        <tr>
            <td colspan="{{ $columnsCount }}" style="text-align: center;"><strong>Rekapitulasi Gaji Harian AerPlus (Summary)</strong></td>
        </tr>
        <tr>
            <td colspan="{{ $columnsCount }}" style="text-align: center;"><strong>Periode {{\Carbon\Carbon::parse($start_date)->format('d/m/Y')}} - {{\Carbon\Carbon::parse($end_date)->format('d/m/Y')}}</strong></td>
        </tr>
        <tr>
            <th><strong>Depot</strong></th>
            <th style="text-align: right;"><strong>Total Gaji</strong></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $grandTotal = 0;
        ?>
        @foreach($daily_salaries as $daily_salary)
        <tr>
            <td>{{ $daily_salary['depot'] ?? '' }}</td>
            <td data-format="#,##0_-">{{ $daily_salary['total'] ?? 0 }}</td>
        </tr>
        <?php
        $grandTotal += $daily_salary['total'] ?? 0;
        ?>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th><strong>TOTAL</strong></th>
            <th data-format="#,##0_-"><strong>{{ $grandTotal }}</strong></th>
        </tr>
    </tfoot>
</table>