@extends('layouts.app')

@section('title', 'Laporan')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('pagestyle')
<style>
    .dataTables_empty {
        display: none;
    }
</style>
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!-- <div class="d-flex flex-wrap flex-stack mb-5">
        <h2 class="fs-2 fw-bold my-2">Laporan
        </h2>
    </div> -->
    <div class="card mb-10" style="background: #1C325E;">
        <!--begin::Body-->
        <div class="card-body d-flex ps-xl-15">
            <!--begin::Action-->
            <div class="m-0">
                <!--begin::Title-->
                <div class="position-relative fs-2x z-index-2 fw-bolder text-white mb-7">
                    Laporan
                    <p class="fs-3 opacity-75">Export dan unduh rekapitulasi data</p>
                </div>

                <!--end::Title-->
                <!--begin::Action-->
                <!-- <div class="mb-3">
                    <a href="/" class="btn btn-danger fw-bold me-2">Dashboard</a>
                </div> -->
                <!--begin::Action-->
                <div class="py-5"></div>
            </div>
            <!--begin::Action-->
            <!--begin::Illustration-->
            <img src="{{asset('assets/media/illustrations/sigma-1/4.png')}}" class="position-absolute me-3 bottom-0 end-0 h-175px" alt="">
            <!--end::Illustration-->
        </div>
        <!--end::Body-->
    </div>
    <div class="row g-6 g-xl-9">

        <div class="col-lg-6 col-xxl-6">
            <!--begin::Budget-->
            <div class="card h-100">
                <div class="card-body p-9">
                    <div class="fs-2 fw-bolder">Pegawai</div>
                    <div class="fs-4 fw-bold text-gray-400 mb-7">Daftar laporan pegawai </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/employees/all" class="text-primary">Seluruh Pegawai</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                </div>
            </div>
            <!--end::Budget-->
        </div>
        <div class="col-lg-6 col-xxl-6">
            <!--begin::Budget-->
            <div class="card h-100">
                <div class="card-body p-9">
                    <div class="fs-2 fw-bolder">Kehadiran</div>
                    <div class="fs-4 fw-bold text-gray-400 mb-7">Daftar laporan kehadiran </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/attendances/all" class="text-primary">Absensi Seluruh Pegawai</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/attendances/by-employee" class="text-primary">Absensi Per Pegawai</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/attendances/absences" class="text-primary">Pegawai Tidak Hadir</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/leaves/all" class="text-primary">Cuti Seluruh Pegawai</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/leaves/by-employee" class="text-primary">Cuti Per Pegawai</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/attendances-period/" class="text-primary">Kehadiran per periode</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                </div>
            </div>
            <!--end::Budget-->
        </div>
        <div class="col-lg-6 col-xxl-6">
            <!--begin::Budget-->
            <div class="card h-100">
                <div class="card-body p-9">
                    <div class="fs-2 fw-bolder">Penggajian</div>
                    <div class="fs-4 fw-bold text-gray-400 mb-7">Daftar laporan penggajian </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/salaries/monthly" class="text-primary">Honor</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <!-- <div class="separator separator-dashed"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/" class="text-primary">Data Kenaikan Gaji Pegawai</a></div>
                        </div>
                        <div>

                        </div>
                    </div> -->
                    <div class="separator separator-dashed"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/salaries/thr" class="text-primary">THR</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/salaries/tax" class="text-primary">Honor Pajak</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/salaries/deposits" class="text-primary">Deposit</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/salaries/leaves" class="text-primary">Cuti</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                </div>
            </div>
            <!--end::Budget-->
        </div>
        <div class="col-lg-6 col-xxl-6">
            <!--begin::Budget-->
            <div class="card h-100">
                <div class="card-body p-9">
                    <div class="fs-2 fw-bolder">Asuransi</div>
                    <div class="fs-4 fw-bold text-gray-400 mb-7">Daftar laporan asuransi </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/insurances/bpjs-ketenagakerjaan" class="text-primary">BPJS Ketenagakerjaan</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/insurances/bpjs-mandiri" class="text-primary">BPJS Mandiri</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                    @foreach($private_insurances as $private_insurance)
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/insurances/privates/{{ $private_insurance->id }}" class="text-primary">{{ $private_insurance->name }}</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                    @endforeach
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold"><a href="/reports/insurances/insurances-list" class="text-primary">Daftar Asuransi</a></div>
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                </div>
            </div>
            <!--end::Budget-->
        </div>

    </div>

</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection

@section('pagescript')

@endsection