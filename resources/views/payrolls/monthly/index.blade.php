@extends('layouts.app')

@section('title', 'Gaji Bulanan')

@section('prehead')
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
    <?php $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] ?>
    <div class="card mb-5 mb-xxl-10" style="background: linear-gradient(to right, #2f80ed, #56ccf2); background-image: url('<?= asset('assets/media/patterns/hexagonal.jpg') ?>'); background-position: center; background-repeat: no-repeat; background-size: cover;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-white mb-5 fs-1">Penggajian Bulanan</p>
                    <h1 class="text-white mb-3 fs-2x">{{ $company->name ?? '' }}</h1>
                    <p class="m-0 fs-3 text-white mb-4">{{ $months[((int) $salary_month) - 1] }} {{ $salary_year }}</p>
                </div>
                <div class="text-end">
                    <p class="text-white mb-3 fs-4 fw-bolder">Total Gaji</p>
                    <p class="text-white mb-4 fs-2x m-0">Rp @{{ currencyFormat(fixSalaries.totalTakeHomePay + generatedSalaries.totalTakeHomePay) }}</p>
                    <div class="text-end">
                        <a class="btn btn-light btn-sm" data-bs-toggle="collapse" href="#cardRunPayroll" role="button" aria-expanded="false" aria-controls="collapseExample">
                            <span style="color: royalblue;" class="me-2">Run Payroll</span>
                            <i class="bi bi-chevron-down" style="color: royalblue;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-5 mb-xxl-10 collapse" id="cardRunPayroll">
        <!--begin::Header-->
        <div class="card-header" style="background: linear-gradient(to right, #2f80ed, #56ccf2); background-image: url('<?= asset('assets/media/patterns/hexagonal.jpg') ?>'); background-position: center; background-repeat: no-repeat; background-size: cover;">
            <div class="card-title">
                <h3 class="text-white">Run Payroll</h3>
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body">
            <!-- <h3>Filter</h3> -->
            <div class="alert alert-secondary" role="alert">
                <span class="bi bi-question-circle me-2"></span>
                <span>Pilih bulan, tahun, dan perusahaan kemudian klik tombol <strong>Generate</strong> untuk menghitung haji</span>
            </div>
            <div class="row align-items-end mb-6">
                <div class="col-md-6 col-lg-4 my-3">
                    <div class="mb-0">
                        <label class="form-label">Bulan</label>

                        <select v-model="model.month" class="form-select form-select-solid">
                            @foreach($months as $index => $month)
                            <?php $value = sprintf('%02d', $index + 1); ?>
                            <option value="{{ $value }}">{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 my-3">
                    <div class="mb-0">
                        <label class="form-label">Tahun</label>
                        <select v-model="model.year" class="form-select form-select-solid">
                            @for($i = 2021; $i <= date("Y"); $i++) <option value="{{ $i }}" <?= $i == (int) date("Y") ? 'selected' : '' ?>>{{ $i }}</option>
                                @endfor
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 my-3">
                    <div class="mb-0">
                        <label class="form-label">Perusahaan</label>
                        <select v-model="model.companyId" class="form-select form-select-solid">
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if($have_staff_permission)
                <div class="col-md-6 col-lg-4 my-3">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" v-model="model.staffOnly" id="staffOnlySwitch" />
                        <label class="form-check-label" for="staffOnlySwitch">
                            <span class="fs-6 fw-bold text-gray-800">Hanya Tampilkan Staff</span>
                        </label>
                    </div>
                </div>
                @endif
            </div>
            <div class="my-3 text-end">
                <!-- <button class="btn btn-primary">Generate</button> -->
                <button type="button" class="btn btn-primary btn-sm" @click="generate">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Generate</span>
                </button>
            </div>
            <!-- <h3>Laporan</h3>
            <div>
                <a href="/salaries/export/report/monthly?start_date_period={{ $start_date }}&end_date_period={{ $end_date }}&staffonly={{ request()->query('staffonly') !== null ? request()->query('staffonly') : 'false' }}" target="_blank" class="btn btn-primary">Download Laporan</a>
            </div> -->
        </div>
        <!--end::Body-->
    </div>
    <div class="card">
        <!--begin::Card header-->
        <!-- <div class="card-header card-header-stretch">
            <div class="card-title">
                <h3 class="m-0 text-gray-900">Data Gaji</h3>
            </div>
            <div class="card-toolbar">
                <ul class="nav nav-tabs nav-line-tabs nav-stretch border-transparent fs-5 fw-bolder" id="kt_security_summary_tabs">
                    <li class="nav-item">
                        <a class="nav-link text-active-primary active" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#not_made_payslip">Belum Dibuat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#made_payslip">Dibuat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#payslip_summary">Rekap</a>
                    </li>
                </ul>
            </div>
        </div> -->
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <div class="d-flex justify-content-end mb-4">
                <div>
                    <ul class="nav nav-tabs nav-line-tabs nav-stretch border-transparent fs-5 fw-bolder" id="kt_security_summary_tabs">
                        <li class="nav-item">
                            <a class="nav-link text-active-primary active" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#not_made_payslip">Belum Dibuat</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#made_payslip">Dibuat</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#payslip_summary">Rekap</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade active show" id="not_made_payslip" role="tab-panel">
                    <div class="alert alert-secondary" role="alert">
                        <div class="d-flex align-items-center">
                            <span class="bi bi-question-circle me-3 fs-4"></span>
                            <span><strong>Gaji Belum Dibuat</strong> adalah gaji yang nilainya belum tetap dan dapat berubah setiap melakukan <strong>Generate</strong> mengikuti perubahan komponen yang mempengaruhi seperti kehadiran, keterlambatan, dan lain-lain.</span>
                        </div>
                    </div>
                    <!--begin::summary-->
                    <div class="row p-0 mb-5">
                        <!--begin::Col-->
                        <div class="col-sm-6">
                            <div class="border border-gray-300  text-center min-w-125px rounded pt-4 pb-2 my-3">
                                <span class="fs-5 fw-bold text-gray-700 d-block">Total Pegawai</span>
                                <span v-cloak class="fs-2hx fw-bolder text-gray-900 counted" data-kt-countup="true" data-kt-countup-value="36899">@{{ currencyFormat(generatedSalaries.totalEmployees) }}</span>
                            </div>
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-sm-6">
                            <div class="border border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                                <span class="fs-5 fw-bold text-gray-700 d-block">Total THP</span>
                                <span v-cloak class="fs-2hx fw-bolder text-gray-900 counted" data-kt-countup="true" data-kt-countup-value="72">Rp @{{ currencyFormat(generatedSalaries.totalTakeHomePay) }}</span>
                            </div>
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                    </div>
                    <!--end::summary-->
                    <div class="row justify-content-between align-items-center mb-5">
                        <div class="col-md-6">
                            <!-- Input Search -->
                            <div class="d-flex align-items-center position-relative my-1">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" employee-table-filter="search" class="form-control form-control-solid w-250px ps-15 fs-7" placeholder="Cari Pegawai" />
                            </div>
                            <!-- Input Search -->
                        </div>
                        <div class="col-md-6 text-end">
                            <button v-cloak v-if="goodToSave" type="button" :data-kt-indicator="saveLoading ? 'on' : null" class="btn btn-light-primary" :disabled="!goodToSave || saveLoading" @click="save">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <span class="indicator-label">Simpan @{{ model.checkedEmployeesIds.length }} Slip Gaji</span>
                                <span class="indicator-progress">Mengirim data...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                            <button v-cloak v-else type="button" :data-kt-indicator="saveLoading ? 'on' : null" class="btn btn-light-primary" :disabled="!goodToSave || saveLoading" @click="save">
                                <span>Pilih Pegawai</span>
                            </button>
                        </div>
                    </div>
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6" id="employee_table">
                        <!--begin::Table head-->
                        <thead class="bg-light-primary">
                            <!--begin::Table row-->
                            <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="ps-2" style="width: 50px;">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" @change="checkAll($event)" id="checkAll" />
                                        <!-- <input class="form-check-input" type="checkbox" v-model="model.checkedAll" id="checkAll" /> -->
                                        <label class="form-check-label" for="checkAll"></label>
                                    </div>
                                </th>
                                <th class="min-w-125px">Nama</th>
                                <th class="">Job Title</th>
                                <th></th>
                                <th class="text-end">Insentif Kehadiran</th>
                                <th class="text-end">Take Home Pay</th>
                                <th class="text-end min-w-70px pe-2">Actions</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="fw-bold text-gray-600 fs-7">
                            <tr v-cloak v-for="(salary, index) in generatedSalaries.salaries">
                                <td class="ps-2">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" v-model="model.checkedEmployeesIds" :value="salary.employee.id" :id="'flexCheckDefault' + salary.employee.id" />
                                        <label class="form-check-label" :for="'flexCheckDefault' + salary.employee.id"></label>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div>
                                            <a :href="'/employees/' + salary?.employee?.id + '/detail-v2'" class="text-gray-700 text-hover-primary fw-bolder mb-1">@{{ salary?.employee?.name }}</a>
                                        </div>
                                        <span class="text-muted">@{{ salary?.employee?.number }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div v-if="salary.employee.active_career">
                                        <span class="text-gray-700">@{{ salary?.employee?.active_career?.job_title?.name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span v-for="property in salary.properties" :class="`badge badge-light-${property.color} me-2 text-uppercase`" v-html="property.text"></span>
                                </td>
                                <td class="text-end">
                                    <span class="text-gray-800">@{{ currencyFormat(salary?.attributes?.presence_incentive_amount) }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-gray-800">@{{ currencyFormat(salary?.summary?.take_home_pay) }}</span>
                                </td>
                                <td class="text-end pe-2">
                                    <button type="button" class="btn btn-light btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#detail_modal" @click="openDetailModal(salary?.employee?.id)">
                                        <!-- <span class="svg-icon svg-icon-5 m-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="black" />
                                                <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="black" />
                                            </svg>
                                        </span> -->
                                        <i class="bi bi-list-ul"></i>
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <div class="tab-pane fade" id="made_payslip" role="tab-panel">
                    <div class="alert alert-secondary" role="alert">
                        <div class="d-flex align-items-center">
                            <span class="bi bi-question-circle me-3 fs-4"></span>
                            <span><strong>Gaji Dibuat</strong> adalah gaji yang nilainya tetap yang didapatkan dari hasil <strong>Generate</strong> dan <strong>Disimpan</strong>. Data gaji masih dapat diubah nilainya untuk setiap komponen dengan memilih tombol <strong><kbd><i class="bi bi-pencil text-white"></i> Edit</kbd></strong></span>
                        </div>
                    </div>
                    <div class="text-end rounded py-5">
                        <div class="">

                        </div>
                        @if($company_id == "6")
                        <!-- <a href="/payrolls/monthly/payment?start_date_period={{ $start_date }}&end_date_period={{ $end_date }}&staff_only={{ $staff_only }}&company_id={{ $company_id }}" class="btn btn-sm btn-outline btn-outline-primary me-3" target="_blank">
                            <span class="me-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-wallet2" viewBox="0 0 16 16">
                                    <path d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5z" />
                                </svg>
                            </span>
                            <span>Pembayaran</span>
                        </a> -->
                        <a href="/payrolls/monthly/payment?start_date_period={{ $start_date }}&end_date_period={{ $end_date }}&staff_only={{ $staff_only }}&company_id={{ $company_id }}" target="_blank" class="btn btn-outline btn-sm btn-outline-primary btn-active-light-primary me-3">
                            <i class="bi bi-wallet text-primary"></i>
                            <span>Pembiayaan</span>
                        </a>
                        @endif
                        @if($have_staff_permission)
                        <a href="/salaries/export/report/monthly?start_date_period={{ $start_date }}&end_date_period={{ $end_date }}&staff_only={{ $staff_only }}&company_id={{ $company_id }}&presence_incentive_only=1" class="btn btn-outline btn-sm btn-outline-success btn-active-light-success me-3" target="_blank">
                            <i class="bi bi-download text-success"></i>
                            <span>Denda & Ins. Kehadiran Staff</span>
                        </a>
                        @endif
                        <a href="/salaries/export/report/monthly?start_date_period={{ $start_date }}&end_date_period={{ $end_date }}&staff_only={{ $staff_only }}&company_id={{ $company_id }}" class="btn btn-outline btn-sm btn-outline-success btn-active-light-success" target="_blank">
                            <i class="bi bi-download text-success"></i>
                            <span>Rekapitulasi Gaji</span>
                        </a>
                    </div>
                    <!--begin::summary-->
                    <div class="row p-0 mb-5">
                        <!--begin::Col-->
                        <div class="col-sm-6">
                            <div class="border border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                                <span class="fs-5 fw-bold text-gray-700 d-block">Total Pegawai</span>
                                <span v-cloak class="fs-2hx fw-bolder text-gray-900 counted" data-kt-countup="true" data-kt-countup-value="36899">@{{ currencyFormat(fixSalaries.totalEmployees) }}</span>
                            </div>
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-sm-6">
                            <div class="border border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                                <span class="fs-5 fw-bold text-gray-700 d-block">Total THP</span>
                                <span v-cloak class="fs-2hx fw-bolder text-gray-900 counted" data-kt-countup="true" data-kt-countup-value="72">Rp @{{ currencyFormat(fixSalaries.totalTakeHomePay) }}</span>
                            </div>
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                    </div>
                    <!--end::summary-->
                    <!-- Input Search -->
                    <!-- <div class="mb-5">
                        
                    </div> -->
                    <!-- Input Search -->
                    <div class="row justify-content-between align-items-center mb-5">
                        <div class="col-md-6">
                            <!-- Input Search -->
                            <div class="d-flex align-items-center position-relative my-1">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" fix-salaries-table-filter="search" class="form-control form-control-solid w-250px ps-15 fs-7" placeholder="Cari Pegawai" />
                            </div>
                            <!-- Input Search -->
                        </div>
                        <div class="col-md-6 text-end">
                            <button v-cloak v-if="model.fixSalaries.checkedEmployeesIds.length > 0" type="button" class="btn btn-danger" @click="openBulkDeleteConfirmation">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                <span class="svg-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <span class="indicator-label">Hapus @{{ model.fixSalaries.checkedEmployeesIds.length }} Slip Gaji</span>
                                <span class="indicator-progress">Menghapus data...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-7" id="fix_salaries_table">
                            <!--begin::Table head-->
                            <thead class="bg-light-primary">
                                <!--begin::Table row-->
                                <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="ps-2">
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" @change="fixSalariesCheckAll($event)" id="fixSalariesCheckAll" />
                                            <!-- <input class="form-check-input" type="checkbox" v-model="model.checkedAll" id="checkAll" /> -->
                                            <label class="form-check-label" for="fixSalariesCheckAll"></label>
                                        </div>
                                    </th>
                                    <th class="min-w-125px">Nama</th>
                                    <th class="">Job Title</th>
                                    <th class="text-end">Insentif Kehadiran</th>
                                    <th class="text-end">Take Home Pay</th>
                                    <th class="text-end min-w-70px pe-2">Actions</th>
                                </tr>
                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="fw-bold text-gray-600">
                                <tr v-cloak v-for="(salary, index) in fixSalaries.salaries">
                                    <td class="ps-2">
                                        <div v-if="salary.salary.paid == 1">
                                            <button class="btn btn-danger btn-icon btn-sm" @click="unpaid(salary.salary.id)">
                                                <i class="bi bi-x pe-0"></i>
                                                <!-- Ubah -->
                                            </button>
                                            <span class="badge badge-light-success">Dibayar</span>
                                        </div>
                                        <div v-else>
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" v-model="model.fixSalaries.checkedEmployeesIds" :value="salary.salary.id" :id="'fixSalariesCheck' + salary.employee.id" />
                                                <label class="form-check-label" :for="'fixSalariesCheck' + salary.employee.id"></label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div>
                                                <a :href="'/employees/' + salary?.employee?.id + '/detail-v2'" class="text-gray-700 text-hover-primary fw-bolder mb-1">@{{ salary?.employee?.name }}</a>
                                            </div>
                                            <span class="text-muted">@{{ salary?.employee?.number }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div v-if="salary.employee.active_career">
                                            <span class="text-gray-700">@{{ salary?.employee?.active_career?.job_title?.name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-gray-800">@{{ currencyFormat(salary?.attributes?.presence_incentive_amount) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-gray-800">@{{ currencyFormat(salary?.summary?.take_home_pay) }}</span>
                                    </td>
                                    <td class="text-end pe-2">
                                        <a :href="'/salaries/' + salary?.salary?.id + '/print'" target="_blank" class="btn btn-success btn-icon btn-sm">
                                            <i class="bi bi-printer pe-0"></i>
                                            <!-- Cetak -->
                                        </a>
                                        <span v-if="salary.salary.paid != 1">
                                            <a :href="'/salaries/' + salary?.salary?.id + '/edit'" class="btn btn-warning btn-icon btn-sm">
                                                <i class="bi bi-pencil pe-0"></i>
                                                <!-- Ubah -->
                                            </a>
                                            <button type="button" class="btn btn-danger btn-icon btn-sm" @click="openDesignationDeleteConfirmation(salary?.salary?.id)">
                                                <span class="svg-icon m-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                                    </svg>
                                                </span>
                                            </button>
                                        </span>
                                        <button type="button" class="btn btn-light btn-icon btn-sm" data-bs-toggle="modal" data-bs-target="#detail_fix_modal" @click="openDetailFixModal(salary?.employee?.id)">
                                            <i class="bi bi-list-ul pe-0"></i>
                                            <!-- Detail -->
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                    </div>
                </div>
                <div class="tab-pane fade" id="payslip_summary" role="tab-panel">
                    <div class="alert alert-secondary" role="alert">
                        <div class="d-flex align-items-center">
                            <span class="bi bi-question-circle me-3 fs-4"></span>
                            <span><strong>Rekap</strong> adalah rekapitulasi dari <strong>Gaji Dibuat</strong></span>
                        </div>
                    </div>
                    <!-- Input Search -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center position-relative my-1">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                            <input type="text" fix-salaries-table-filter="search" class="form-control form-control-solid w-250px ps-15 fs-7" placeholder="Cari Pegawai" />
                        </div>
                    </div>
                    <!-- Input Search -->
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-7" id="summary_salaries_table">
                            <!--begin::Table head-->
                            <thead class="bg-light-primary">
                                <!--begin::Table row-->
                                <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                                    <th rowspan="2" class="ps-2">No</th>
                                    <th class="min-w-125px" rowspan="2">Nama</th>
                                    <th rowspan="2" class="min-w-150px">NPWP</th>
                                    <th rowspan="2" class="min-w-150px">Status</th>
                                    <th rowspan="2" class="min-w-150px text-end">Gaji Pokok</th>
                                    <th rowspan="2" class="min-w-150px text-end">Tunjangan</th>
                                    <th rowspan="2" class="min-w-150px text-end">Insentif Kehadiran</th>
                                    <th rowspan="2" class="min-w-150px text-end">Gaji Bruto</th>
                                    <th colspan="4" class="text-center">Pemotongan Piutang</th>
                                    <th rowspan="2" class="min-w-150px text-end">Kelebihan Cuti</th>
                                    <th rowspan="2" class="min-w-150px text-end pe-2">Gaji Netto</th>
                                </tr>
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-150px text-end">Awal</th>
                                    <th class="min-w-150px text-end">Pinjaman</th>
                                    <th class="min-w-150px text-end">Potongan</th>
                                    <th class="min-w-150px text-end">Sisa</th>
                                </tr>
                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="fw-bold text-gray-600">
                                <tr v-cloak v-for="(salary, index) in summarySalaries.salaries">
                                    <td class="ps-2">
                                        <!-- NPWP -->
                                        <span>@{{ index + 1 }}</span>
                                    </td>
                                    <td>
                                        <!-- Nama -->
                                        <div>
                                            <div>
                                                <a :href="'/employees/' + salary?.employee?.id + '/detail-v2'" class="text-gray-700 text-hover-primary fw-bolder mb-1">@{{ salary?.name }}</a>
                                            </div>
                                            <span class="text-muted">@{{ salary?.number }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <!-- NPWP -->
                                        <span>@{{ salary?.npwp_number }}</span>
                                    </td>
                                    <td>
                                        <!-- Status -->
                                        <span>@{{ salary?.npwp_status }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Gaji Pokok -->
                                        <span class="text-gray-800">@{{ currencyFormat(salary?.basic_salary) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Tunjangan -->
                                        <span class="text-gray-800">@{{ currencyFormat(salary?.position_allowance) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Insentif Kehadiran -->
                                        <span class="text-gray-800">@{{ currencyFormat(salary?.attendance_allowance) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Gaji Bruto -->
                                        <span class="text-gray-800">@{{ currencyFormat(salary?.bruto_salary) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Awal -->
                                        <span class="text-gray-800">@{{ currencyFormat(salary?.total_loan) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Pinjaman -->
                                        <span class="text-gray-800">@{{ currencyFormat(salary?.current_month_loan) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Potongan -->
                                        <span class="text-gray-800">@{{ currencyFormat(0 - salary?.loan) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Sisa -->
                                        <span class="text-gray-800">@{{ currencyFormat(salary?.remaining_loan) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Kelebihan Cuti -->
                                        <span class="text-gray-800">@{{ currencyFormat(0 - salary?.excess_leave) }}</span>
                                    </td>
                                    <td class="text-end pe-2">
                                        <!-- Netto -->
                                        <span class="text-gray-800">@{{ currencyFormat(salary?.total) }}</span>
                                    </td>
                                </tr>
                            </tbody>
                            <!--end::Table body-->
                            <tfoot class="bg-light-primary">
                                <tr>
                                    <td></td>
                                    <td>Total</td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-end">
                                        <!-- Gaji Pokok -->
                                        <span class="text-gray-800">@{{ currencyFormat(getTotalSummarySalaries('basic_salary')) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Tunjangan -->
                                        <span class="text-gray-800">@{{ currencyFormat(getTotalSummarySalaries('position_allowance')) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Insentif Kehadiran -->
                                        <span class="text-gray-800">@{{ currencyFormat(getTotalSummarySalaries('attendance_allowance')) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Gaji Bruto -->
                                        <span class="text-gray-800">@{{ currencyFormat(getTotalSummarySalaries('bruto_salary')) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Awal -->
                                        <span class="text-gray-800">@{{ currencyFormat(getTotalSummarySalaries('total_loan')) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Pinjaman -->
                                        <span class="text-gray-800">@{{ currencyFormat(getTotalSummarySalaries('current_month_loan')) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Potongan -->
                                        <span class="text-gray-800">@{{ currencyFormat(0 - getTotalSummarySalaries('loan')) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Sisa -->
                                        <span class="text-gray-800">@{{ currencyFormat(getTotalSummarySalaries('remaining_loan')) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- Kelebihan Cuti -->
                                        <span class="text-gray-800">@{{ currencyFormat(0 - getTotalSummarySalaries('excess_leave')) }}</span>
                                    </td>
                                    <td class="text-end pe-2">
                                        <!-- Netto -->
                                        <span class="text-gray-800">@{{ currencyFormat(getTotalSummarySalaries('total')) }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!--end::Table-->
                </div>
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
    <div class="modal fade" tabindex="-1" id="detail_modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Gaji</h5>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <div v-if="selectedDetailSalary">
                        <div class="d-flex flex-center flex-column">
                            <!--begin::Name-->
                            <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bolder mb-0">@{{ selectedDetailSalary?.employee?.name }}</a>
                            <!--end::Name-->
                            <!--begin::Position-->
                            <div class="fw-bold text-gray-400 mb-6">@{{ selectedDetailSalary?.employee?.active_career?.job_title?.name }}</div>
                            <!--end::Position-->
                        </div>
                        <div>
                            <h4>Pendapatan</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                            <th>Nama</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(income, index) in selectedDetailSalary.temp_incomes">
                                            <td>@{{ income.name }}</td>
                                            <td class="text-end">@{{ currencyFormat(income.amount) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td class="text-end fw-bold">@{{ currencyFormat(selectedDetailSalary.summary.total_incomes) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div>
                            <h4>Potongan</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                            <th>Nama</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(deduction, index) in selectedDetailSalary.deductions">
                                            <td>@{{ deduction.name }}</td>
                                            <td class="text-end">@{{ currencyFormat(deduction.amount) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td class="text-end fw-bold">@{{ currencyFormat(selectedDetailSalary.summary.total_deductions) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <h3 class="my-10 bg-light-success p-5 rounded text-success fs-6 text-end">Rp @{{ currencyFormat(selectedDetailSalary.summary.take_home_pay) }}</h3>
                    </div>
                    <div v-else>
                        <h3 class="text-center text-gray-800">Pilih Pegawai</h3>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Card-->
    <div class="modal fade" tabindex="-1" id="detail_fix_modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Gaji</h5>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-2x"></span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <div v-if="selectedDetailFixSalary">
                        <div class="d-flex flex-center flex-column">
                            <!--begin::Name-->
                            <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bolder mb-0">@{{ selectedDetailFixSalary?.employee?.name }}</a>
                            <!--end::Name-->
                            <!--begin::Position-->
                            <div class="fw-bold text-gray-400 mb-6">@{{ selectedDetailFixSalary?.employee?.active_career?.job_title?.name }}</div>
                            <!--end::Position-->
                        </div>
                        <div>
                            <h4>Pendapatan</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                            <th>Nama</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(income, index) in selectedDetailFixSalary.incomes">
                                            <td>@{{ income.name }}</td>
                                            <td class="text-end">@{{ currencyFormat(income.amount) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td class="text-end fw-bold">@{{ currencyFormat(selectedDetailFixSalary.summary.total_incomes) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div>
                            <h4>Potongan</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                            <th>Nama</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(deduction, index) in selectedDetailFixSalary.deductions">
                                            <td>@{{ deduction.name }}</td>
                                            <td class="text-end">@{{ currencyFormat(deduction.amount) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td class="text-end fw-bold">@{{ currencyFormat(selectedDetailFixSalary.summary.total_deductions) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <h3 class="my-10 bg-light-success p-5 rounded text-success fs-6 text-end">Rp @{{ currencyFormat(selectedDetailFixSalary.summary.take_home_pay) }}</h3>
                    </div>
                    <div v-else>
                        <h3 class="text-center text-gray-800">Pilih Pegawai</h3>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
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
    let datatable = null;
    $(function() {
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toastr-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

    })
</script>
<script>
    const closeCompanyAddModal = () => {
        const addCompanyModal = document.querySelector('#kt_modal_add_company');
        const modal = bootstrap.Modal.getInstance(addCompanyModal);
        modal.hide();
    }

    const salaries = <?php echo Illuminate\Support\Js::from($generated_salaries['salaries']) ?>;
    const fixSalaries = <?php echo Illuminate\Support\Js::from($fix_salaries['salaries']) ?>;
    const summarySalaries = <?php echo Illuminate\Support\Js::from($summary_salaries['salaries']) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                model: {
                    fixSalaries: {
                        checkedAll: false,
                        checkedEmployeesIds: [],
                    },
                    checkedAll: false,
                    checkedEmployeesIds: [],
                    month: "{{ $salary_month }}",
                    year: "{{ $salary_year }}",
                    staffOnly: "{{ $staff_only }}",
                    companyId: "{{ $company_id }}",
                },
                generatedSalaries: {
                    salaries,
                    totalEmployees: Number("{{ $generated_salaries['total_employees'] }}"),
                    totalTakeHomePay: Number("{{ $generated_salaries['total_take_home_pay'] }}"),
                },
                fixSalaries: {
                    salaries: fixSalaries,
                    totalEmployees: Number("{{ $fix_salaries['total_employees'] }}"),
                    totalTakeHomePay: Number("{{ $fix_salaries['total_take_home_pay'] }}"),
                },
                summarySalaries: {
                    salaries: summarySalaries,
                },
                startDate: "{{ $start_date }}",
                endDate: "{{ $end_date }}",
                generateLoading: false,
                saveLoading: false,
                totalEmployees: 0,
                totalTakeHomePay: 0,
                selectedDetailEmployeeId: null,
                selectedDetailFixEmployeeId: null,
            }
        },
        computed: {
            visibleAlert() {
                return !this.model.startDate && !this.model.endDate;
            },
            employeeIds() {
                return this.generatedSalaries.salaries.filter(salary => salary.employee).map(salary => salary.employee.id);
            },
            checkedEmployees() {
                const {
                    checkedEmployeesIds
                } = this.model;
                return this.generatedSalaries.salaries.filter(salary => checkedEmployeesIds.includes(salary.employee.id));
            },
            fixSalariesEmployeeIds() {
                return this.fixSalaries.salaries.filter(salary => salary.employee).map(salary => salary.employee.id);
            },
            fixSalariesIds() {
                return this.fixSalaries.salaries.map(salary => salary.salary.id);
            },
            fixSalariesCheckedEmployees() {
                const {
                    checkedEmployeesIds
                } = this.model.fixSalaries;
                return this.fixSalaries.salaries.filter(salary => checkedEmployeesIds.includes(salary.employee.id));
            },
            goodToSave() {
                if (this.model.checkedEmployeesIds.length > 0) {
                    return true;
                }

                return false;
            },
            selectedDetailSalary() {
                const {
                    selectedDetailEmployeeId,
                } = this;
                const salaries = this.generatedSalaries.salaries;

                if (selectedDetailEmployeeId && salaries.length) {
                    const [salary] = salaries.filter(salary => salary.employee.id == selectedDetailEmployeeId);
                    if (salary) {
                        salary.temp_incomes = salary.incomes;
                        if (salary.employee.type == "staff") {
                            const incomes = salary.incomes.filter(income => income.type != "presence_incentive");
                            salary.temp_incomes = incomes;
                        }
                        return salary;
                    }
                }
                return null;
            },
            selectedDetailFixSalary() {
                const {
                    selectedDetailFixEmployeeId,
                } = this;
                const salaries = this.fixSalaries.salaries;

                if (selectedDetailFixEmployeeId && salaries.length) {
                    const [salary] = salaries.filter(salary => salary.employee.id == selectedDetailFixEmployeeId);
                    if (salary) {
                        return salary;
                    }
                }
                return null;
            }
        },
        methods: {
            checkAll(e) {
                const value = e.target.checked;
                // console.log(value)
                // console.log(e)
                if (value) {
                    const ids = this.generatedSalaries.salaries.filter(salary => salary.employee).map(salary => salary.employee.id);
                    // console.log(ids)
                    this.model.checkedEmployeesIds = ids;
                } else {
                    this.model.checkedEmployeesIds = [];
                }
            },
            fixSalariesCheckAll(e) {
                const value = e.target.checked;
                // console.log(value)
                // console.log(e)
                if (value) {
                    const ids = this.fixSalaries.salaries.map(salary => salary.salary.id);
                    // console.log(ids)
                    this.model.fixSalaries.checkedEmployeesIds = ids;
                } else {
                    this.model.fixSalaries.checkedEmployeesIds = [];
                }
            },
            async generate() {
                const {
                    month,
                    year,
                    staffOnly,
                    companyId,
                } = this.model;
                document.location.href = `/payrolls/monthly?month=${month}&year=${year}&staff_only=${staffOnly ? "true" : "false"}&company_id=${companyId}`;
            },
            async save() {
                const self = this;
                try {
                    self.saveLoading = true;

                    const body = {
                        salaries: self.checkedEmployees,
                        start_date: self.startDate,
                        end_date: self.endDate,
                    }

                    const response = await axios.post(`/salaries/action/bulk-save`, body);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        // const data = response?.data?.data;
                        toastr.success(message + '. Mengalihkan..');
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
                    self.saveLoading = false
                }
            },
            currencyFormat(number) {
                const numbered = Number(number);
                return new Intl.NumberFormat('De-de').format(numbered);
            },
            openDetailModal(employeeId) {
                this.selectedDetailEmployeeId = employeeId;
            },
            openDetailFixModal(employeeId) {
                this.selectedDetailFixEmployeeId = employeeId;
            },
            getTotalSummarySalaries(key) {
                return this.summarySalaries.salaries.map(salary => {
                    if (typeof salary[key] == "undefined") {
                        return 0;
                    }

                    return Number(salary[key]);
                }).reduce((acc, cur) => acc + cur, 0);
            },
            openBulkDeleteConfirmation() {
                const self = this;
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Data akan dihapus",
                    icon: 'warning',
                    reverseButtons: true,
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: "btn btn-danger",
                        cancelButton: "btn btn-light"
                    },
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return self.sendBulkDeleteRequest();
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            sendBulkDeleteRequest() {
                const self = this;
                const ids = self.model.fixSalaries.checkedEmployeesIds;
                return axios.delete('/salaries/action/bulk-delete', {
                        params: {
                            ids,
                        }
                    })
                    .then(function(response) {
                        console.log(response);
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil dihapus'
                        }
                        // self.deleteDesignation(id);
                        // redrawDatatable();
                        toastr.success(message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    })
                    .catch(function(error) {
                        console.error(error)
                        // console.log(error.data);
                        let message = error?.response?.data?.message;
                        if (!message) {
                            message = 'Something wrong...'
                        }
                        toastr.error(message);
                    });
            },
            openDesignationDeleteConfirmation(id) {
                const self = this;
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Data akan dihapus",
                    icon: 'warning',
                    reverseButtons: true,
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: "btn btn-danger",
                        cancelButton: "btn btn-light"
                    },
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return self.sendDesignationDeleteRequest(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            sendDesignationDeleteRequest(id) {
                const self = this;
                return axios.delete('/salaries/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil dihapus'
                        }
                        // self.deleteDesignation(id);
                        // redrawDatatable();
                        toastr.success(message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    })
                    .catch(function(error) {
                        console.error(error)
                        // console.log(error.data);
                        let message = error?.response?.data?.message;
                        if (!message) {
                            message = 'Something wrong...'
                        }
                        toastr.error(message);
                    });
            },
            async unpaid(salaryId) {
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Pembayaran akan dibatalkan. Aksi ini tidak mempengaruhi beban dan jurnal yang sudah terbuat. Lakukan penyesuaian mandiri di dalam aplikasi admin AerPlus",
                    icon: 'warning',
                    reverseButtons: true,
                    showCancelButton: true,
                    confirmButtonText: 'Batalkan Pembayaran',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: "btn btn-danger",
                        cancelButton: "btn btn-light"
                    },
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return axios.post('/salaries/action/unpaid', {
                                salary_ids: [salaryId],
                            })
                            .then(function(response) {
                                let message = response?.data?.message;
                                if (!message) {
                                    message = 'Pembayaran dibatalkan'
                                }

                                toastr.success(message);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);
                            })
                            .catch(function(error) {
                                console.error(error)
                                // console.log(error.data);
                                let message = error?.response?.data?.message;
                                if (!message) {
                                    message = 'Something wrong...'
                                }
                                toastr.error(message);
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                });
            }
        },
        watch: {
            // 'model.checkedAll': function(newValue) {
            //     if (newValue) {
            //         const ids = this.employeeIds;
            //         this.model.checkedEmployeesIds = ids;
            //     } else {
            //         this.model.checkedEmployeesIds = [];
            //     }
            // },
            'model.checkedEmployeesIds': function(newValue) {
                const {
                    employeeIds
                } = this;
                const diff = employeeIds.filter(id => !newValue.includes(id));

                if (diff.length > 0) {
                    // this.model.checkedAll = false;
                    document.getElementById('checkAll').checked = false;
                } else {
                    document.getElementById('checkAll').checked = true;
                }
            },
            'model.fixSalaries.checkedEmployeesIds': function(newValue) {
                const {
                    fixSalariesIds
                } = this;
                const diff = fixSalariesIds.filter(id => !newValue.includes(id));

                if (diff.length > 0) {
                    // this.model.checkedAll = false;
                    document.getElementById('fixSalariesCheckAll').checked = false;
                } else {
                    document.getElementById('fixSalariesCheckAll').checked = true;
                }
            },
        }
    })
</script>
<script>
    $(function() {
        $("#date-range-picker").daterangepicker({
            locale: {
                format: "DD/MM/YYYY"
            }
        }, function(start, end, label) {
            // alert("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            app.$data.model.startDate = start.format('YYYY-MM-DD');
            app.$data.model.endDate = end.format('YYYY-MM-DD');
        });

        const datatable = $('#employee_table').DataTable({
            "order": false,
            "columnDefs": [{
                "targets": 0,
                "orderable": false
            }],
            // "order": [
            //     [1, 'asc']
            // ],
            "drawCallback": function() {
                console.log('redraw table...')
            },
            "language": {
                "infoEmpty": " ",
                "zeroRecords": " "
            }
        });

        const filterSearch = document.querySelector('[employee-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });

        const fixSalariesDatatable = $('#fix_salaries_table').DataTable({
            "columnDefs": [{
                "targets": 0,
                "orderable": false
            }],
            "order": [
                [1, 'asc']
            ],
            "drawCallback": function() {
                console.log('redraw table...')
            },
            "language": {
                "infoEmpty": " ",
                "zeroRecords": " "
            }
        });

        const fixSalariesFilterSearch = document.querySelector('[fix-salaries-table-filter="search"]');
        fixSalariesFilterSearch.addEventListener('keyup', function(e) {
            fixSalariesDatatable.search(e.target.value).draw();
        });
    })
</script>
@endsection