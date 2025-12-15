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
    <h4 style="text-align: center;">SURAT PERINGATAN 2 (SP-2)</h4>
    <p>Nomor: {{ $warning_letter->number }}</p>
    <p>
        <span>Kepada Yth</span>, <br>
        <span> {{ $warning_letter->employee->name ?? "[Nama Karyawan]" }}</span><br>
        <span>{{ $warning_letter->employee->activeCareer->jobTitle->name ?? "[Nama Jabatan]" }}</span><br>
        <span>{{ $warning_letter->employee->office->division->company->name ?? "[Nama PT]" }}</span>
    </p>
    <p>Sehubungan dengan evaluasi atas kinerja dan perilaku kerja Saudara, serta tindak lanjut dari Surat Peringatan Satu (SP-1) yang telah diberikan sebelumnya, maka dengan ini manajemen menyampaikan Surat Peringatan Dua (SP-2) sebagai bentuk pembinaan lanjutan.
    </p>
    <p>Kami menilai masih terdapat beberapa hal yang perlu diperbaiki agar pelaksanaan tugas Saudara dapat kembali selaras dengan nilai, etika dan standar kerja perusahaan. Surat ini diharapkan dapat menjadi pengingat untuk melakukan perbaikan secara konsisten dan berkelanjutan. Perusahaan berhadap Saudara dapat mengambil surat ini sebagai motivasi untuk meningkatkan profesionalisme dan kinerja ke arah yang lebih baik. </p>
    <p>Deskripsi pelanggaran/kelalaian yang dimaksud dalam surat ini Lampiran yang termasuk dalam Surat Peringatan ini.
    </p>
    <p>Surat Peringatan Dua (SP-2) ini memiliki jangka waktu 3 (tiga) bulan, yang artinya berakhir pada tanggal {{ \Carbon\Carbon::parse($warning_letter->effective_end_date)->isoFormat('LL') }}. Apabila dalam periode tersebut tidak terjadi perubahan positif, terjadi pelanggaran serupa, ataupun pelanggaran lainnya, maka manajemen berhak memberikan Surat Peringatan 3 (SP-3) atau tindakan disiplin lanjutan sesuai kebijakan perusahaan. </p>
    <p>Demikian surat peringatan ini disampaikan untuk menjadi perhatian dan bahan refleksi demi perbaikan bersama.</p>
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