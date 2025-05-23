@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('prehead')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.1.0/css/fixedColumns.dataTables.min.css">
@endsection

@inject('carbon', 'Carbon\Carbon')

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!--begin::Row-->
    <!--begin::Main column-->
    <div class="d-flex flex-column flex-lg-row-fluid gap-7 gap-lg-10">
        <!--begin::Order details-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Kenaikan Gaji</h2>
                </div>
                <!-- <div class="card-toolbar">
                    <button v-cloak v-if="model.componentId" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_add_employee">Tambah</button>
                </div> -->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-10">
                    <div class="border rounded p-8">
                        <div class="row">
                            <div class="col-md-3">
                                <!--begin::Label-->
                                <label class="form-label">Perusahaan</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="model.companyId" class="form-select form-select-sm">
                                    <option value="">Pilih Perusahaan</option>
                                    <option v-for="(company, index) in companies" :key="company.id" :value="company.id">@{{ company.name }}</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <div class="col-md-3">
                                <!--begin::Label-->
                                <label class="form-label">Divisi</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="model.divisionId" class="form-select form-select-sm">
                                    <option value="">Pilih Divisi</option>
                                    <option v-for="(division, index) in filteredDivisions" :key="division.id" :value="division.id">@{{ division.name }}</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <div class="col-md-3">
                                <!--begin::Label-->
                                <label class="form-label">Periode (Mulai Berlaku Gaji)</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input v-model="model.effectiveDate" type="month" class="form-control form-control-sm">
                                <!--end::Input-->
                            </div>
                        </div>
                    </div>
                    <!--begin::Search products-->
                    <div class="d-flex align-items-center position-relative mb-n7">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span class="svg-icon svg-icon-1 position-absolute ms-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <input type="text" data-salary-increase-filter="search" class="form-control form-control-solid w-100 w-lg-50 ps-14" placeholder="Cari Pegawai" />
                    </div>
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table align-middle table-striped table-row-bordered fs-6 gy-5" id="salary-increases-datatable">
                            <!--begin::Table head-->
                            <thead>
                                <!--begin::Table row-->
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-250px text-start align-middle ps-3" rowspan="2">Nama</th>
                                    <th class="min-w-250px text-start align-middle ps-3" rowspan="2">NPWP</th>
                                    <th class="min-w-150px text-start align-middle" rowspan="2">Status PTKP</th>
                                    <th class="min-w-150px text-start align-middle" rowspan="2">Tgl. Masuk</th>
                                    <!-- <th class="text-center" colspan="5">Saat Ini</th> -->
                                    <th class="text-center" colspan="7">Honor 2021</th>
                                    <th class="text-center" colspan="7">Honor 2022</th>
                                    <!-- <th class="text-center" colspan="5">Baru</th> -->
                                </tr>
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <!-- Saat ini -->
                                    <!-- <th class="text-end">Gaji Pokok</th>
                                    <th class="text-end">Tunjangan</th>
                                    <th class="text-end">Harian</th>
                                    <th class="text-end">Lembur</th>
                                    <th class="text-end">Bonus</th> -->
                                    <!-- Kenikan -->
                                    <th class="text-end min-w-150px">Bulanan</th>
                                    <th class="text-end min-w-150px">Tunjangan</th>
                                    <th class="text-end min-w-150px">Harian</th>
                                    <th class="text-end min-w-100px">Koef. Harian</th>
                                    <th class="text-end min-w-150px">Lembur</th>
                                    <th class="text-end min-w-100px">Koef. Lembur</th>
                                    <th class="text-end min-w-150px">Total</th>
                                    <th class="text-end min-w-200px">Bulanan (%)</th>
                                    <th class="text-end min-w-200px">Tunjangan (%)</th>
                                    <th class="text-end min-w-200px">Harian (%)</th>
                                    <th class="text-end min-w-100px">Koef. Harian</th>
                                    <th class="text-end min-w-200px">Lembur (%)</th>
                                    <th class="text-end min-w-100px">Koef. Lembur</th>
                                    <th class="text-end min-w-200px">Total (%)</th>
                                    <!-- Baru -->
                                    <!-- <th class="text-end">Gaji Pokok</th>
                                    <th class="text-end">Tunjangan</th>
                                    <th class="text-end">Harian</th>
                                    <th class="text-end">Lembur</th>
                                    <th class="text-end">Bonus</th> -->
                                </tr>
                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="text-gray-600">
                                <!--begin::Table row-->
                                <tr v-cloak v-if="employeesByDivision.length < 1">
                                    <td colspan="10">
                                        <div class="text-center">
                                            <em>Tidak ada data</em>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-cloak v-else v-for="(employee, index) in employeesByDivision" :key="employee.id">
                                    <!--begin::Category=-->
                                    <td>
                                        <div class="ps-3 border-end">
                                            <div class="text-gray-800 text-hover-primary fs-6 fw-bolder mb-1">@{{ employee.name }}</div>
                                            <span class="text-muted">@{{ employee.number }}</span>
                                        </div>
                                    </td>
                                    <!--end::Category=-->
                                    <!--begin::NPWP-->
                                    <td>
                                        <span class="text-gray-800 fs-6 mb-1 text-uppercase">@{{ employee?.npwp_number }}</span>
                                    </td>
                                    <!--end::NPWP-->
                                    <!--begin::Status PTKP-->
                                    <td>
                                        <span class="text-gray-800 fs-6 mb-1 text-uppercase">@{{ employee?.npwp_status }}</span>
                                    </td>
                                    <!--end::Status PTKP-->
                                    <!--begin::Tanggal Masuk-->
                                    <td>
                                        <span class="text-gray-800 fs-6 mb-1">@{{ moment(employee?.start_work_date).format('DD MMMM YYYY') }}</span>
                                    </td>
                                    <!--end::Tanggal Masuk-->
                                    <!-- begin::Gaji Sekarang ================================= -->


                                    <!-- end::Gaji Sekarang ================================= -->
                                    <!-- begin::gaji ================================= -->
                                    <!--begin::gaji Pokok-->
                                    <td>
                                        <!-- <div v-if="haveSalaryComponent(employee, 'gaji_pokok')">
                                        </div> -->
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" v-model="employee.gaji_pokok_value" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat" disabled>
                                        </div>
                                    </td>
                                    <!--end::gaji Pokok-->
                                    <!--begin::Kenaikan Persentase Gaji Pokok-->

                                    <!--end::Kenaikan Persentase Gaji Pokok-->
                                    <!--begin::Kenaikan Tunjangan-->
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" v-model="employee.tunjangan_value" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat" disabled>
                                        </div>
                                    </td>
                                    <!--end::Kenaikan Tunjangan-->
                                    <!--begin::Kenaikan Persentase Tunjangan-->

                                    <!--end::Kenaikan Persentase Tunjangan-->
                                    <!--begin::Kenaikan Harian-->
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" v-model="employee.uang_harian_value" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat" disabled>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" v-model="employee.uang_harian_coefficient" class="form-control text-end form-control-sm" disabled>
                                    </td>
                                    <!--end::Kenaikan Harian-->
                                    <!--begin::Kenaikan Persentase Harian-->

                                    <!--end::Kenaikan Persentase Harian-->
                                    <!--begin::Kenaikan Lembur-->
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" v-model="employee.lembur_value" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat" disabled>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" v-model="employee.lembur_coefficient" class="form-control text-end form-control-sm" disabled>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" :value="currencyFormat(getCurrentSalaryTotal(employee))" class="form-control text-end form-control-sm" disabled>
                                        </div>
                                        <!-- <span class="text-gray-800 fs-6 mb-1 fw-bold">@{{ currencyFormat(getCurrentSalaryTotal(employee)) }}</span> -->
                                    </td>
                                    <!--end::Kenaikan Lembur-->
                                    <!--begin::Kenaikan Persentase Lembur-->
                                    <!-- end::gaji ================================= -->

                                    <!-- begin::Gaji Baru =================================-->
                                    <!-- begin::gaji ================================= -->
                                    <!--begin::gaji Pokok-->
                                    <td>
                                        <!-- <div v-if="haveSalaryComponent(employee, 'gaji_pokok')">
                                        </div> -->
                                        <div class="row align-items-center">
                                            <div class="col-md-9">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" v-model="employee.gaji_pokok_new_value" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat">
                                                </div>
                                            </div>
                                            <div class="col-md-3" v-html="getSalaryPercentage(employee, 'gaji_pokok')">
                                                <!-- <div class="d-flex align-items-center">
                                                    <span class="svg-icon svg-icon-success">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.5" d="M13 9.59998V21C13 21.6 12.6 22 12 22C11.4 22 11 21.6 11 21V9.59998H13Z" fill="black" />
                                                            <path d="M5.7071 7.89291C5.07714 8.52288 5.52331 9.60002 6.41421 9.60002H17.5858C18.4767 9.60002 18.9229 8.52288 18.2929 7.89291L12.7 2.3C12.3 1.9 11.7 1.9 11.3 2.3L5.7071 7.89291Z" fill="black" />
                                                        </svg>
                                                    </span>
                                                    <span class="text-success">@{{ getSalaryPercentage(employee, 'gaji_pokok').value }}%</span>
                                                </div> -->
                                            </div>
                                        </div>
                                    </td>
                                    <!--end::gaji Pokok-->
                                    <!--begin::Kenaikan Persentase Gaji Pokok-->

                                    <!--end::Kenaikan Persentase Gaji Pokok-->
                                    <!--begin::Kenaikan Tunjangan-->
                                    <td>
                                        <div class="row align-items-center">
                                            <div class="col-md-9">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" v-model="employee.tunjangan_new_value" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat">
                                                </div>
                                            </div>
                                            <div class="col-md-3" v-html="getSalaryPercentage(employee, 'tunjangan')">
                                                <!-- <div class="d-flex align-items-center">
                                                    <span class="svg-icon svg-icon-success">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.5" d="M13 9.59998V21C13 21.6 12.6 22 12 22C11.4 22 11 21.6 11 21V9.59998H13Z" fill="black" />
                                                            <path d="M5.7071 7.89291C5.07714 8.52288 5.52331 9.60002 6.41421 9.60002H17.5858C18.4767 9.60002 18.9229 8.52288 18.2929 7.89291L12.7 2.3C12.3 1.9 11.7 1.9 11.3 2.3L5.7071 7.89291Z" fill="black" />
                                                        </svg>
                                                    </span>
                                                    <span class="text-success">@{{ getSalaryPercentage(employee, 'tunjangan').value }}%</span>
                                                </div> -->
                                            </div>
                                        </div>
                                    </td>
                                    <!--end::Kenaikan Tunjangan-->
                                    <!--begin::Kenaikan Persentase Tunjangan-->

                                    <!--end::Kenaikan Persentase Tunjangan-->
                                    <!--begin::Kenaikan Harian-->
                                    <td>
                                        <div class="row align-items-center">
                                            <div class="col-md-9">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" v-model="employee.uang_harian_new_value" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat">
                                                </div>
                                            </div>
                                            <div class="col-md-3" v-html="getSalaryPercentage(employee, 'uang_harian')">
                                                <!-- <div class="d-flex align-items-center">
                                                    <span class="svg-icon svg-icon-success">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.5" d="M13 9.59998V21C13 21.6 12.6 22 12 22C11.4 22 11 21.6 11 21V9.59998H13Z" fill="black" />
                                                            <path d="M5.7071 7.89291C5.07714 8.52288 5.52331 9.60002 6.41421 9.60002H17.5858C18.4767 9.60002 18.9229 8.52288 18.2929 7.89291L12.7 2.3C12.3 1.9 11.7 1.9 11.3 2.3L5.7071 7.89291Z" fill="black" />
                                                        </svg>
                                                    </span>
                                                    <span class="text-success">@{{ getSalaryPercentage(employee, 'uang_harian').value }}%</span>
                                                </div> -->
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" v-model="employee.uang_harian_new_coefficient" class="form-control text-end form-control-sm">
                                    </td>
                                    <!--end::Kenaikan Harian-->
                                    <!--begin::Kenaikan Persentase Harian-->

                                    <!--end::Kenaikan Persentase Harian-->
                                    <!--begin::Kenaikan Lembur-->
                                    <td>
                                        <div class="row align-items-center">
                                            <div class="col-md-9">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" v-model="employee.lembur_new_value" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat">
                                                </div>
                                            </div>
                                            <div class="col-md-3" v-html="getSalaryPercentage(employee, 'lembur')">
                                                <!-- <div class="d-flex align-items-center">
                                                    <span class="svg-icon svg-icon-success">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.5" d="M13 9.59998V21C13 21.6 12.6 22 12 22C11.4 22 11 21.6 11 21V9.59998H13Z" fill="black" />
                                                            <path d="M5.7071 7.89291C5.07714 8.52288 5.52331 9.60002 6.41421 9.60002H17.5858C18.4767 9.60002 18.9229 8.52288 18.2929 7.89291L12.7 2.3C12.3 1.9 11.7 1.9 11.3 2.3L5.7071 7.89291Z" fill="black" />
                                                        </svg>
                                                    </span>
                                                    <span class="text-success">@{{ getSalaryPercentage(employee, 'lembur').value }}%</span>
                                                </div> -->
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" v-model="employee.lembur_new_coefficient" class="form-control text-end form-control-sm">
                                    </td>
                                    <td class="pe-3 text-end">
                                        <div class="row align-items-center">
                                            <div class="col-md-9">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" :value="currencyFormat(getNewSalaryTotal(employee))" class="form-control text-end form-control-sm" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-3" v-html="getTotalSalaryPercentage(employee)">
                                                <!-- <div class="d-flex align-items-center">
                                                    <span class="svg-icon svg-icon-success">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.5" d="M13 9.59998V21C13 21.6 12.6 22 12 22C11.4 22 11 21.6 11 21V9.59998H13Z" fill="black" />
                                                            <path d="M5.7071 7.89291C5.07714 8.52288 5.52331 9.60002 6.41421 9.60002H17.5858C18.4767 9.60002 18.9229 8.52288 18.2929 7.89291L12.7 2.3C12.3 1.9 11.7 1.9 11.3 2.3L5.7071 7.89291Z" fill="black" />
                                                        </svg>
                                                    </span>
                                                    <span class="text-success">10%</span>
                                                </div> -->
                                            </div>
                                        </div>
                                        <!-- <span class="text-gray-800 fs-6 mb-1 fw-bold">@{{ currencyFormat(getCurrentSalaryTotal(employee)) }}</span> -->
                                    </td>
                                    <!--end::Kenaikan Lembur-->
                                    <!--begin::Kenaikan Persentase Lembur-->
                                    <!-- end::gaji ================================= -->
                                    <!-- end::Gaji Baru =================================-->
                                </tr>
                                <!--end::Table row-->
                            </tbody>
                            <!--end::Table body-->
                            <!-- begin:: Table footer -->
                            <tfoot>
                                <tr v-cloak class="bg-light-success">
                                    <td class="bg-white">Total</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-end fs-5 text-success fw-bold">@{{ currencyFormat(totalAll2.gaji_pokok_total) }}</td>
                                    <td class="text-end fs-5 text-success fw-bold">@{{ currencyFormat(totalAll2.tunjangan_total) }}</td>
                                    <td class="text-end fs-5 text-success fw-bold">@{{ currencyFormat(totalAll2.uang_harian_total) }}</td>
                                    <td></td>
                                    <td class="text-end fs-5 text-success fw-bold">@{{ currencyFormat(totalAll2.lembur_total) }}</td>
                                    <td class="text-end fs-5 text-success fw-bold"></td>
                                    <td class="text-end fs-5 text-success fw-bold">@{{ currencyFormat(grandTotal.currentSalary) }}</td>
                                    <td class="text-end fs-5 text-success fw-bold">@{{ currencyFormat(totalAll2.gaji_pokok_new_total) }}</td>
                                    <td class="text-end fs-5 text-success fw-bold">@{{ currencyFormat(totalAll2.tunjangan_new_total) }}</td>
                                    <td class="text-end fs-5 text-success fw-bold">@{{ currencyFormat(totalAll2.uang_harian_new_total) }}</td>
                                    <td></td>
                                    <td class="text-end fs-5 text-success fw-bold">@{{ currencyFormat(totalAll2.lembur_new_total) }}</td>
                                    <td class="text-end fs-5 text-success fw-bold"></td>
                                    <td class="text-end fs-5 text-success fw-bold">@{{ currencyFormat(grandTotal.newSalary) }}</td>
                                </tr>
                            </tfoot>
                            <!-- end:: Table footer -->
                        </table>
                        <!--end::Table-->
                    </div>
                </div>
            </div>
            <!--end::Card header-->
        </div>
        <!--end::Order details-->
        <div class="d-flex justify-content-end">
            <!--begin::Button-->
            <button type="button" class="btn btn-primary" :data-kt-indicator="submitLoading ? 'on' : null" @click="onSave" :disabled="disabledSaveButton">
                <span class="indicator-label">Simpan Perubahan</span>
                <span class="indicator-progress">Please wait...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
            <!--end::Button-->
        </div>
    </div>
    <!--end::Main column-->
    <!-- <div class="d-flex flex-column flex-lg-row">
    </div> -->
    <!--end::Row-->
    <!-- end::card -->
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.1.0/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
@endsection

