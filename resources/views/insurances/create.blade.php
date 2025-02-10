@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@inject('carbon', 'Carbon\Carbon')

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!--begin::Row-->
    <!--begin::Aside column-->
    <div class="w-100 mb-7 me-7 me-lg-10">
        <!--begin::Order details-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Filter</h2>
                </div>
                <div class="card-toolbar">

                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <!--begin::Input group-->
                <div class="d-flex flex-column flex-md-row gap-5">
                    <div class="fv-row flex-row-fluid fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label">Tahun</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select v-model="model.year" class="form-select">
                            @if(isset($min_year))
                            @for($year = $min_year; $year <= date("Y"); $year++) <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                                @else
                                <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                @endif
                        </select>
                        <!--end::Input-->
                    </div>
                    <div class="fv-row flex-row-fluid fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label">Perusahaan</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select v-model="model.companyId" class="form-select">
                            <option value="">Pilih Perusahaan</option>
                            <option v-for="(company, index) in companies" :key="company.id" :value="company.id">@{{ company.name }}</option>
                        </select>
                        <!--end::Input-->
                    </div>
                    <div class="flex-row-fluid">
                        <!--begin::Label-->
                        <label class="form-label">Divisi</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select v-model="model.divisionId" class="form-select">
                            <option value="">Pilih Divisi</option>
                            <option v-for="(division, index) in filteredDivisions" :key="division.id" :value="division.id">@{{ division.name }}</option>
                        </select>
                        <!--end::Input-->
                    </div>
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Card header-->
        </div>
        <!--end::Order details-->
    </div>
    <!--end::Aside column-->
    <!--begin::Main column-->
    <div class="d-flex flex-column flex-lg-row-fluid gap-7 gap-lg-10">
        <!--begin::Order details-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Asuransi</h2>
                </div>
                <div class="card-toolbar">
                    <button v-cloak v-if="model.componentId" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_add_employee">Tambah</button>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-10">
                    <div class="d-flex flex-wrap fw-bold mb-4 fs-5 text-gray-400">Masukkan nilai kenaikan gaji pegawai untuk gaji pokok dan tunjangan</div>
                    <!--begin::Input group-->
                    <div>

                        <!--begin::Selected products-->
                        <!-- <div class="row row-cols-1 row-cols-xl-3 row-cols-md-2 border border-dashed rounded pt-3 pb-1 px-2 mb-5 mh-300px overflow-scroll" id="kt_ecommerce_edit_order_selected_products">
                            
                            <span class="w-100 text-muted">Pilih satu atau beberapa pegawai yang memiliki komponen gaji ini</span>
                           
                        </div> -->
                        <!--begin::Selected products-->
                        <!--begin::Total price-->

                        <div class="row g-6 g-xl-9">
                            <div class="col-lg-4">
                                <div class="border border-gray-300 border-dashed rounded py-3 px-4">
                                    <!--begin::Number-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                                        <span class="svg-icon svg-icon-3 svg-icon-primary me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M6.28548 15.0861C7.34369 13.1814 9.35142 12 11.5304 12H12.4696C14.6486 12 16.6563 13.1814 17.7145 15.0861L19.3493 18.0287C20.0899 19.3618 19.1259 21 17.601 21H6.39903C4.87406 21 3.91012 19.3618 4.65071 18.0287L6.28548 15.0861Z" fill="black" />
                                                <rect opacity="0.3" x="8" y="3" width="8" height="8" rx="4" fill="black" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                        <div class="fs-4 fw-bolder counted" data-kt-countup="true" data-kt-countup-value="75">75</div>
                                    </div>
                                    <!--end::Number-->
                                    <!--begin::Label-->
                                    <div class="fw-bold fs-6 text-gray-400">Pegawai</div>
                                    <!--end::Label-->
                                </div>
                                <!--begin::Stat-->
                            </div>
                            <div class="col-lg-4">
                                <div class="border border-gray-300 border-dashed rounded py-3 px-4">
                                    <!--begin::Number-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                        <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black"></rect>
                                                <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="black"></path>
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                        <div class="fs-4 fw-bolder">Rp 15.000</div>
                                    </div>
                                    <!--end::Number-->
                                    <!--begin::Label-->
                                    <div class="fw-bold fs-6 text-gray-400">Total Kenaikan Gaji Pokok</div>
                                    <!--end::Label-->
                                </div>
                                <!--end::Stat-->
                            </div>
                            <div class="col-lg-4">
                                <div class="border border-gray-300 border-dashed rounded py-3 px-4">
                                    <!--begin::Number-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                        <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black"></rect>
                                                <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="black"></path>
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                        <div class="fs-4 fw-bolder">Rp 15.000</div>
                                    </div>
                                    <!--end::Number-->
                                    <!--begin::Label-->
                                    <div class="fw-bold fs-6 text-gray-400">Total Kenaikan Tunjangan</div>
                                    <!--end::Label-->
                                </div>
                                <!--end::Stat-->
                            </div>
                        </div>

                        <!--end::Total price-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Separator-->
                    <div class="separator"></div>
                    <!--end::Separator-->
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
                        <input type="text" data-insurance-employee-filter="search" class="form-control form-control-solid w-100 w-lg-50 ps-14" placeholder="Cari Pegawai" />
                    </div>
                    <!--end::Search products-->
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table align-middle table-striped table-row-bordered fs-6 gy-5" id="insurances-datatable">
                            <!--begin::Table head-->
                            <thead>
                                <!--begin::Table row-->
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-250px text-center align-middle" rowspan="2">Nama</th>
                                    <th class="min-w-150px text-center align-middle" rowspan="2">Job Title</th>
                                    <th class="text-center" colspan="2">Tahun Lalu <span v-cloak>(@{{ lastYear }})</span></th>
                                    <th class="text-center" colspan="2">Tahun Berjalan <span v-cloak>(@{{ model.year }})</span></th>
                                    <th class="min-w-200px text-center align-middle" rowspan="2">Total Pensiun s/d Tahun Berjalan</th>
                                </tr>
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-200px text-center">Kesehatan</th>
                                    <th class="min-w-200px text-center">Pensiun</th>
                                    <th class="min-w-200px text-center">Kesehatan</th>
                                    <th class="min-w-200px text-center" style="padding-right: 0.75rem;">Pensiun</th>
                                </tr>

                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="fw-bold text-gray-600">
                                <!--begin::Table row-->
                                <tr v-if="employeesByDivision.length < 1">
                                    <td colspan="7">
                                        <div v-cloak class="text-center">
                                            <em>Tidak ada data</em>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else v-for="(employee, index) in employeesByDivision" :key="employee.id">
                                    <!--begin::Category=-->
                                    <td>
                                        <div v-cloak>
                                            <div class="text-gray-800 text-hover-primary fs-5 fw-bolder mb-1">@{{ employee.name }}</div>
                                            <span class="text-muted">@{{ employee.number }}</span>
                                        </div>
                                    </td>
                                    <!--end::Category=-->
                                    <!--begin::Type=-->
                                    <!--begin::Job Title-->
                                    <td>
                                        <span class="text-gray-800 fs-5 mb-1">@{{ employee?.active_career?.job_title?.name }}</span>
                                    </td>
                                    <!--end::Job Title-->
                                    <!--begin::Gaji Pokok-->
                                    <td>
                                        <div v-if="!haveInsuranceByYear(employee, lastYear)" class="text-center">
                                            <small class="text-muted"><em>Tidak ada data</em></small>
                                        </div>
                                        <div v-else class="text-center">
                                            <span class="text-gray-800 fs-5 mb-1">@{{ currencyFormat(getInsuranceByYear(employee, lastYear).health) }}</span>
                                        </div>
                                    </td>
                                    <!--end::Gaji Pokok-->
                                    <!--begin::Tunjangan-->
                                    <td class="text-end">
                                        <div v-if="!haveInsuranceByYear(employee, lastYear)" class="text-center">
                                            <small class="text-muted"><em>Tidak ada data</em></small>
                                        </div>
                                        <div v-else class="text-center">
                                            <span class="text-gray-800 fs-5 mb-1">@{{ currencyFormat(getTotalRetirementByYear(employee, lastYear)) }}</span>
                                        </div>
                                    </td>
                                    <!--end::Tunjangan-->
                                    <!--begin::Kenaikan Gaji Pokok-->
                                    <td>
                                        <!-- <div v-if="!haveInsuranceByYear(employee, model.year)" class="text-center">
                                            <small class="text-muted"><em>Tidak ada data</em></small>
                                        </div>
                                        <div v-else class="text-end">
                                            </div> -->
                                        <input v-model="employee.new_health" type="text" class="form-control text-end form-control-sm" :placeholder="haveInsuranceByYear(employee, model.year) ? 'Nilai baru' : 'Kesehatan'" v-cleave="cleave.thousandFormat">
                                        <div v-if="haveInsuranceByYear(employee, model.year)" class="text-end mt-3 text-gray-500">
                                            <span>Nilai saat ini: </span> <span class="fs-5 mb-1">@{{ currencyFormat(getInsuranceByYear(employee, model.year)?.health) }}</span>
                                        </div>
                                    </td>
                                    <!--end::Kenaikan Gaji Pokok-->
                                    <!--begin::Kenaikan Persentase Gaji Pokok-->
                                    <td class="text-end">
                                        <!-- <div>
                                            <input v-model="employee.allowance_increase" type="text" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat">
                                        </div> -->
                                        <input v-model="employee.new_retirement" type="text" class="form-control text-end form-control-sm" :placeholder="haveInsuranceByYear(employee, model.year) ? 'Nilai baru' : 'Pensiun'" v-cleave="cleave.thousandFormat">
                                        <div v-if="haveInsuranceByYear(employee, model.year)" class="text-end mt-3 text-gray-500">
                                            <span>Nilai saat ini: </span> <span class="fs-5 mb-1">@{{ currencyFormat(getInsuranceByYear(employee, model.year)?.retirement) }}</span>
                                        </div>
                                    </td>
                                    <!--end::Kenaikan Persentase Gaji Pokok-->
                                    <!--begin::Kenaikan Tunjangan-->
                                    <td>
                                        <div v-if="employee.new_retirement" class="text-center">
                                            <span class="text-gray-800 fs-5 mb-1">@{{ currencyFormat(getTotalRetirementByYear(employee, lastYear) + Number(employee.new_retirement.replaceAll('.', ''))) }}</span>
                                        </div>
                                        <div v-else class="text-center">
                                            <span class="text-gray-800 fs-5 mb-1">@{{ currencyFormat(getTotalRetirementByYear(employee, lastYear) + (getInsuranceByYear(employee, model.year)?.retirement || 0)) }}</span>
                                        </div>
                                    </td>
                                    <!--end::Kenaikan Tunjangan-->
                                </tr>
                                <!--end::Table row-->
                            </tbody>
                            <!--end::Table body-->
                            <!--begin::Table footer-->
                            <tfoot>
                                <tr>
                                    <td>Total</td>
                                    <td></td>
                                    <td class="text-primary fs-5 text-center">@{{ currencyFormat(totalAll.totalHealth) }}</td>
                                    <td class="text-primary fs-5 text-center">@{{ currencyFormat(totalAll.totalRetirement) }}</td>
                                    <td class="text-primary fs-5 text-end">@{{ currencyFormat(totalAll.totalNewHealth) }}</td>
                                    <td class="text-primary fs-5 text-end">@{{ currencyFormat(totalAll.totalNewRetirement) }}</td>
                                    <td class="text-primary fs-5 text-center">@{{ currencyFormat(totalAll.totalFinalRetirement) }}</td>
                                </tr>
                            </tfoot>
                            <!--end::Table footer-->
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


        // datatable = $('#insurances-datatable').DataTable({
        //     "ordering": false,
        //     "drawCallback": function() {
        //         console.log('redraw table...')
        //     },
        //     "language": {
        //         "infoEmpty": " ",
        //         "zeroRecords": " "
        //     }
        // });


        // const filterSearch = document.querySelector('[data-insurance-employee-filter="search"]');
        // filterSearch.addEventListener('keyup', function(e) {
        //     datatable.search(e.target.value).draw();
        // });

    })
