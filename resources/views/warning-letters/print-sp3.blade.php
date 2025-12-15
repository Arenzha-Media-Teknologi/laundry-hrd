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
    <h4 style="text-align: center;">SURAT PERINGATAN TIGA (SP-3)</h4>
    <p>Nomor: {{ $warning_letter->number }}</p>
    <p>
        <span>Kepada Yth</span>, <br>
        <span> {{ $warning_letter->employee->name ?? "[Nama Karyawan]" }}</span><br>
        <span>{{ $warning_letter->employee->activeCareer->jobTitle->name ?? "[Nama Jabatan]" }}</span><br>
        <span>{{ $warning_letter->employee->office->division->company->name ?? "[Nama PT]" }}</span>
    </p>
    <p>Melalui surat ini, manajemen menyampaikan Surat Peringatan Tiga (SP-3) kepada Saudara. Surat ini diterbitkan setelah sebelumnya Saudara menerima SP-1 dan SP-2, namun hingga saat ini dinilai belum menunjukkan perbaikan memadai sesuai harapan perusahaan.
    </p>
    <p>Surat Peringatan Tiga (SP-3) ini merupakan bentuk pembinaan terakhir sekaligus penegasan akan komitmen perusahaan terhadap kedisiplinan dan profesionalisme kerja. Kami tetap berharap Saudara dapat menggunakan kesempatan ini untuk memperbaiki kineja dan perilaku kerja secara menyeluruh.
    </p>
    <p>Deskripsi pelanggaran/kelalaian yang dimaksud dalam surat ini tercantum dalam Lampiran yang termasuk dalam Surat Peringatan ini.
    </p>
    <p>Apabila setelah surat ini diterbitkan masih terjadi pelanggaran atau ketidaksesuaian, maka manajemen berhak mengambil langkah lanjutan sesuai ketentuan yang berlaku, termasuk diantaranya pemutusan hubungan kerja (PHK).
    </p>
    <p>Demikian surat ini disampaikan untuk dijadikan perhatian serius serta bahan refleksi menuju perubahan yang lebih positif. </p>
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