@section('pagescript')
<script>
    moment.locale('id');
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


        datatable = $('#salary-increases-datatable').DataTable({
            "ordering": false,
            "drawCallback": function() {
                console.log('redraw table...')
            },
            "language": {
                "infoEmpty": " ",
                "zeroRecords": " "
            },
            fixedColumns: {
                left: 1,
            },
            // fixedHeader: {
            // header: true,
            // footer: true
            // }
        });

        const filterSearch = document.querySelector('[data-salary-increase-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });
    })
</script>
<script>
    const closeModal = (selector) => {
        const modalElement = document.querySelector(selector);
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();
    }

    const salaryComponents = <?php echo Illuminate\Support\Js::from($salary_components) ?>;
    const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;
    const companies = <?php echo Illuminate\Support\Js::from($companies) ?>;
    const divisions = <?php echo Illuminate\Support\Js::from($divisions) ?>;

    Vue.prototype.moment = moment;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                salaryComponents,
                employees,
                companies,
                divisions,
                model: {
                    componentId: '',
                    companyId: 25,
                    divisionId: 3,
                    effectiveDate: '',
                },
                selectedUnassignedEmployeesIds: [],
                submitLoading: false,
                cleave: {
                    thousandFormat: {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand',
                        numeralDecimalMark: ',',
                        delimiter: '.'
                    }
                }
            }
        },
        computed: {
            filteredDivisions() {
                const {
                    companyId
                } = this.model;
                const {
                    divisions
                } = this;
                if (companyId) {
                    return divisions.filter(division => division.company_id == companyId);
                }

                return [];
            },
            employeesByDivision() {
                const self = this;
                const {
                    divisionId
                } = self.model;
                const {
                    employees
                } = self;
                if (divisionId) {
                    return employees.filter(employee => employee?.office?.division?.id == divisionId);
                }

                return [];
            },
            totalAll2() {
                let totalAll = {};
                const employees = this.employeesByDivision;
                const newEmployees = employees.map(employee => {
                    const salaryComponentTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'lembur', 'gaji_pokok_new', 'tunjangan_new', 'uang_harian_new', 'lembur_new'];
                    let values = {};
                    salaryComponentTypes.forEach(salaryComponentType => {
                        try {
                            const componentValue = typeof employee[salaryComponentType + '_value'] !== 'string' ? employee[salaryComponentType + '_value'] : employee[salaryComponentType + '_value'].replaceAll('.', '').replaceAll(',', '.');

                            values = {
                                ...values,
                                [salaryComponentType + '_value']: Number(componentValue) || 0,
                            }
                        } catch (error) {
                            console.log(error)
                            values = {
                                ...values,
                                [salaryComponentType + '_value']: 0,
                            }
                        }
                    });

                    return values;
                });

                const salaryComponentTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'lembur', 'gaji_pokok_new', 'tunjangan_new', 'uang_harian_new', 'lembur_new'];

                salaryComponentTypes.forEach(salaryComponentType => {
                    const total = newEmployees.map(employee => employee[salaryComponentType + '_value']).reduce((acc, cur) => acc + cur, 0);

                    totalAll = {
                        ...totalAll,
                        [salaryComponentType + '_total']: total || 0,
                    }
                });

                return totalAll;
            },
            grandTotal() {
                const salaryComponentTypes = ['gaji_pokok', 'tunjangan', 'uang_harian'];
                let currentSalaryGrandTotal = 0;
                salaryComponentTypes.forEach(salaryComponentType => {
                    currentSalaryGrandTotal += (this.totalAll2[salaryComponentType + '_total'] || 0);
                });

                const salaryComponentTypesNew = ['gaji_pokok_new', 'tunjangan_new', 'uang_harian_new'];
                let newSalaryGrandTotal = 0;
                salaryComponentTypesNew.forEach(salaryComponentType => {
                    newSalaryGrandTotal += (this.totalAll2[salaryComponentType + '_total'] || 0);
                });

                return {
                    currentSalary: currentSalaryGrandTotal,
                    newSalary: newSalaryGrandTotal,
                }
            },
            // cleanEmployees2() {
            //     const self = this;
            //     const {
            //         employees
            //     } = this;
            //     if (Array.isArray(employees)) {
            //         const newEmployees = employees.map(employee => {

            //             const salaryComponentTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'lembur'];
            //             let values = {};
            //             salaryComponentTypes.forEach(salaryComponentType => {
            //                 try {
            //                     const componentValue = typeof employee[salaryComponentType + '_value'] !== 'string' ? employee[salaryComponentType + '_value'] : employee[salaryComponentType + '_value'].replaceAll('.', '').replaceAll(',', '.');

            //                     values = {
            //                         ...values,
            //                         [salaryComponentType + '_value']: Number(componentValue) || 0,
            //                     }
            //                 } catch (error) {
            //                     console.log(error)
            //                     values = {
            //                         ...values,
            //                         [salaryComponentType + '_value']: 0,
            //                     }
            //                 }
            //             });

            //             // return values;

            //             return {
            //                 ...values,
            //                 ...employee
            //             };

            //         })

            //         return newEmployees;
            //     }

            //     return [];
            // },
            disabledSaveButton() {
                return this.submitLoading;
            }
        },
        methods: {
            // JOB TITLE METHODS
            async onSave() {
                let self = this;
                try {
                    self.submitLoading = true;
                    // const employees = self.clearCurrencyFormatAll(self.employees);
                    const employees = self.employees;
                    const effectiveDate = self.model.effectiveDate;

                    // console.log(employees);
                    // return;

                    const response = await axios.post('/salary-increases', {
                        employees,
                        effective_date: effectiveDate,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.submitLoading = false;
                }
            },
            currencyFormat(number) {
                if (number && !isNaN(number)) {
                    return new Intl.NumberFormat('De-de').format(number);
                }

                return 0;
            },
            clearCurrencyFormatAll(employees = []) {
                const self = this;
                // const {
                //     employees
                // } = this;
                // return [];
                if (Array.isArray(employees)) {
                    const newEmployees = employees.map(employee => {
                        const salaryComponentTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'lembur'];
                        let values = {};
                        salaryComponentTypes.forEach(salaryComponentType => {
                            try {
                                const componentValue = typeof employee[salaryComponentType + '_value'] !== 'string' ? employee[salaryComponentType + '_value'] : employee[salaryComponentType + '_value'].replaceAll('.', '').replaceAll(',', '.');

                                values = {
                                    ...values,
                                    [salaryComponentType + '_value']: Number(componentValue) || 0,
                                }
                            } catch (error) {
                                console.log(error)
                                values = {
                                    ...values,
                                    [salaryComponentType + '_value']: 0,
                                }
                            }
                        });

                        return {
                            ...values,
                            ...employee
                        };

                        // return 10;

                        // return 10;

                    })

                    return newEmployees;
                }

                return [];
            },
            getCurrentSalaryTotal(employee = null) {
                if (employee) {
                    const salaryComponentTypes = ['gaji_pokok', 'tunjangan', 'uang_harian'];
                    let salaryTotal = 0;

                    salaryComponentTypes.forEach(salaryComponentType => {
                        const amount = typeof employee[salaryComponentType + '_value'] !== 'string' ? employee[salaryComponentType + '_value'] : employee[salaryComponentType + '_value'].replaceAll('.', '');
                        salaryTotal += Number(amount);
                    });

                    return salaryTotal;
                }

                return 0;
            },
            getNewSalaryTotal(employee = null) {
                if (employee) {
                    const salaryComponentTypes = ['gaji_pokok_new', 'tunjangan_new', 'uang_harian_new'];
                    let salaryTotal = 0;

                    salaryComponentTypes.forEach(salaryComponentType => {
                        const amount = typeof employee[salaryComponentType + '_value'] !== 'string' ? employee[salaryComponentType + '_value'] : employee[salaryComponentType + '_value'].replaceAll('.', '');
                        salaryTotal += Number(amount);
                    });

                    return salaryTotal;
                }

                return 0;
            },
            getSalaryPercentage(employee = null, salaryType = null) {
                if (employee && salaryType) {
                    const salaryAmount = typeof employee[salaryType + '_value'] !== 'string' ? employee[salaryType + '_value'] : employee[salaryType + '_value'].replaceAll('.', '');
                    const salaryNewAmount = typeof employee[salaryType + '_new_value'] !== 'string' ? employee[salaryType + '_new_value'] : employee[salaryType + '_new_value'].replaceAll('.', '');

                    const diffAmount = Number(salaryNewAmount) - Number(salaryAmount);

                    let percentage = (Number(diffAmount) / Number(salaryAmount)) * 100;
                    if (!Number.isFinite(percentage)) {
                        percentage = Number(salaryNewAmount);
                    }

                    // return {
                    //     value: percentage.toFixed(1),
                    //     indicator: function() {
                    //         if (percentage < 0) {
                    //             return {
                    //                 type: 'down',
                    //                 color: 'danger'
                    //             };
                    //         } else if (percentage == 0) {
                    //             return {
                    //                 type: 'stay',
                    //                 color: 'secondary'
                    //             };
                    //         } else if (percentage > 0) {
                    //             return {
                    //                 type: 'up',
                    //                 color: 'success'
                    //             };
                    //         }

                    //         return {
                    //             type: 'stay',
                    //             color: 'secondary'
                    //         };
                    //     }(),
                    // };
                    if (percentage < 0) {
                        return ` <div class="d-flex align-items-center">
                                <span class="svg-icon svg-icon-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="black"/>
                                        <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="black"/>
                                    </svg>
                                </span>
                                <span class="text-danger">${ percentage.toFixed(1) }%</span>
                            </div>`
                    } else if (percentage == 0) {
                        return ` <div class="d-flex align-items-center">
                                
                                <span class="text-dark">~0%</span>
                            </div>`
                    } else if (percentage > 0) {
                        return ` <div class="d-flex align-items-center">
                                <span class="svg-icon svg-icon-success"> 
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black"/>
                                        <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="black"/>
                                    </svg>
                                </span>
                                <span class="text-success">${ percentage.toFixed(1) }%</span>
                            </div>`
                    }


                }
                return '??';
            },
            getTotalSalaryPercentage(employee = null) {
                if (employee) {
                    const newSalaryTotal = this.getNewSalaryTotal(employee);
                    const currentSalaryTotal = this.getCurrentSalaryTotal(employee);

                    const diffAmount = Number(newSalaryTotal) - Number(currentSalaryTotal);

                    let percentage = (Number(diffAmount) / Number(currentSalaryTotal)) * 100;
                    if (!Number.isFinite(percentage)) {
                        percentage = Number(currentSalaryTotal);
                    }

                    if (percentage < 0) {
                        return ` <div class="d-flex align-items-center">
                                <span class="svg-icon svg-icon-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="11" y="18" width="13" height="2" rx="1" transform="rotate(-90 11 18)" fill="black"/>
                                        <path d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z" fill="black"/>
                                    </svg>
                                </span>
                                <span class="text-danger">${ percentage.toFixed(1) }%</span>
                            </div>`
                    } else if (percentage == 0) {
                        return ` <div class="d-flex align-items-center">
                                
                                <span class="text-dark">~0%</span>
                            </div>`
                    } else if (percentage > 0) {
                        return ` <div class="d-flex align-items-center">
                                <span class="svg-icon svg-icon-success"> 
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black"/>
                                        <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="black"/>
                                    </svg>
                                </span>
                                <span class="text-success">${ percentage.toFixed(1) }%</span>
                            </div>`
                    }
                }
                return '??';
            },
            clearCurrencyFormat(currency) {
                if (currency) {
                    let newCurrency = '';
                    newCurrency = currency.replaceAll('.', '');
                    newCurrency = currency.replaceAll(',', '.');
                    return Number(currency);
                }

                return undefined;
            }
        },
        directives: {
            cleave: {
                inserted: (el, binding) => {
                    el.cleave = new Cleave(el, binding.value || {})
                },
                update: (el) => {
                    const event = new Event('input', {
                        bubbles: true
                    });
                    setTimeout(function() {
                        el.value = el.cleave.properties.result
                        el.dispatchEvent(event)
                    }, 100);
                }
            }
        }
    })

    // var myModalEl = document.getElementById('modal_add_employee')
    // myModalEl.addEventListener('hidden.bs.modal', function(event) {
    //     app.$data.selectedUnassignedEmployeesIds = [];
    // })
</script>
@endsection