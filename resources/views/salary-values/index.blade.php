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
        <div class="card card-flush py-4 border-top border-top-2 border-primary">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Nilai Gaji</h2>
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
                        <input type="text" data-salary-increase-filter="search" class="form-control w-100 w-lg-50 ps-14" placeholder="Cari Pegawai" />
                    </div>
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table align-middle table-striped table-row-bordered" id="salary-increases-datatable">
                            <!--begin::Table head-->
                            <thead>
                                <!--begin::Table row-->
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-250px text-start align-middle ps-3" rowspan="2">Nama</th>
                                    <th class="min-w-150px text-start align-middle" rowspan="2">Job Title</th>
                                    <!-- <th class="text-center" colspan="5">Saat Ini</th> -->
                                    <th class="text-center" colspan="6">Nilai Gaji</th>
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
                                    <th class="text-end min-w-200px">Gaji Pokok</th>

                                    <th class="text-end min-w-200px">Tunjangan</th>

                                    <th class="text-end min-w-200px">Harian</th>
                                    <th class="text-end min-w-100px">Koef. Harian</th>

                                    <th class="text-end min-w-200px">Lembur</th>
                                    <th class="text-end min-w-100px pe-3">Koef. Lembur</th>
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
                                <tr v-if="employeesByDivision.length < 1">
                                    <td>
                                        <div class="text-center text-muted">
                                            <em>Tidak ada data</em>
                                        </div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr v-cloak v-else v-for="(employee, index) in employeesByDivision" :key="employee.id">
                                    <!--begin::Category=-->
                                    <td>
                                        <div class="ps-3">
                                            <div class="text-gray-700 text-hover-primary fs-6 fw-bolder mb-1">@{{ employee.name }}</div>
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


                                    <!-- end::Gaji Sekarang ================================= -->
                                    <!-- begin::gaji ================================= -->
                                    <!--begin::gaji Pokok-->
                                    <td>
                                        <!-- <div v-if="haveSalaryComponent(employee, 'gaji_pokok')">
                                        </div> -->
                                        <input type="text" v-model="employee.gaji_pokok_value" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat">
                                    </td>
                                    <!--end::gaji Pokok-->
                                    <!--begin::Kenaikan Persentase Gaji Pokok-->

                                    <!--end::Kenaikan Persentase Gaji Pokok-->
                                    <!--begin::Kenaikan Tunjangan-->
                                    <td>
                                        <input type="text" v-model="employee.tunjangan_value" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat">
                                    </td>
                                    <!--end::Kenaikan Tunjangan-->
                                    <!--begin::Kenaikan Persentase Tunjangan-->

                                    <!--end::Kenaikan Persentase Tunjangan-->
                                    <!--begin::Kenaikan Harian-->
                                    <td>
                                        <input type="text" v-model="employee.uang_harian_value" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat">
                                    </td>
                                    <td>
                                        <input type="number" v-model="employee.uang_harian_coefficient" class="form-control text-end form-control-sm">
                                    </td>
                                    <!--end::Kenaikan Harian-->
                                    <!--begin::Kenaikan Persentase Harian-->

                                    <!--end::Kenaikan Persentase Harian-->
                                    <!--begin::Kenaikan Lembur-->
                                    <td>

                                        <input type="text" v-model="employee.lembur_value" class="form-control text-end form-control-sm" v-cleave="cleave.thousandFormat">
                                    </td>
                                    <td class="pe-3">
                                        <input type="number" v-model="employee.lembur_coefficient" class="form-control text-end form-control-sm">
                                    </td>
                                    <!--end::Kenaikan Lembur-->
                                    <!--begin::Kenaikan Persentase Lembur-->
                                    <!-- end::gaji ================================= -->
                                    <!-- begin::Gaji Baru =================================-->
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
                                    <td class="text-end fs-5 text-success fw-bold">@{{ currencyFormat(totalAll2.gaji_pokok_total) }}</td>
                                    <td class="text-end fs-5 text-success fw-bold">@{{ currencyFormat(totalAll2.tunjangan_total) }}</td>
                                    <td class="text-end fs-5 text-success fw-bold">@{{ currencyFormat(totalAll2.uang_harian_total) }}</td>
                                    <td></td>
                                    <td class="text-end fs-5 text-success fw-bold">@{{ currencyFormat(totalAll2.lembur_total) }}</td>
                                    <td></td>
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
            totalAll2() {
                let totalAll = {};
                const employees = this.employeesByDivision;
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

                    return values;
                });

                const salaryComponentTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'lembur'];

                salaryComponentTypes.forEach(salaryComponentType => {
                    const total = newEmployees.map(employee => employee[salaryComponentType + '_value']).reduce((acc, cur) => acc + cur, 0);

                    totalAll = {
                        ...totalAll,
                        [salaryComponentType + '_total']: total || 0,
                    }
                });

                return totalAll;
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
                    const employees = self.clearCurrencyFormatAll(self.employees);

                    // console.log(employees);
                    // return;

                    const response = await axios.post('/salary-values', {
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