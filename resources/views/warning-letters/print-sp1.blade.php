<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $warning_letter->number }}</title>
    <style>
        p {
            font-size: x-small;
            line-height: 1.2rem;
            text-align: justify;
        }
    </style>
</head>

<body>
    <h4 style="text-align: center;">SURAT PERINGATAN SATU (SP-1)</h4>
    <p>Nomor: {{ $warning_letter->number }}</p>
    <p>
        <span>Kepada Yth</span>, <br>
        <span> {{ $warning_letter->employee->name ?? "[Nama Karyawan]" }}</span><br>
        <span>{{ $warning_letter->employee->activeCareer->jobTitle->name ?? "[Nama Jabatan]" }}</span><br>
        <span>{{ $warning_letter->employee->office->division->company->name ?? "[Nama PT]" }}</span>
    </p>
    <p>Sebagai bagian dari upaya pembinaan dan pengembangan sumber daya manusia, manajemen menilai perlu menyampaikan Surat Peringatan Satu (SP-1) kepada Saudara
    </p>
    <p>Surat ini diterbitkan karena terdapat hal-hal dalam pelaksanaan tugas atau sikap kerja yang belum sepenuhnya sesuai dengan nilai dan standar yang berlaku di perusahaan. Kami memandang bahwa hal ini masih dapat diperbaiki melalui komunikasi terbuka dan kesadaran untuk bertumbuh bersama. </p>
    <p>Deskripsi pelanggaran/kelalaian yang dimaksud dalam hal ini tercantum dalam Lampiran yang termasuk dalam Surat Peringatan ini. </p>
    <p>Surat Peringatan ini bertujuan untuk memberikan kesempatan kepada Saudara agar dapat melakukan introspeksi, memperbaiki kinerja, dan kembali selaras dengan nilai-nilai perusahaan. </p>
    <p>Surat Peringatan Satu (SP-1) ini memiliki jangka waktu 6 (enam) bulan, yang artinya berakhir pada tanggal {{ \Carbon\Carbon::parse($warning_letter->effective_end_date)->isoFormat('LL') }}. Apabila selepas jangka waktu tersebut terdapat perubahan dan perbaikan yang signifikan, maka surat ini akan dianggap tidak lagi berlaku. Namun apabila dalam periode tersebut terjadi pelanggaran serupa, ataupun pelanggaran lainnya, maka manajemen dapat memberikan Surat Peringatan Dua (SP-2), ataupun tindakan disipliner lain sesuai kebijakan perusahaan. </p>
    <p>Demikian surat peringatan ini disampaikan dengan harapan dapat menjadi dorongan untuk berkembang lebih baik ke depan. </p>
    <p>Jakarta, {{ \Carbon\Carbon::parse($warning_letter->effective_start_date)->isoFormat('LL') }}</p>
    <p></p>
    <p></p>
    <p></p>
    <p></p>
    <div style="width: 30%; border-top: 1px solid #000"></div>
    <p style="margin-bottom: 0;">{{ $warning_letter->signatoryEmployee->name ?? '[Nama jelas pejabat yang menandatangani]' }}</p>
    <p style="margin: 0;">{{ $warning_letter->signatoryEmployee->activeCareer->jobTitle->name ?? '[Jabatan pejabat yang menandatangani]
    ' }}</p>
</body>

</html>