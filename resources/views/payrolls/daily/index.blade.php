@extends('layouts.app')

@section('title', 'Gaji Harian Magenta')

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
    <!-- <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_4">Perusahaan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_5">Link 2</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_6">Link 3</a>
        </li>
    </ul> -->
    <div class="row flex-wrap flex-stack mb-5">
        <!--begin::Heading-->
        <div class="col-md-6 d-flex align-items-center">
            <h2 class="fs-2 fw-bold my-2">Gaji Harian Magenta
                <!-- <span class="fs-6 text-gray-400 ms-1">by Month</span> -->
            </h2>
            <div class="ps-3">
                <!-- <select class="form-select" style="width: 150px;" id="select-year">
                    <?php $selectedYear = request()->query('year') ?? date('Y'); ?>
                    @for($yearOption = 2023; $yearOption <= date('Y'); $yearOption++) <option value="{{ $yearOption }}" <?= $selectedYear == $yearOption ? 'selected' : '';  ?>>
                        {{ $yearOption }}</option>
                        @endfor
                </select> -->
            </div>
        </div>
        <!--end::Heading-->
        <!--begin::Controls-->
        <div class="col-md-6 d-flex flex-wrap my-1 justify-content-end">
            <!--begin::Select wrapper-->
            <div class="m-0">
                <!--begin::Select-->
                <button class="btn btn-secondary btn-icon me-2 position-relative" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <i class="bi bi-gear"></i>
                    <?php
                    $reportReceiver = $setting->report_receiver ?? "";
                    $reportMaker = $setting->report_maker ?? "";
                    ?>
                    @if(empty($reportReceiver) || empty($reportMaker))
                    <span class="position-absolute top-0 start-100 translate-middle badge badge-circle badge-warning" style="width: 1rem; height: 1rem"></span>
                    @endif
                </button>
                <button class="btn btn-secondary btn-icon me-2"><i class="bi bi-funnel"></i></button>
                <a href="/payrolls/daily/generate" class="btn btn-primary"><i class="bi bi-command"></i> <span class="align-middle">Run Payroll</span></a>
                <!--end::Select-->
            </div>
            <!--end::Select wrapper-->
        </div>
        <!--end::Controls-->
    </div>
    <div class="row g-6 g-xl-9">
        @foreach($months as $index => $month)
        @if(array_key_exists($index, $payslips))
        <div class="col-lg-6 col-xxl-4">
            <!--begin::Budget-->
            <div class="card h-100">
                <div class="card-body p-9">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <div class="fs-2 fw-bolder">{{ $month }}</div>
                        <div>
                            <a href="/daily-salaries/export/magenta-report?month={{ sprintf('%02d', $index + 1) }}&year={{ $year }}" class="btn btn-light-primary btn-sm" target="_blank">
                                <i class="bi bi-download"></i>
                                <span>Rekapitulasi</span>
                            </a>
                        </div>
                    </div>
                    <div class="fs-4 fw-bold text-gray-400 mb-7">Periode di bulan {{ strtolower($month) }}</div>
                    <!-- <li><strong>{{ $month }}</strong></li> -->

                    @foreach($payslips[$index] as $period => $payslip)
                    <?php
                    $startDate = explode('/', $period)[0];
                    $endDate = explode('/', $period)[1];
                    ?>
                    <div class="fs-6 d-flex justify-content-between my-4">
                        <div class="fw-bold">Periode &nbsp;<a href="/payrolls/daily/show-by-period?start_date={{$startDate}}&end_date={{ $endDate }}" class="text-primary">{{ \Carbon\Carbon::parse($startDate)->isoFormat('ll') }} - {{ \Carbon\Carbon::parse($endDate)->isoFormat('ll') }}</a></div>
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
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="bi bi-gear me-2 text-dark"></i><span>Pengaturan</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <h6 class="mb-5">Rekapitulasi</h6>
                        <div class="mb-5">
                            <label for="" class="form-label">Yang Menerima</label>
                            <input type="text" v-model="model.reportReceiver" class="form-control form-control-sm">
                        </div>
                        <div>
                            <label for="" class="form-label">Dibuat Oleh</label>
                            <input type="text" v-model="model.reportMaker" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <!-- <button type="button" class="btn btn-primary">Simpan</button> -->
                    <button v-cloak type="button" :data-kt-indicator="saveSettingLoading ? 'on' : 'off'" class="btn btn-primary" :disabled="saveSettingLoading" @click="saveSetting">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">Mengirim data...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection

@section('pagescript')
<script>
    let app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                model: {
                    reportReceiver: '{{ $setting->report_receiver ?? "" }}',
                    reportMaker: '{{ $setting->report_maker ?? "" }}',
                },
                saveSettingLoading: false,
            }
        },
        methods: {
            async saveSetting() {
                const self = this;
                try {
                    self.saveSettingLoading = true;

                    const body = {
                        report_receiver: self.model.reportReceiver,
                        report_maker: self.model.reportMaker,
                    }

                    const response = await axios.post(`/payrolls/daily/save-setting`, body);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        // const data = response?.data?.data;
                        toastr.success(message + '. Memuat ulang..');
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                    self.saveSettingLoading = false
                } finally {}
            }
        }
    })
</script>
<script>
    $(function() {
        $('#select-year').on('change', function(e) {
            document.location.href = "/payrolls/daily?year=" + $(this).val();
        })
    })
</script>
@endsection