</script>
<script>
    const closeModal = (selector) => {
        const modalElement = document.querySelector(selector);
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();
    }

    const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;
    const companies = <?php echo Illuminate\Support\Js::from($companies) ?>;
    const divisions = <?php echo Illuminate\Support\Js::from($divisions) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                employees,
                companies,
                divisions,
                model: {
                    componentId: '',
                    year: '{{ date("Y") }}',
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
            lastYear() {
                const currentYear = Number(this.model.year);
                return currentYear - 1;
            },
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
                    const year = self.model.year;
                    return employees
                        .filter(employee => employee?.office?.division?.id == divisionId)
                    // .map(employee => {
                    //     const insurances = employee.insurances;
                    //     let newHealth = 0;
                    //     let newRetirement = 0;
                    //     if (insurances && Array.isArray(insurances)) {
                    //         const [insurance] = insurances.filter(insurance => insurance.year == year);
                    //         if (insurance) {
                    //             newHealth = insurance.health || 0;
                    //             newRetirement = insurance.retirement || 0;
                    //         }
                    //     }

                    //     return {
                    //         new_health: employee.new_health ? undefined : newHealth,
                    //         new_retirement: employee.new_retirement ? undefined : newRetirement,
                    //         ...employee,
                    //     }
                    // })
                }

                return [];
            },
            totalAll() {
                const employees = this.employeesByDivision;
                const year = this.model.year;
                const lastYear = this.lastYear;
                const newEmployees = employees
                    // .filter(employee => {
                    //     return employee.new_health || employee.new_retirement;
                    // })
                    .map(employee => {
                        const insurances = employee.insurances;
                        // const year = this.model.year;
                        // const lastYear = this.lastYear;
                        let healthAmount = 0;
                        let retirementAmount = 0;
                        let newHealth = 0;
                        let newRetirement = 0;

                        // let totalRetirementLastYear = 0;
                        if (insurances && Array.isArray(insurances)) {
                            const [insurance] = insurances.filter(insurance => insurance.year == lastYear);

                            if (insurance) {
                                healthAmount = insurance.health || 0;
                                // retirementAmount = insurance.retirement || 0;

                                retirementAmount = insurances
                                    .filter(insurance => Number(insurance.year) <= Number(lastYear))
                                    .map(insurance => Number(insurance.retirement))
                                    .reduce((acc, cur) => acc + cur, 0)

                            }

                            const [currentYearInsurance] = insurances.filter(insurance => insurance.year == year);
                            if (currentYearInsurance) {
                                newHealth = currentYearInsurance.health || 0;
                                newRetirement = currentYearInsurance.retirement || 0;
                            }
                        }

                        if (employee.new_health) {
                            if (typeof employee.new_health === 'string') {
                                newHealth = employee.new_health.replaceAll('.', '');
                            }
                        }

                        if (employee.new_retirement) {
                            if (typeof employee.new_retirement === 'string') {
                                newRetirement = employee.new_retirement.replaceAll('.', '');
                            }
                        }

                        newHealth = Number(newHealth);
                        newRetirement = Number(newRetirement);

                        return {
                            health: healthAmount,
                            retirement: retirementAmount,
                            newHealth,
                            newRetirement,
                        }

                    })

                const totalHealth = newEmployees.map(employee => Number(employee.health)).reduce((acc, cur) => acc + cur, 0);
                const totalNewHealth = newEmployees.map(employee => Number(employee.newHealth)).reduce((acc, cur) => acc + cur, 0);
                const totalFinalHealth = totalHealth + totalNewHealth;
                const totalRetirement = newEmployees.map(employee => Number(employee.retirement)).reduce((acc, cur) => acc + cur, 0);
                const totalNewRetirement = newEmployees.map(employee => Number(employee.newRetirement)).reduce((acc, cur) => acc + cur, 0);
                const totalFinalRetirement = totalRetirement + totalNewRetirement;

                return {
                    totalHealth,
                    totalNewHealth,
                    totalFinalHealth,
                    totalRetirement,
                    totalNewRetirement,
                    totalFinalRetirement,
                }

            },
            disabledSaveButton() {
                return this.submitLoading;
            },
            cleanEmployees() {
                const self = this;
                const {
                    employees
                } = this;
                if (Array.isArray(employees)) {
                    const newEmployees = employees
                        .filter(employee => {
                            return employee.new_health || employee.new_retirement;
                        })
                        .map(employee => {
                            let newHealth = employee.new_health || 0;
                            let newRetirement = employee.new_retirement || 0;
                            if (newHealth && typeof newHealth === 'string') {
                                newHealth = newHealth.replaceAll('.', '');
                            }
                            if (newRetirement && typeof newRetirement === 'string') {
                                newRetirement = newRetirement.replaceAll('.', '');
                            }

                            employee.new_health = Number(newHealth);
                            employee.new_retirement = Number(newRetirement);

                            return employee;

                        })

                    return newEmployees;
                }

                return [];
            },
        },
        methods: {
            // JOB TITLE METHODS
            async onSave() {
                let self = this;
                try {
                    self.submitLoading = true;
                    const employees = self.cleanEmployees;
                    const year = self.model.year;

                    const response = await axios.post('/insurances', {
                        employees,
                        year
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
            clearCurrencyFormat(currency) {
                if (currency) {
                    let newCurrency = '';
                    newCurrency = currency.replaceAll('.', '');
                    newCurrency = currency.replaceAll(',', '.');
                    return Number(currency);
                }

                return undefined;
            },
            getInsuranceByYear(employee, year) {
                const insurances = employee.insurances;
                // const year = this.model.year;
                // const lastYear = this.lastYear;
                if (insurances && Array.isArray(insurances)) {
                    const [insurance] = insurances.filter(component => component.year == year);
                    if (insurance) {
                        return insurance;
                    }
                }

                return null;
            },
            getTotalRetirementByYear(employee, year) {
                const insurances = employee.insurances;
                // const year = this.model.year;
                // const lastYear = this.lastYear;
                if (insurances && Array.isArray(insurances)) {
                    const total = insurances
                        .filter(insurance => Number(insurance.year) <= Number(year))
                        .map(insurance => Number(insurance.retirement))
                        .reduce((acc, cur) => acc + cur, 0)
                    return total;
                }

                return null;
            },
            haveInsuranceByYear(employee, year) {
                const insurances = employee.insurances;
                // const year = this.model.year;
                // const lastYear = this.lastYear;
                if (insurances && Array.isArray(insurances)) {
                    const currentYearInsurances = insurances.filter(component => component.year == year);
                    if (currentYearInsurances.length > 0) {
                        return true;
                    }
                }

                return false;
            },
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
</script>
@endsection