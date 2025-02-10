<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Pengajuan Lembur</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10px;
        }

        .table {
            border-collapse: collapse;
        }

        table.table-bordered tr td {
            border: 1px solid #000;
            padding: 2px;
        }
    </style>
</head>

<body>
    <div style="margin-bottom: 10px;">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h4>SURAT PERINTAH LEMBUR - METAPRINT</h4>
                </td>
                <td style="text-align: right;">
                    <h4>No. {{ $overtime_application->number }}</h4>
                </td>
            </tr>
        </table>
    </div>
    <div style="width: 100%;">
        <div style="width: 100%; overflow: hidden;">
            <div style="width: 45%; float: left;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 40%;">TGL / HARI</td>
                        <td style="width: 10%;">:</td>
                        <td style="width: 50%;">{{ \Carbon\Carbon::parse($overtime_application->date)->format('d/m/Y') }} / {{ \Carbon\Carbon::parse($overtime_application->date)->locale('id')->dayName }}</td>
                    </tr>
                </table>
            </div>
            <div style="width: 45%; float: right;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 40%;">Jenis Lemburan</td>
                        <td style="width: 10%;">:</td>
                        <td style="width: 50%; text-transform: capitalize;">{{ $overtime_application->type }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    @if($overtime_application->type == "other")
    <div style="width: 100%;">
        <div style="width: 100%; overflow: hidden;">
            <div style="width: 45%; float: left;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 40%;">Judul</td>
                        <td style="width: 10%;">:</td>
                        <td style="width: 50%;">{{ $overtime_application->title }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    @else
    <div style="width: 100%;">
        <div style="width: 100%; overflow: hidden;">
            <div style="width: 45%; float: left;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 40%;">Job Order #</td>
                        <td style="width: 10%;">:</td>
                        <td style="width: 50%;">{{ $overtime_application->job_order_number }}</td>
                    </tr>
                    <tr>
                        <td>Pesanan</td>
                        <td>:</td>
                        <td>{{ $overtime_application->order }}</td>
                    </tr>
                    <tr>
                        <td>Delivery</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($overtime_application->delivery_date)->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </div>
            <div style="width: 45%; float: right;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 40%;">Pelanggan</td>
                        <td style="width: 10%;">:</td>
                        <td style="width: 50%;">{{ $overtime_application->customer }}</td>
                    </tr>
                    <tr>
                        <td>Qty Order</td>
                        <td>:</td>
                        <td>{{ $overtime_application->order_quantity }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    @endif
    <div style="margin-top: 30px;">
        <table style="width: 100%; text-align: center;" class="table table-bordered">
            <thead>
                <tr>
                    <td rowspan="2" colspan="3">Nama Peserta Lembur</td>
                    <td rowspan="2">Keterangan</td>
                    <td colspan="3">Jam Pengajuan</td>
                    <td colspan="3">Jam Sistem</td>
                    <td rowspan="2">Selisih</td>
                    <td rowspan="2">Paraf</td>
                </tr>
                <tr>
                    <td>Masuk</td>
                    <td>Keluar</td>
                    <td>Jumlah</td>
                    <td>Masuk</td>
                    <td>Keluar</td>
                    <td>Jumlah</td>
                </tr>
            </thead>
            <tbody>
                @foreach($member_pic as $pic)
                <tr>
                    <td>PIC</td>
                    <td>:</td>
                    <td>{{ $pic['employee'] }}</td>
                    <td>{{ $pic['description'] }}</td>
                    <td>{{ $pic['clockIn'] }}</td>
                    <td>{{ $pic['clockOut'] }}</td>
                    <td>{{ $pic['overtime'] }}</td>
                    <td>{{ $pic['systemClockIn'] }}</td>
                    <td>{{ $pic['systemClockOut'] }}</td>
                    <td>{{ $pic['systemOvertime'] }}</td>
                    <td>{{ $pic['overtime'] - $pic['systemOvertime'] }}</td>
                    <td></td>
                </tr>
                @endforeach
                @foreach($members as $index => $member)
                <tr>
                    @if($index == 0)
                    <td>Anggota</td>
                    @else
                    <td></td>
                    @endif
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $member['employee'] }}</td>
                    <td>{{ $member['description'] }}</td>
                    <td>{{ $member['clockIn'] }}</td>
                    <td>{{ $member['clockOut'] }}</td>
                    <td>{{ $member['overtime'] }}</td>
                    <td>{{ $member['systemClockIn'] }}</td>
                    <td>{{ $member['systemClockOut'] }}</td>
                    <td>{{ $member['systemOvertime'] }}</td>
                    <td>{{ $member['overtime'] - $member['systemOvertime'] }}</td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top: 20px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 10%;">
                    <strong>Alasan selisih jam lembur</strong>
                </td>
                <td style="width: 5%;">:</td>
                <td style="width: 85%; border-bottom: 1px solid #000;">
                    <div>Test</div>
                </td>
            </tr>
        </table>
    </div>
    <div style="margin-top: 40px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 10%;">Disiapkan Oleh</td>
                <td style="width: 5%;">:</td>
                <td style="width: 15%; text-align: center;"><strong>{{ $overtime_application->preparedByEmployee->name ?? '-' }}</strong></td>
                <td></td>
                <td style="width: 10%;">Diajukan Oleh</td>
                <td style="width: 5%;">:</td>
                <td style="width: 15%; text-align: center;"><strong>{{ $overtime_application->submittedByEmployee->name ?? '-' }}</strong></td>
                <td></td>
                <td style="width: 10%;">Diketahui</td>
                <td style="width: 5%;">:</td>
                <td style="width: 15%; text-align: center;"><strong>{{ $overtime_application->knownByEmployee->name ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td colspan="11" style="height: 50px;"></td>
            </tr>
            <tr>
                <td style="width: 10%;"></td>
                <td style="width: 5%;"></td>
                <td style="width: 15%;"></td>
                <td></td>
                <td style="width: 10%;"></td>
                <td style="width: 5%;"></td>
                <td style="width: 15%; text-align: center;">(Paraf dan nama jelas)</td>
                <td></td>
                <td style="width: 10%;"></td>
                <td style="width: 5%;"></td>
                <td style="width: 15%; text-align: center;">(Paraf dan nama jelas)</td>
            </tr>
        </table>
    </div>
</body>

</html>