@extends('layouts.app')

@section('title', 'Generate Gaji Harian')

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
    <div class="card mb-5 mb-xxl-10">
        <!--begin::Header-->
        <div class="card-header">
            <div class="card-title">
                <h3>Generate Gaji Harian</h3>
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body pb-0">
            <!--begin::Alert-->
            <div v-if="visibleAlert" v-cloak class="alert bg-light-warning d-flex flex-column flex-sm-row align-items-center w-100 p-5 mb-10">
                <!--begin::Icon-->
                <!--begin::Svg Icon | path: icons/duotune/files/fil024.svg-->
                <span class="svg-icon svg-icon-1 svg-icon-warning me-4 mb-5 mb-sm-0 align-middle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                        <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black" />
                        <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black" />
                    </svg>
                </span>
                <!--end::Svg Icon-->
                <!--end::Icon-->
                <!--begin::Content-->
                <div class="d-flex flex-column pe-0 pe-sm-10">
                    <span class="text-gray-700 fs-6">Pilih tanggal dan tekan tombol "Generate" untuk melihat gaji</span>
                </div>
                <!--end::Content-->
            </div>
            <!--end::Alert-->
            <div class="row align-items-end mb-6">
                <div class="col-md-8 col-lg-4">
                    <div class="mb-0">
                        <label class="form-label">Pilih Tanggal</label>
                        <input class="form-control form-control-solid" placeholder="Pilih tanggal" id="date-range-picker" />
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <!-- <button class="btn btn-primary">Generate</button> -->
                    <button type="button" :data-kt-indicator="generateLoading ? 'on' : null" class="btn btn-primary w-100" :disabled="generateLoading" @click="generate">
                        <span class="indicator-label">Generate</span>
                        <span class="indicator-progress">Mengirim data...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </div>
            <!--begin::Left Section-->
            <div class="row p-0 mb-5">
                <!--begin::Col-->
                <div class="col-sm-6">
                    <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                        <span class="fs-4 fw-bold text-primary d-block">Total Pegawai</span>
                        <span v-cloak class="fs-2hx fw-bolder text-gray-900 counted" data-kt-countup="true" data-kt-countup-value="36899">@{{ currencyFormat(totalEmployees) }}</span>
                    </div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-sm-6">
                    <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                        <span class="fs-4 fw-bold text-success d-block">Total THP</span>
                        <span v-cloak class="fs-2hx fw-bolder text-gray-900 counted" data-kt-countup="true" data-kt-countup-value="72">Rp @{{ currencyFormat(totalTakeHomePay) }}</span>
                    </div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <!-- <div class="col-sm-4">
                    <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                        <span class="fs-4 fw-bold text-danger d-block">Failed Attempts</span>
                        <span class="fs-2hx fw-bolder text-gray-900 counted" data-kt-countup="true" data-kt-countup-value="291">291</span>
                    </div>
                </div> -->
                <!--end::Col-->
            </div>
            <!--end::Left Section-->
        </div>
        <!--end::Body-->
    </div>
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <input type="text" v-model="model.searchEmployeeKeyword" employee-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Pegawai" id="input_search_employee" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <!--begin::Export-->
                    <button v-cloak v-if="goodToSave" type="button" :data-kt-indicator="saveLoading ? 'on' : null" class="btn btn-primary" :disabled="!goodToSave || saveLoading" @click="save">
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
                    <!--end::Export-->
                </div>
                <!--end::Toolbar-->
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed" id="employee_table">
                <!--begin::Table head-->
                <thead class="bg-light-primary">
                    <!--begin::Table row-->
                    <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                        <!-- <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#employee_table .form-check-input" value="1" />
                            </div>
                        </th> -->
                        <th class="ps-2">
                            <div class="form-check form-check-custom">
                                <input class="form-check-input" type="checkbox" @change="checkAll($event)" id="checkAll" />
                                <!-- <input class="form-check-input" type="checkbox" v-model="model.checkedAll" id="checkAll" /> -->
                                <label class="form-check-label" for="checkAll"></label>
                            </div>
                        </th>
                        <th class="min-w-125px">Nama</th>
                        <th class="">Job Title</th>
                        <th class="text-center">Jumlah Hari</th>
                        <th class="text-end">Take Home Pay</th>
                        <th class="text-end min-w-70px pe-2">Actions</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="fw-bold text-gray-600">
                    <tr v-cloak v-for="(salary, index) in searchedSalaries">
                        <td class="ps-2">
                            <div class="form-check form-check-custom">
                                <input class="form-check-input" type="checkbox" v-model="model.checkedEmployeesIds" :value="salary.employee.id" :id="'flexCheckDefault' + salary.employee.id" />
                                <label class="form-check-label" :for="'flexCheckDefault' + salary.employee.id"></label>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div>
                                    <a :href="'/employees/' + salary?.employee?.id + '/detail'" target="_blank" class="text-gray-800 text-hover-primary fs-6 fw-bolder mb-1">@{{ salary?.employee?.name }}</a>
                                </div>
                                <span class="text-muted">@{{ salary?.employee?.number }}</span>
                            </div>
                        </td>
                        <td>
                            <div v-if="salary.employee.active_career">
                                <span class="text-gray-700 fs-6">@{{ salary?.employee?.active_career?.job_title?.name }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="text-gray-700 fs-6">@{{ salary?.number_of_days }}</span>
                        </td>
                        <td class="text-end">
                            <span class="text-gray-800 fs-6">@{{ currencyFormat(salary?.summary?.take_home_pay) }}</span>
                        </td>
                        <td class="text-end pe-2">
                            <button type="button" class="btn btn-light btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#detail_modal" @click="openDetailModal(salary?.employee?.id)">
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
        <!--end::Card body-->
    </div>
    <!--end::Card-->
    <div class="modal fade" tabindex="-1" id="detail_modal">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content shadow-none">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Gaji</h5>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <div v-if="selectedDetailSalary">
                        <div class="d-flex flex-center flex-column">
                            <!--begin::Name-->
                            <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bolder mb-0">@{{ selectedDetailSalary?.employee?.number }} - @{{ selectedDetailSalary?.employee?.name }}</a>
                            <!--end::Name-->
                            <!--begin::Position-->
                            <div class="fw-bold text-gray-400 mb-6">@{{ selectedDetailSalary?.employee?.active_career?.job_title?.name }}</div>
                            <!--end::Position-->
                        </div>
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-striped">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th>Tanggal</th>
                                        <th>Hari</th>
                                        <th>Kalender</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Masuk</th>
                                        <th class="text-center">Pulang</th>
                                        <th class="text-center">Lembur (Jam)</th>
                                        <th class="text-center">Long Shift</th>
                                        <th class="text-center">Keterlambatan</th>
                                        <th class="text-end">Uang Harian</th>
                                        <th class="text-end">Uang Lembur</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(salary, index) in selectedDetailSalary.periods" class="fw-bold fs-6 text-gray-700">
                                        <td class="text-gray-600 ps-2">@{{ salary.date }}</td>
                                        <td class="text-gray-700">
                                            <div>
                                                <span v-if="salary.attendance.working_pattern_day == 'holiday' || salary.attendance.event_calendars.length" class="text-danger"><strong>@{{ salary.day_name }}</strong></span>
                                                <span v-else><strong>@{{ salary.day_name }}</strong></span>
                                            </div>
                                            <div>
                                                <small> @{{ salary.office_name }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div v-for="event in salary.events" class="mb-3">
                                                <span class="badge badge-light-danger">@{{ event.name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center text-uppercase">
                                            <span class="text-uppercase" :class="badgeColor(salary.attendance.status)">@{{ salary.attendance.status }}</span>
                                        </td>
                                        <td class="text-center text-gray-600">@{{ salary.attendance.clock_in_time }}</td>
                                        <td class="text-center text-gray-600">@{{ salary.attendance.clock_out_time }}</td>
                                        <td class="text-center text-gray-600">@{{ Math.floor((salary?.attendance?.overtime || 0) / 60) }}</td>
                                        <td class="text-center text-gray-600">@{{ salary.attendance.is_long_shift == 1 ? 'Ya' : '' }}</td>
                                        <td class="text-center text-gray-600">@{{ salary.attendance.time_late }}</td>
                                        <!-- <td class="text-end text-gray-700">@{{ currencyFormat(salary.daily_wage) }}</td> -->
                                        <td class="text-end text-gray-700">
                                            <div v-if="salary.editing_daily_wage">
                                                <div class="input-group input-group-sm mb-5">
                                                    <span class="input-group-text" id="basic-addon1">Rp</span>
                                                    <input type="text" v-model="salary.daily_wage" class="form-control text-end" @blur="onBlurDailyWage(salary)" @input="onInputDailyWage(salary)">
                                                </div>
                                            </div>
                                            <div v-else>
                                                <span @click="onClickDailyWage(salary)">@{{ currencyFormat(salary.daily_wage) }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end text-gray-700">
                                            <div v-if="salary.editing_overtime_pay">
                                                <div class="input-group input-group-sm mb-5">
                                                    <span class="input-group-text" id="basic-addon1">Rp</span>
                                                    <input type="text" v-model="salary.overtime_pay" class="form-control text-end" @blur="onBlurOvertimePay(salary)" @input="onInputOvertimePay(salary)">
                                                </div>
                                            </div>
                                            <div v-else>
                                                <span @click="onClickOvertimePay(salary)">@{{ currencyFormat(salary.overtime_pay) }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end text-gray-700 pe-2">@{{ currencyFormat(salary.total) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-light-success">
                                    <tr>
                                        <td colspan="9"></td>
                                        <td class="text-end fw-bold fs-5 text-gray-800">@{{ currencyFormat(totalDailyWage) }}</td>
                                        <td class="text-end fw-bold fs-5 text-gray-800">@{{ currencyFormat(totalOvertimePay) }}</td>
                                        <td class="text-end fw-bolder fs-4 text-gray-800 pe-2">@{{ currencyFormat(totalIncomes) }}</td>
                                    </tr>
                                </tfoot>
                                <!-- <tfoot>
                                    <tr>
                                        <td colspan="9"></td>
                                        <td class="text-end fw-bold fs-3">Rp @{{ currencyFormat(selectedDetailSalary?.summary?.total_incomes) }}</td>
                                    </tr>
                                </tfoot> -->
                            </table>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-md-6 col-lg-4">
                                <table class="table">
                                    <tbody class="fs-5 fw-bold bg-light-success">
                                        <!-- <tr>
                                            <td class="ps-2 fw-normal">Total Harian</td>
                                            <td class="text-end pe-2">Rp @{{ currencyFormat(selectedDetailSalary?.total_periods) }}</td>
                                        </tr> -->
                                        <tr v-for="additionalIncome in selectedDetailSalary?.additional_incomes">
                                            <td class="ps-2 fw-normal">@{{ additionalIncome?.name }}</td>
                                            <td class="text-end pe-2">Rp @{{ currencyFormat(additionalIncome?.amount || 0) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div>
                            <h4>Pendapatan Lainnya</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                            <th></th>
                                            <th>Nama</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="selectedDetailSalary.total_unredeemed_deposits > 0" class="fw-bold fs-6 text-gray-700">
                                            <td style="width: 50px;">
                                                <div class="form-check form-check-custom form-check-primary">
                                                    <input class="form-check-input" type="checkbox" v-model="selectedDetailSalary.include_unredeemed_deposit" @change="onChangeIncludeUnredeemedDeposit(selectedDetailSalary)" />
                                                </div>
                                            </td>
                                            <td><span :class="!selectedDetailSalary.include_unredeemed_deposit ? 'text-decoration-line-through fst-italic' : ''">Pengembalian Deposit</span></td>
                                            <td class="text-end"><span :class="!selectedDetailSalary.include_unredeemed_deposit ? 'text-decoration-line-through fst-italic' : ''">@{{ currencyFormat(selectedDetailSalary.total_unredeemed_deposits) }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="text-end py-3">
                            <span class="fw-bold fs-3">Rp @{{ currencyFormat(selectedDetailSalary?.summary?.total_incomes) }}</span>
                        </div>
                        <div class="separator separator-dashed mb-5"></div>
                        <div>
                            <h4>Potongan</h4>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                            <th></th>
                                            <th>Nama</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="selectedDetailSalary.late_charge > 0" class="fw-bold fs-6 text-gray-700">
                                            <td></td>
                                            <td>Denda keterlambatan @{{ selectedDetailSalary.total_time_late }} menit</td>
                                            <td class="text-end">@{{ currencyFormat(selectedDetailSalary.late_charge) }}</td>
                                        </tr>
                                        <tr v-if="selectedDetailSalary.total_unpaid_deposits > 0" class="fw-bold fs-6 text-gray-700">
                                            <td style="width: 50px;">
                                                <div class="form-check form-check-custom form-check-primary">
                                                    <input class="form-check-input" type="checkbox" v-model="selectedDetailSalary.include_deposit" @change="onChangeIncludeDeposit(selectedDetailSalary)" />
                                                </div>
                                            </td>
                                            <td><span :class="!selectedDetailSalary.include_deposit ? 'text-decoration-line-through fst-italic' : ''">Deposit</span></td>
                                            <td class="text-end"><span :class="!selectedDetailSalary.include_deposit ? 'text-decoration-line-through fst-italic' : ''">@{{ currencyFormat(selectedDetailSalary.total_unpaid_deposits) }}</span></td>
                                        </tr>
                                        <!-- <tr v-if="selectedDetailSalary.total_loan > 0" class="fw-bold fs-6 text-gray-700">
                                            <td style="width: 50px;">
                                                <div class="form-check form-check-custom form-check-primary">
                                                    <input class="form-check-input" type="checkbox" v-model="selectedDetailSalary.include_loan" @change="onChangeIncludeLoan(selectedDetailSalary)" />
                                                </div>
                                            </td>
                                            <td><span :class="!selectedDetailSalary.include_loan ? 'text-decoration-line-through fst-italic' : ''">Kasbon</span></td>
                                            <td class="text-end"><span :class="!selectedDetailSalary.include_loan ? 'text-decoration-line-through fst-italic' : ''">@{{ currencyFormat(selectedDetailSalary.total_loan) }}</span></td>
                                        </tr> -->
                                        <tr class="fw-bold fs-6 text-gray-700">
                                            <td style="width: 50px;">
                                                <div class="form-check form-check-custom form-check-primary">
                                                    <input class="form-check-input" type="checkbox" v-model="selectedDetailSalary.include_loan" @change="onChangeIncludeLoan(selectedDetailSalary)" />
                                                </div>
                                            </td>
                                            <td><span :class="!selectedDetailSalary.include_loan ? 'text-decoration-line-through fst-italic' : ''">Kasbon</span></td>
                                            <!-- <td class="text-end"><span :class="!selectedDetailSalary.include_loan ? 'text-decoration-line-through fst-italic' : ''">@{{ currencyFormat(selectedDetailSalary.total_loan) }}</span></td> -->
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end">
                                                    <select v-model="selectedDetailSalary.selected_loan_id" class="form-select" style="width: 300px;">
                                                        <option value="">Pilih Kasbon</option>
                                                        <template v-for="loan in selectedDetailSalary?.loans">
                                                            <option v-for="loanItem in selectedDetailSalary?.loan_items" :value="loanItem.id">@{{ loan.description }} - Cicilan @{{ moment(loanItem?.payment_date).format('MMM YYYY') }} - Rp @{{ loanItem?.basic_payment?.toLocaleString('De-de') }}</option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-for="(otherDeduction, index) in selectedDetailSalary.other_deductions">
                                            <td></td>
                                            <td>
                                                <select class="form-select" v-model="otherDeduction.type">
                                                    <option value="">Pilih Potongan</option>
                                                    <option value="late_fee">Denda</option>
                                                </select>
                                            </td>
                                            <td class="d-flex justify-content-end">
                                                <div class="me-2">
                                                    <input type="number" v-model="otherDeduction.amount" class="form-control" style="width: 200px;">
                                                </div>
                                                <div>
                                                    <button class="btn btn-icon btn-danger" @click="deleteOtherDeduction(index)"><i class="bi bi-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end">
                                                <button class="btn btn-primary" @click="addOtherDeduction">Tambah Potongan</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <!-- <tfoot>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end fw-bold fs-3">Rp @{{ currencyFormat(selectedDetailSalary?.summary?.total_deductions) }}</td>
                                        </tr>
                                    </tfoot> -->
                                </table>
                            </div>
                        </div>
                        <div class="row justify-content-end mt-10">
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between mb-5">
                                    <h5 class="mb-0 text-gray-700">Pendapatan</h5>
                                    <h5 class="mb-0 text-gray-800">Rp @{{ currencyFormat(totalIncomes) }}</h5>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-0 text-gray-700">Potongan</h5>
                                    <h5 class="mb-0 text-gray-800">(Rp @{{ currencyFormat(totalDeductions) }})</h5>
                                </div>

                                <div class="separator separator-dashed my-3"></div>
                                <div class="d-flex justify-content-between my-3 bg-light-success p-5 border-success border-bottom-dashed border-top-dashed">
                                    <h3 class="mb-0 text-gray-800">TAKE HOME PAY</h3>
                                    <h3 class="mb-0 text-gray-800">Rp @{{ currencyFormat(takeHomePay) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <h3 class="text-center text-gray-800">Pilih Pegawai</h3>
                    </div>
                </div>

                <!-- <div class="modal-body">
                    <div v-if="selectedDetailSalary">
                        <div class="d-flex flex-center flex-column">
                            <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bolder mb-0">@{{ selectedDetailSalary?.employee?.name }}</a>
                            <div class="fw-bold text-gray-400 mb-6">@{{ selectedDetailSalary?.employee?.active_career?.job_title?.name }}</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th>Tanggal</th>
                                        <th>Hari</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Masuk</th>
                                        <th class="text-center">Pulang</th>
                                        <th class="text-center">Lembur</th>
                                        <th class="text-center">Keterlambatan</th>
                                        <th class="text-end">Uang Harian</th>
                                        <th class="text-end">Uang Lembur</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(salary, index) in selectedDetailSalary.periods">
                                        <td>@{{ salary.date }}</td>
                                        <td>
                                            <span v-if="salary.attendance.working_pattern_day == 'holiday' || salary.attendance.event_calendars.length" class="text-danger">@{{ salary.day_name }}</span>
                                            <span v-else>@{{ salary.day_name }}</span>
                                        </td>
                                        <td class="text-center text-uppercase">@{{ salary.attendance.status }}</td>
                                        <td class="text-center">@{{ salary.attendance.clock_in_time }}</td>
                                        <td class="text-center">@{{ salary.attendance.clock_in_time }}</td>
                                        <td class="text-center">@{{ salary.attendance.overtime }}</td>
                                        <td class="text-center">@{{ salary.attendance.time_late }}</td>
                                        <td class="text-end">@{{ currencyFormat(salary.daily_wage) }}</td>
                                        <td class="text-end">@{{ currencyFormat(salary.overtime_pay) }}</td>
                                        <td class="text-end">@{{ currencyFormat(salary.total) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="9"></td>
                                        <td class="text-end fw-bold">@{{ currencyFormat(selectedDetailSalary?.summary?.total_incomes) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div v-if="selectedDetailSalary.late_charge > 0">
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
                                        <tr>
                                            <td>Denda keterlambatan @{{ selectedDetailSalary.total_time_late }} menit</td>
                                            <td class="text-end">@{{ currencyFormat(selectedDetailSalary.late_charge) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td class="text-end fw-bold">@{{ currencyFormat(selectedDetailSalary.late_charge) }}</td>
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
                </div> -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-bs-toggle="tooltip" data-bs-placement="top" title="Perubahan tidak akan tersimpan" @click="onDismissDetailModal">Tutup</button>
                    <button type="button" class="btn btn-primary" @click="onSaveDetailModal"><i class="bi bi-save"></i> Simpan</button>
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

    const closeModal = (selector) => {
        const el = document.querySelector(selector);
        const modal = bootstrap.Modal.getInstance(el);
        modal.hide();
    }

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                model: {
                    checkedAll: false,
                    checkedEmployeesIds: [],
                    startDate: '{{ date("Y-m-d") }}',
                    endDate: '{{ date("Y-m-d") }}',
                    searchEmployeeKeyword: '',
                },
                generateLoading: false,
                saveLoading: false,
                salaries: [],
                totalEmployees: 0,
                totalTakeHomePay: 0,
                selectedDetailEmployeeId: null,
                tempSalaries: [],
            }
        },
        computed: {
            visibleAlert() {
                return !this.model.startDate && !this.model.endDate;
            },
            employeeIds() {
                return this.salaries.filter(salary => salary.employee).map(salary => salary.employee.id);
            },
            checkedEmployees() {
                const {
                    checkedEmployeesIds
                } = this.model;
                return this.salaries.filter(salary => checkedEmployeesIds.includes(salary.employee.id));
            },
            goodToSave() {
                if (this.model.checkedEmployeesIds.length > 0) {
                    return true;
                }

                return false;
            },
            searchedSalaries() {
                const keyword = this.model.searchEmployeeKeyword;
                return this.salaries.filter(salary => salary.employee.name.toLowerCase().indexOf(keyword) > -1);
            },
            selectedDetailSalary() {
                const {
                    selectedDetailEmployeeId,
                    salaries
                } = this;
                if (selectedDetailEmployeeId && salaries.length) {
                    const [salary] = salaries.filter(salary => salary.employee.id == selectedDetailEmployeeId);
                    if (salary) {
                        return salary;
                    }
                }
                return null;
            },
            totalDailyWage() {
                const self = this;
                const total = self.selectedDetailSalary.periods.map(period => Number(period.daily_wage)).reduce((acc, cur) => acc + cur, 0);
                return total;
            },
            totalOvertimePay() {
                const self = this;
                const total = self.selectedDetailSalary.periods.map(period => Number(period.overtime_pay)).reduce((acc, cur) => acc + cur, 0);
                return total;
            },
            totalIncomes() {
                return this.totalDailyWage + this.totalOvertimePay;
            },
            totalOtherDeductions() {
                const self = this;
                const total = self.selectedDetailSalary.other_deductions.map(deduction => Number(deduction.amount)).reduce((acc, cur) => acc + cur, 0);
                return total;
            },
            totalDeductions() {
                // return this.selectedDetailSalary?.summary?.total_deductions || 0;
                const totalLoan = this.selectedDetailSalary?.total_loan || 0;
                const totalOtherDeductions = this.totalOtherDeductions;
                const total = totalLoan + totalOtherDeductions;

                return total;
            },
            takeHomePay() {
                return this.totalIncomes - this.totalDeductions;
            },
            totalAllTakeHomePay() {
                return this.salaries.map(salary => Number(salary.summary.take_home_pay)).reduce((acc, cur) => acc + cur, 0);
            }
        },
        methods: {
            addOtherDeduction() {
                return this.selectedDetailSalary.other_deductions.push({
                    type: '',
                    amount: 0,
                });
            },
            deleteOtherDeduction(index) {
                return this.selectedDetailSalary.other_deductions.splice(index, 1);
            },
            moment(date) {
                return moment(date);
            },
            onChangeIncludeDeposit(selectedDetailSalary) {
                this.$nextTick(() => {
                    const includeDeposit = selectedDetailSalary.include_deposit;
                    if (includeDeposit) {
                        selectedDetailSalary.summary.total_deductions += selectedDetailSalary.total_unpaid_deposits;
                    } else {
                        selectedDetailSalary.summary.total_deductions -= selectedDetailSalary.total_unpaid_deposits;
                    }

                    selectedDetailSalary.summary.take_home_pay = selectedDetailSalary.summary.total_incomes - selectedDetailSalary.summary.total_deductions;
                    // console.log();
                });
            },
            onChangeIncludeUnredeemedDeposit(selectedDetailSalary) {
                this.$nextTick(() => {
                    const includeDeposit = selectedDetailSalary.include_unredeemed_deposit;
                    if (includeDeposit) {
                        selectedDetailSalary.summary.total_incomes += selectedDetailSalary.total_unredeemed_deposits;
                    } else {
                        selectedDetailSalary.summary.total_incomes -= selectedDetailSalary.total_unredeemed_deposits;
                    }

                    selectedDetailSalary.summary.take_home_pay = selectedDetailSalary.summary.total_incomes - selectedDetailSalary.summary.total_deductions;
                    // console.log();
                });
            },
            onChangeIncludeLoan(selectedDetailSalary, previousSelectedLoanId = null) {
                this.$nextTick(() => {
                    // const includeLoan = selectedDetailSalary.include_loan;
                    // if(includeLoan) {
                    //     selectedDetailSalary.summary.total_deductions += selectedDetailSalary.total_loan;
                    // } else {
                    //     selectedDetailSalary.summary.total_deductions -= selectedDetailSalary.total_loan;
                    // }

                    // selectedDetailSalary.summary.take_home_pay = selectedDetailSalary.summary.total_incomes - selectedDetailSalary.summary.total_deductions;



                    // if (!selectedLoanId) {
                    //     totalLoan = 0;
                    // }

                    const includeLoan = selectedDetailSalary?.include_loan;

                    let previousTotalLoan = 0;
                    if (previousSelectedLoanId) {
                        const [selectedPreviousLoan] = selectedDetailSalary?.loan_items.filter(loanItem => loanItem.id == previousSelectedLoanId);
                        if (selectedPreviousLoan) {
                            previousTotalLoan = selectedPreviousLoan?.basic_payment || 0;
                        }
                    }

                    const selectedLoanId = selectedDetailSalary?.selected_loan_id;
                    const [selectedLoan] = selectedDetailSalary?.loan_items.filter(loanItem => loanItem.id == selectedLoanId);

                    let totalLoan = 0;

                    if (selectedLoan) {
                        totalLoan = selectedLoan?.basic_payment;
                    }
                    totalLoan = totalLoan - previousTotalLoan;

                    if (includeLoan) {

                        console.log('selected loan', selectedLoan);
                        selectedDetailSalary.total_loan = totalLoan;
                        selectedDetailSalary.summary.total_deductions += totalLoan;
                    } else {
                        if (!selectedLoanId) {
                            selectedDetailSalary.total_loan = 0;
                        }
                        selectedDetailSalary.summary.total_deductions -= selectedDetailSalary.total_loan;
                    }

                    selectedDetailSalary.summary.take_home_pay = selectedDetailSalary.summary.total_incomes - selectedDetailSalary.summary.total_deductions;
                    // console.log();
                });
            },
            checkAll(e) {
                const value = e.target.checked;
                // console.log(value)
                // console.log(e)
                if (value) {
                    const ids = this.salaries.filter(salary => salary.employee).map(salary => salary.employee.id);
                    // console.log(ids)
                    this.model.checkedEmployeesIds = ids;
                } else {
                    this.model.checkedEmployeesIds = [];
                }
            },
            async generate() {
                const self = this;
                try {
                    const startDate = this.model.startDate;
                    const endDate = this.model.endDate;

                    if (!startDate || !endDate) {
                        Swal.fire({
                            text: "Pilih tanggal",
                            icon: "warning",
                            buttonsStyling: false,
                            confirmButtonText: "Tutup",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });

                        return null;
                    }

                    self.generateLoading = true;

                    const response = await axios.get(`/daily-salaries/action/generate-aerplus?start_date=${startDate}&end_date=${endDate}`);

                    if (response) {
                        // console.log(response);
                        const salaries = response?.data?.data?.salaries;
                        const totalEmployees = response?.data?.data?.total_employees;
                        const totalTakeHomePay = response?.data?.data?.total_take_home_pay;

                        if (salaries) {
                            self.salaries = salaries;
                        }

                        self.totalEmployees = totalEmployees;
                        self.totalTakeHomePay = totalTakeHomePay;
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.generateLoading = false
                }
            },
            async save() {
                const self = this;
                try {
                    const startDate = this.model.startDate;
                    const endDate = this.model.endDate;

                    if (!startDate || !endDate) {
                        Swal.fire({
                            text: "Pilih tanggal",
                            icon: "warning",
                            buttonsStyling: false,
                            confirmButtonText: "Tutup",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });

                        return null;
                    }

                    self.saveLoading = true;

                    const body = {
                        salaries: self.checkedEmployees,
                        start_date: startDate,
                        end_date: endDate,
                    }

                    const response = await axios.post(`/daily-salaries/action/bulk-save-aerplus`, body);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        // const data = response?.data?.data;
                        toastr.success(message);
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
                } finally {
                    self.saveLoading = false
                }
            },
            currencyFormat(number) {
                return new Intl.NumberFormat('De-de').format(number);
            },
            openDetailModal(employeeId) {
                // this.tempSalaries = this.salaries.map(salary => salary);
                this.tempSalaries = JSON.parse(JSON.stringify(this.salaries));
                this.selectedDetailEmployeeId = employeeId;
            },
            badgeColor(status) {
                const prefix = 'badge badge-';
                switch (status) {
                    case 'hadir':
                        return prefix + 'success';
                    case 'izin':
                        return prefix + 'primary';
                    case 'cuti':
                        return prefix + 'info';
                    case 'sakit':
                        return prefix + 'warning';
                    default:
                        return '';
                }
            },
            onClickDailyWage(salary) {
                // const {
                //     selectedDetailEmployeeId,
                //     salaries
                // } = this;
                // if (selectedDetailEmployeeId && salaries.length) {
                //     const salaryIndex = salaries.findIndex(salary => salary.employee.id == selectedDetailEmployeeId);

                //     this.salaries[salaryIndex].periods[index].editing_daily_wage = true;
                // }
                salary.editing_daily_wage = true;
            },
            onBlurDailyWage(salary) {
                salary.editing_daily_wage = false;
            },
            onInputDailyWage(salary) {
                salary.total = Number(salary.daily_wage) + Number(salary.overtime_pay);
            },
            onClickOvertimePay(salary) {
                salary.editing_overtime_pay = true;
            },
            onBlurOvertimePay(salary) {
                salary.editing_overtime_pay = false;
            },
            onInputOvertimePay(salary) {
                salary.total = Number(salary.daily_wage) + Number(salary.overtime_pay);
            },
            onDismissDetailModal() {
                console.log('on dismiss');
                this.salaries = this.tempSalaries;
            },
            onSaveDetailModal() {
                this.selectedDetailSalary.summary.total_incomes = this.totalIncomes;
                this.selectedDetailSalary.summary.total_deductions = this.totalDeductions;
                this.selectedDetailSalary.summary.take_home_pay = this.takeHomePay;
                // this.totalTakeHomePay += this.takeHomePay;
                closeModal('#detail_modal');
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
            'selectedDetailSalary.selected_loan_id': function(newValue, oldValue) {
                const self = this;
                // if (this.selectedDetailSalary) {
                //     this.onChangeIncludeLoan(this.selectedDetailSalary, oldValue);
                // };
            }
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

        // datatable = $('#employee_table').DataTable({
        //     "drawCallback": function() {
        //         console.log('redraw table...')
        //     },
        //     "language": {
        //         "infoEmpty": " ",
        //         "zeroRecords": " "
        //     }
        // });

        // const filterSearch = document.querySelector('[employee-table-filter="search"]');
        // filterSearch.addEventListener('keyup', function(e) {
        //     datatable.search(e.target.value).draw();
        // });
    })
</script>
@endsection