@extends('layouts.app')

@section('title', 'Gaji Harian')

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
    <div class="row flex-wrap flex-stack mb-5">
        <!--begin::Heading-->
        <div class="col-md-6 d-flex align-items-center">
            <h2 class="fs-2 fw-bold my-2">Gaji Harian
                <!-- <span class="fs-6 text-gray-400 ms-1">by Month</span> -->
            </h2>
            <div class="ps-3">
                <select class="form-select" style="width: 150px;" id="select-year">
                    <?php $selectedYear = request()->query('year') ?? date('Y'); ?>
                    @for($year = 2023; $year <= date('Y'); $year++) <option value="{{ $year }}" <?= $selectedYear == $year ? 'selected' : '';  ?>>
                        {{ $year }}</option>
                        @endfor
                </select>
            </div>
        </div>
        <!--end::Heading-->
        <!--begin::Controls-->
        <div class="col-md-6 d-flex flex-wrap my-1 justify-content-end">
            <!--begin::Select wrapper-->
            <div class="m-0">
                <!--begin::Select-->
                <a href="/payrolls/daily-aerplus/generate" class="btn btn-primary">Generate</a>
                <!--end::Select-->
            </div>
            <!--end::Select wrapper-->
        </div>
        <!--end::Controls-->
    </div>
    <div class="row g-6 g-xl-9">
        <?php $isEmpty = false; ?>
        @foreach($months as $index => $month)
        @if(array_key_exists($index, $payslips))
        <div class="col-lg-6 col-xxl-6">
            <!--begin::Budget-->
            <div class="card h-100">
                <div class="card-body p-9">
                    <div class="fs-2 fw-bolder">{{ $month }}</div>
                    <div class="fs-4 fw-bold text-gray-400 mb-7">Periode di bulan {{ strtolower($month) }}</div>
                    @foreach($payslips[$index] as $period => $payslip)
                    <?php
                    $startDate = explode('/', $period)[0];
                    $endDate = explode('/', $period)[1];
                    ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold">Periode &nbsp;<a href="/payrolls/daily-aerplus/show-by-period?start_date={{$startDate}}&end_date={{ $endDate }}" class="text-primary">{{ \Carbon\Carbon::parse($startDate)->isoFormat('ll') }} - {{ \Carbon\Carbon::parse($endDate)->isoFormat('ll') }}</a></div>
                        </div>
                        <div>
                            <!-- <a href="/daily-salaries/export/aerplus-report?start_date={{ $startDate }}&end_date={{ $endDate }}" target="_blank" class="btn btn-light-primary btn-sm">
                                <i class="bi bi-download"></i>
                                <span>Rekap Detail</span>
                            </a> -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-download"></i>
                                    <span>Rekapitulasi</span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/daily-salaries/export/aerplus-report?start_date={{ $startDate }}&end_date={{ $endDate }}" target="_blank">Detail</a></li>
                                    <li><a class="dropdown-item" href="/daily-salaries/export/aerplus-summary-report?start_date={{ $startDate }}&end_date={{ $endDate }}" target="_blank">Summary</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                    @endforeach
                </div>
            </div>
            <!--end::Budget-->
        </div>
        @endif
        @endforeach
        @if(count($payslips) == 0)
        <div class="text-center">
            <img src="{{ asset('assets/media/illustrations/sigma-1/5.png') }}" alt="" width="300">
            <p class="text-muted fw-bold fs-3 mt-3">Tidak ada data</p>
        </div>
        @endif
    </div>

</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection

@section('pagescript')
<script>
    $(function() {
        $('#select-year').on('change', function(e) {
            document.location.href = "/payrolls/daily-aerplus?year=" + $(this).val();
        })
    })
</script>
@endsection