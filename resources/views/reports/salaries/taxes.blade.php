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
                    <h2>Laporan Honor Pajak</h2>
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
                            <div class="col-md-6">
                                <!--begin::Label-->
                                <label class="form-label">Periode</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="month" class="form-control form-control-sm" value="{{ date('Y-m') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="month" class="form-control form-control-sm" value="{{ date('Y-m') }}">
                                    </div>
                                </div>

                                <!--end::Input-->
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button class="btn btn-primary btn-sm"><i class="bi bi-filter"></i> Terapkan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="division-table">
                        <!--begin::Search products-->
                        <div class="d-flex align-items-center position-relative mb-8">
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
                        <div class="table-responsive">
                            <table class="table table-rounded table-striped border gy-7 gs-7">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th>No</th>
                                        <th class="min-w-200px">ID</th>
                                        <th class="min-w-200px">Nama</th>
                                        <th class="min-w-200px">NPWP</th>
                                        <th class="min-w-200px">Status PTKP</th>
                                        <th class="min-w-200px">Tgl Masuk</th>
                                        <th class="min-w-200px">Honor</th>
                                        <th class="min-w-200px">THR</th>
                                        <th class="min-w-200px">Nominal PTKP</th>
                                        <th class="min-w-200px">T. Jabatan</th>
                                        <th class="min-w-200px">PPh21</th>
                                        <th class="min-w-200px">Take Home Pay</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <!-- --- -->
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <td colspan="6">Sub-Total</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <td colspan="6">Grand-Total</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card header-->
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