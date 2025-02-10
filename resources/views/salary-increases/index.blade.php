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
                                <label class="form-label">Periode</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="month" class="form-control form-control-sm">
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
                    <!--end::Search products-->
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table align-middle table-striped table-row-bordered fs-6 gy-5" id="salary-increases-datatable">
                            <!--begin::Table head-->
                            <thead>
                                <!--begin::Table row-->
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-250px text-center align-middle" rowspan="2">Nama</th>
                                    <th class="min-w-150px text-center align-middle" rowspan="2">Job Title</th>
                                    <th class="text-center" colspan="4">Saat Ini</th>
                                    <th class="text-center" colspan="8">Kenaikan</th>
                                    <th class="text-center" colspan="4">Baru</th>
                                </tr>
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <!-- Saat ini -->
                                    <th class="text-end min-w-150px">Gaji Pokok</th>
                                    <th class="text-end min-w-150px">Tunjangan</th>
                                    <th class="text-end min-w-150px">Harian</th>
                                    <th class="text-end min-w-150px">Lembur</th>
                                    <!-- <th class="text-end min-w-150px">Bonus</th> -->
                                    <!-- Kenikan -->
                                    <th class="text-end min-w-200px">Gaji Pokok</th>
                                    <th class="text-end">%</th>
                                    <th class="text-end min-w-200px">Tunjangan</th>
                                    <th class="text-end">%</th>
                                    <th class="text-end min-w-200px">Harian</th>
                                    <th class="text-end">%</th>
                                    <th class="text-end min-w-200px">Lembur</th>
                                    <th class="text-end">%</th>
                                    <!-- <th class="text-end min-w-200px">Bonus</th> -->
                                    <!-- <th class="text-end">%</th> -->
                                    <!-- Baru -->
                                    <th class="text-end min-w-150px">Gaji Pokok</th>
                                    <th class="text-end min-w-150px">Tunjangan</th>
                                    <th class="text-end min-w-150px">Harian</th>
                                    <th class="text-end min-w-150px">Lembur</th>
                                    <!-- <th class="text-end min-w-150px">Bonus</th> -->
                                </tr>
                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="text-gray-600">
                                <!--begin::Table row-->
                                <tr v-cloak v-if="employeesByDivision.length < 1">
                                    <td colspan="10">
                                        <div v-cloak class="text-center">
                                            <em>Tidak ada data</em>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-cloak v-else v-for="(employee, index) in employeesByDivision" :key="employee.id">
                                    <!--begin::Category=-->
                                    <td>
                                        <div v-cloak>
                                            <div class="text-gray-800 text-hover-primary fs-5 fw-bolder mb-1">@{{ employee.name }}</div>
                                            <span class="text-muted">@{{ employee.number }}</span>
                                        </div>
                                    </td>
                                    <!--end::Category=-->
                                    <!--begin::Job Title-->
                                    <td>
                                        <span class="text-gray-800 fs-6 mb-1">@{{ employee?.active_career?.job_title?.name }}</span>
                                    </td>
                                    <!--end::Job Title-->
                                    <!-- begin::Gaji Sekarang ================================= -->
                                    <!--begin::Gaji Pokok-->
                                    <td>
                                        <div v-if="!haveSalaryComponent(employee, 'gaji_pokok')" class="text-center">
                                            <small class="text-muted"><em>Tidak memiliki gaji pokok</em></small>
                                        </div>
                                        <div v-else class="text-end">
                                            <span class="text-gray-800 fs-6 mb-1">@{{ currencyFormat(getSalaryComponent(employee, 'gaji_pokok')) }}</span>
                                        </div>
                                    </td>
                                    <!--end::Gaji Pokok-->
                                    <!--begin::Tunjangan-->
                                    <td class="text-end">
                                        <div v-if="!haveSalaryComponent(employee, 'tunjangan')" class="text-center">
                                            <small class="text-muted"><em>Tidak memiliki tunjangan</em></small>
                                        </div>
                                        <div v-else class="text-end">
                                            <span class="text-gray-800 fs-6 mb-1">@{{ currencyFormat(getSalaryComponent(employee, 'tunjangan')) }}</span>
                                        </div>
                                    </td>
                                    <!--end::Tunjangan-->
                                    <!--begin::Harian-->
                                    <td class="text-end">
                                        <div v-if="!haveSalaryComponent(employee, 'uang_harian')" class="text-center">
                                            <small class="text-muted"><em>Tidak memiliki uang harian</em></small>
                                        </div>
                                        <div v-else class="text-end">
                                            <span class="text-gray-800 fs-6 mb-1">@{{ currencyFormat(getSalaryComponent(employee, 'uang_harian')) }}</span>
                                        </div>
                                    </td>
                                    <!--end::Harian-->
                                    <!--begin::Lembur-->
                                    <td class="text-end">
                                        <div v-if="!haveSalaryComponent(employee, 'lembur')" class="text-center">
                                            <small class="text-muted"><em>Tidak memiliki lembur</em></small>
                                        </div>
                                        <div v-else class="text-end">
                                            <span class="text-gray-800 fs-6 mb-1">@{{ currencyFormat(getSalaryComponent(employee, 'lembur')) }}</span>
                                        </div>
                                    </td>
                                    <!--end::Lembur-->
                                    <!--begin::Bonus-->
                                    <!-- <td class="text-end">
                                        <div v-if="!haveSalaryComponent(employee, 'bonus')" class="text-center">
                                            <small class="text-muted"><em>Tidak memiliki bonus</em></small>
                                        </div>
                                        <div v-else class="text-end">
                                            <span class="text-gray-800 fs-6 mb-1">@{{ currencyFormat(getSalaryComponent(employee, 'bonus')) }}</span>
                                        </div>
                                    </td> -->
                                    <!--end::Bonus-->
                                    <!-- end::Gaji Sekarang ================================= -->
                                    <!-- begin::Kenaikan Gaji ================================= -->
                                    <!--begin::Kenaikan Gaji Pokok-->
                                    <td>
                                        <div v-if="haveSalaryComponent(employee, 'gaji_pokok')">
                                            <input v-model="employee.basic_salary_increase" type="text" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat">
                                        </div>
                                    </td>
                                    <!--end::Kenaikan Gaji Pokok-->
                                    <!--begin::Kenaikan Persentase Gaji Pokok-->
                                    <td class="text-end">
                                        <span class="text-success fs-6 mb-1">
                                            @{{ getIncreasePercentage(employee, 'gaji_pokok', 'basic_salary_increase') }}
                                        </span>
                                    </td>
                                    <!--end::Kenaikan Persentase Gaji Pokok-->
                                    <!--begin::Kenaikan Tunjangan-->
                                    <td>
                                        <div v-if="haveSalaryComponent(employee, 'tunjangan')">
                                            <input v-model="employee.allowance_increase" type="text" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat">
                                        </div>
                                    </td>
                                    <!--end::Kenaikan Tunjangan-->
                                    <!--begin::Kenaikan Persentase Tunjangan-->
                                    <td class="text-end">
                                        <span class="text-success fs-6 mb-1">
                                            @{{ getIncreasePercentage(employee, 'tunjangan', 'allowance_increase') }}
                                        </span>
                                    </td>
                                    <!--end::Kenaikan Persentase Tunjangan-->
                                    <!--begin::Kenaikan Harian-->
                                    <td>
                                        <div v-if="haveSalaryComponent(employee, 'uang_harian')">
                                            <input v-model="employee.daily_money_increase" type="text" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat">
                                        </div>
                                    </td>
                                    <!--end::Kenaikan Harian-->
                                    <!--begin::Kenaikan Persentase Harian-->
                                    <td class="text-end">
                                        <span class="text-success fs-6 mb-1">
                                            @{{ getIncreasePercentage(employee, 'uang_harian', 'daily_money_increase') }}
                                        </span>
                                    </td>
                                    <!--end::Kenaikan Persentase Harian-->
                                    <!--begin::Kenaikan Lembur-->
                                    <td>
                                        <div v-if="haveSalaryComponent(employee, 'lembur')">
                                            <input v-model="employee.overtime_increase" type="text" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat">
                                        </div>
                                    </td>
                                    <!--end::Kenaikan Lembur-->
                                    <!--begin::Kenaikan Persentase Lembur-->
                                    <td class="text-end">
                                        <span class="text-success fs-6 mb-1">
                                            @{{ getIncreasePercentage(employee, 'lembur', 'overtime_increase') }}
                                        </span>
                                    </td>
                                    <!--end::Kenaikan Persentase Lembur-->
                                    <!--begin::Kenaikan Bonus-->
                                    <!-- <td>
                                        <div v-if="haveSalaryComponent(employee, 'bonus')">
                                            <input v-model="employee.bonus_increase" type="text" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat">
                                        </div>
                                    </td> -->
                                    <!--end::Kenaikan Bonus-->
                                    <!--begin::Kenaikan Persentase Bonus-->
                                    <!-- <td class="text-end">
                                        <span class="text-success fs-6 mb-1">
                                            @{{ getIncreasePercentage(employee, 'bonus', 'bonus_increase') }}
                                        </span>
                                    </td> -->
                                    <!--end::Kenaikan Persentase Bonus-->
                                    <!-- end::Kenaikan Gaji ================================= -->
                                    <!-- begin::Gaji Baru =================================-->
                                    <!--begin::New Gaji Pokok-->
                                    <td class="text-end">
                                        <span class="text-gray-800 fs-6 mb-1">
                                            @{{ currencyFormat(getNewSalaryAmount(employee, 'gaji_pokok', 'basic_salary_increase')) }}
                                        </span>
                                    </td>
                                    <!--end::New Gaji Pokok-->
                                    <!--begin::New Tunjangan-->
                                    <td class="text-end">
                                        <span class="text-gray-800 fs-6 mb-1">
                                            @{{ currencyFormat(getNewSalaryAmount(employee, 'tunjangan', 'allowance_increase')) }}
                                        </span>
                                    </td>
                                    <!--end::New Tunjangan-->
                                    <!--begin::New Harian-->
                                    <td class="text-end">
                                        <span class="text-gray-800 fs-6 mb-1">
                                            @{{ currencyFormat(getNewSalaryAmount(employee, 'uang_harian', 'daily_money_increase')) }}
                                        </span>
                                    </td>
                                    <!--end::New Harian-->
                                    <!--begin::New Lembur-->
                                    <td class="text-end">
                                        <span class="text-gray-800 fs-6 mb-1">
                                            @{{ currencyFormat(getNewSalaryAmount(employee, 'lembur', 'overtime_increase')) }}
                                        </span>
                                    </td>
                                    <!--end::New Lembur-->
                                    <!--begin::New Bonus-->
                                    <!-- <td class="text-end">
                                        <span class="text-gray-800 fs-6 mb-1">
                                            @{{ currencyFormat(getNewSalaryAmount(employee, 'bonus', 'bonus_increase')) }}
                                        </span>
                                    </td> -->
                                    <!--end::New Bonus-->
                                    <!-- end::Gaji Baru =================================-->
                                </tr>
                                <!--end::Table row-->
                            </tbody>
                            <!--end::Table body-->
                            <!-- begin:: Table footer -->
                            <tfoot>
                                <tr class="bg-light-primary">
                                    <td>Total</td>
                                    <td></td>
                                    <td class="text-end fs-6 fw-bold">@{{ currencyFormat(totalAll.totalBasicSalary) }}</td>
                                    <td class="text-end fs-6 fw-bold">@{{ currencyFormat(totalAll.totalAllowance) }}</td>
                                    <td class="text-end fs-6 fw-bold">@{{ currencyFormat(totalAll.totalDailyMoney) }}</td>
                                    <td class="text-end fs-6 fw-bold">@{{ currencyFormat(totalAll.totalOvertime) }}</td>
                                    <!-- <td class="text-end fs-6 fw-bold">@{{ currencyFormat(totalAll.totalBonus) }}</td> -->
                                    <td class="text-end fs-6 fw-bold">@{{ currencyFormat(totalAll.totalBasicSalaryAddition) }}</td>
                                    <td></td>
                                    <td class="text-end fs-6 fw-bold">@{{ currencyFormat(totalAll.totalAllowanceAddition) }}</td>
                                    <td></td>
                                    <td class="text-end fs-6 fw-bold">@{{ currencyFormat(totalAll.totalDailyMoneyAddition) }}</td>
                                    <td></td>
                                    <td class="text-end fs-6 fw-bold">@{{ currencyFormat(totalAll.totalOvertimeAddition) }}</td>
                                    <td></td>
                                    <!-- <td class="text-end fs-6 fw-bold">@{{ currencyFormat(totalAll.totalBonusAddition) }}</td>
                                    <td></td> -->
                                    <td colspan="5"></td>
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
            }
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
            totalAll() {
                const employees = this.employeesByDivision;
                const newEmployees = employees.map(employee => {
                    let basicSalaryIncrease = employee.basic_salary_increase;
                    let allowanceIncrease = employee.allowance_increase;
                    let dailyMoneyIncrease = employee.daily_money_increase;
                    let overtimeIncrease = employee.overtime_increase;
                    let bonusIncrease = employee.bonus_increase;

                    const newSalaryComponents = employee.salary_components.map(component => {
                        if (component.salary_type == 'gaji_pokok') {
                            if (basicSalaryIncrease) {
                                basicSalaryIncrease = basicSalaryIncrease.replaceAll('.', '').replaceAll(',', '.');
                                component.addition = Number(basicSalaryIncrease);
                            }
                        }
                        if (component.salary_type == 'tunjangan') {
                            if (allowanceIncrease) {
                                allowanceIncrease = allowanceIncrease.replaceAll('.', '').replaceAll(',', '.');
                                component.addition = Number(allowanceIncrease);
                            }
                        }
                        if (component.salary_type == 'uang_harian') {
                            if (dailyMoneyIncrease) {
                                dailyMoneyIncrease = dailyMoneyIncrease.replaceAll('.', '').replaceAll(',', '.');
                                component.addition = Number(dailyMoneyIncrease);
                            }
                        }
                        if (component.salary_type == 'lembur') {
                            if (overtimeIncrease) {
                                overtimeIncrease = overtimeIncrease.replaceAll('.', '').replaceAll(',', '.');
                                component.addition = Number(overtimeIncrease);
                            }
                        }
                        if (component.salary_type == 'bonus') {
                            if (bonusIncrease) {
                                bonusIncrease = bonusIncrease.replaceAll('.', '').replaceAll(',', '.');
                                component.addition = Number(bonusIncrease);
                            }
                        }

                        return component;
                    })

                    let basicSalary = 0;
                    let basicSalaryAddition = 0;
                    let allowance = 0;
                    let allowanceAddition = 0;
                    let dailyMoney = 0;
                    let dailyMoneyAddition = 0;
                    let overtime = 0;
                    let overtimeAddition = 0;
                    let bonus = 0;
                    let bonusAddition = 0;
                    const [basicSalaryComponent] = newSalaryComponents.filter(component => component.salary_type == 'gaji_pokok');
                    const [allowanceComponent] = newSalaryComponents.filter(component => component.salary_type == 'tunjangan');
                    const [dailyMoneyComponent] = newSalaryComponents.filter(component => component.salary_type == 'uang_harian');
                    const [overtimeComponent] = newSalaryComponents.filter(component => component.salary_type == 'lembur');
                    const [bonusComponent] = newSalaryComponents.filter(component => component.salary_type == 'bonus');

                    if (basicSalaryComponent) {
                        basicSalary = basicSalaryComponent.pivot.amount || 0;
                        basicSalaryAddition = basicSalaryComponent.addition || 0;

                    }
                    if (allowanceComponent) {
                        allowance = allowanceComponent.pivot.amount || 0;
                        allowanceAddition = allowanceComponent.addition || 0;
                    }
                    if (dailyMoneyComponent) {
                        dailyMoney = dailyMoneyComponent.pivot.amount || 0;
                        dailyMoneyAddition = dailyMoneyComponent.addition || 0;
                    }
                    if (overtimeComponent) {
                        overtime = overtimeComponent.pivot.amount || 0;
                        overtimeAddition = overtimeComponent.addition || 0;
                    }
                    if (bonusComponent) {
                        bonusComponent = bonusComponent.pivot.amount || 0;
                        bonusAddition = bonusComponent.addition || 0;
                    }

                    return {
                        basic_salary: basicSalary,
                        basic_salary_addition: basicSalaryAddition,
                        allowance,
                        allowance_addition: allowanceAddition,
                        daily_money: dailyMoney,
                        daily_money_addition: dailyMoneyAddition,
                        overtime,
                        overtime_addition: overtimeAddition,
                        bonus,
                        bonus_addition: bonusAddition,
                    };

                })

                const totalBasicSalary = newEmployees.map(employee => Number(employee.basic_salary)).reduce((acc, cur) => acc + cur, 0);

                const totalBasicSalaryAddition = newEmployees.map(employee => Number(employee.basic_salary_addition)).reduce((acc, cur) => acc + cur, 0);

                const totalAllowance = newEmployees.map(employee => Number(employee.allowance)).reduce((acc, cur) => acc + cur, 0);

                const totalAllowanceAddition = newEmployees.map(employee => Number(employee.allowance_addition)).reduce((acc, cur) => acc + cur, 0);

                const totalDailyMoney = newEmployees.map(employee => Number(employee.daily_money)).reduce((acc, cur) => acc + cur, 0);

                const totalDailyMoneyAddition = newEmployees.map(employee => Number(employee.daily_money_addition)).reduce((acc, cur) => acc + cur, 0);

                const totalOvertime = newEmployees.map(employee => Number(employee.overtime)).reduce((acc, cur) => acc + cur, 0);

                const totalOvertimeAddition = newEmployees.map(employee => Number(employee.overtime_addition)).reduce((acc, cur) => acc + cur, 0);

                const totalBonus = newEmployees.map(employee => Number(employee.bonus)).reduce((acc, cur) => acc + cur, 0);

                const totalBonusAddition = newEmployees.map(employee => Number(employee.bonus_addition)).reduce((acc, cur) => acc + cur, 0);

                const totalFinalBasicSalary = totalBasicSalary + totalBasicSalaryAddition;
                const totalFinalAllowance = totalAllowance + totalAllowanceAddition;
                const totalFinalDailyMoney = totalDailyMoney + totalDailyMoneyAddition;
                const totalFinalOvertime = totalOvertime + totalOvertimeAddition;
                const totalFinalBonus = totalBonus + totalBonusAddition;

                return {
                    totalBasicSalary,
                    totalBasicSalaryAddition,
                    totalFinalBasicSalary,
                    totalAllowance,
                    totalAllowanceAddition,
                    totalFinalAllowance,
                    totalDailyMoney,
                    totalDailyMoneyAddition,
                    totalFinalDailyMoney,
                    totalOvertime,
                    totalOvertimeAddition,
                    totalFinalOvertime,
                    totalBonus,
                    totalBonusAddition,
                    totalFinalBonus,
                }
            },
            cleanEmployees() {
                const self = this;
                const {
                    employees
                } = this;
                if (Array.isArray(employees)) {
                    const newEmployees = employees.map(employee => {
                        let basicSalaryIncrease = employee.basic_salary_increase;
                        let allowanceIncrease = employee.allowance_increase;

                        const newSalaryComponents = employee.salary_components.map(component => {
                            if (component.salary_type == 'gaji_pokok') {
                                if (basicSalaryIncrease) {
                                    basicSalaryIncrease = basicSalaryIncrease.replaceAll('.', '').replaceAll(',', '.');
                                    component.addition = Number(basicSalaryIncrease);
                                }
                            }
                            if (component.salary_type == 'tunjangan') {
                                if (allowanceIncrease) {
                                    allowanceIncrease = allowanceIncrease.replaceAll('.', '').replaceAll(',', '.');
                                    component.addition = Number(allowanceIncrease);
                                }
                            }

                            return component;
                        })

                        return {
                            salary_components: newSalaryComponents,
                            ...employee
                        };

                    })

                    return newEmployees;
                }

                return [];
            },
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
                    const employees = self.cleanEmployees;

                    const response = await axios.post('/salary-increases/action/increase', {
                        employees,
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

                return null;
            },
            getSalaryComponent(employee, salaryType) {
                const salaryComponents = employee.salary_components;
                if (salaryComponents && Array.isArray(salaryComponents)) {
                    const [component] = salaryComponents.filter(component => component.salary_type == salaryType);
                    if (component) {
                        const amount = component?.pivot?.amount || 0;
                        return amount
                    }
                    // return '-';
                    return `Tidak memiliki ${this.getSalaryName(salaryType)}`;
                }

                return 0;
            },
            getSalaryName(salaryType) {
                switch (salaryType) {
                    case 'gaji_pokok':
                        return 'Gaji Pokok';
                    case 'tunjangan':
                        return 'Tunjangan';
                    default:
                        return null;
                }
            },
            getIncreasePercentage(employee, salaryType, increaseProperty) {
                const self = this;
                const currentAmount = self.getSalaryComponent(employee, salaryType);
                let salaryIncrease = employee[increaseProperty];
                if (salaryIncrease) {
                    salaryIncrease = salaryIncrease.replaceAll('.', '');
                    salaryIncrease = salaryIncrease.replaceAll(',', '.');
                    const percentage = (Number(salaryIncrease) / currentAmount) * 100;
                    // return Math.round(percentage);
                    return percentage.toFixed(2);
                }

                return null;
            },
            getNewSalaryAmount(employee, salaryType, increaseProperty) {
                const self = this;
                const currentAmount = self.getSalaryComponent(employee, salaryType);
                let salaryIncrease = employee[increaseProperty];
                if (salaryIncrease) {
                    salaryIncrease = salaryIncrease.replaceAll('.', '');
                    salaryIncrease = salaryIncrease.replaceAll(',', '.');
                    const newAmount = Number(currentAmount) + Number(salaryIncrease);
                    return newAmount;
                }

                return Number(currentAmount);
            },
            haveSalaryComponent(employee, salaryType) {
                const salaryComponents = employee.salary_components;
                const [component] = salaryComponents.filter(component => component.salary_type == salaryType);
                if (component) {
                    return true;
                }

                return false;
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