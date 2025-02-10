@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@inject('carbon', 'Carbon\Carbon')

@section('content')
<div id="kt_content_container" class="container-xxl">
    @include('settings.menu')
    <!--begin::Row-->
    <div class="form d-flex flex-column flex-lg-row">
        <!--begin::Aside column-->
        <div class="w-100 flex-lg-row-auto w-lg-300px mb-7 me-7 me-lg-10">
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
                    <div class="d-flex flex-column gap-10">
                        <!--begin::Input group-->
                        <div class="fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Komponen Gaji</label>
                            <!--end::Label-->
                            <!--begin::Select2-->
                            <select v-model="model.componentId" class="form-select">
                                <option value="">Pilih Komponen Gaji</option>
                                <optgroup label="Pendapatan">
                                    <?php $incomes = collect($salary_components)->where('type', 'income')->all() ?>
                                    @foreach($incomes as $income)
                                    <option value="{{ $income->id }}">{{ $income->name }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Potongan">
                                    <?php $deductions = collect($salary_components)->where('type', 'deduction')->all() ?>
                                    @foreach($deductions as $deduction)
                                    <option value="{{ $deduction->id }}">{{ $deduction->name }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            <!--end::Select2-->
                            <!--begin::Description-->
                            <!-- <div class="text-muted fs-7">Set the date of the order to process.</div> -->
                            <!--end::Description-->
                        </div>
                        <!--end::Input group-->
                    </div>
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
                        <h2>Pilih Pegawai</h2>
                    </div>
                    <div class="card-toolbar">
                        <button v-cloak v-if="model.componentId" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_add_employee">Tambah</button>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <div class="d-flex flex-column gap-10">
                        <!--begin::Input group-->
                        <div>
                            <!--begin::Label-->
                            <label class="form-label">Tambah pegawai ke dalam komponen gaji</label>
                            <!--end::Label-->
                            <!--begin::Selected products-->
                            <div class="row row-cols-1 row-cols-xl-3 row-cols-md-2 border border-dashed rounded pt-3 pb-1 px-2 mb-5 mh-300px overflow-scroll" id="kt_ecommerce_edit_order_selected_products">
                                <!--begin::Empty message-->
                                <span class="w-100 text-muted">Pilih satu atau beberapa pegawai yang memiliki komponen gaji ini</span>
                                <!--end::Empty message-->
                            </div>
                            <!--begin::Selected products-->
                            <!--begin::Total price-->

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
                            <input type="text" data-kt-ecommerce-edit-order-filter="search" class="form-control form-control-solid w-100 w-lg-50 ps-14" placeholder="Cari Pegawai" />
                        </div>
                        <!--end::Search products-->
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <!--begin::Table head-->
                            <thead>
                                <!--begin::Table row-->
                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-250px">Nama</th>
                                    <th class="min-w-150px text-end">Jumlah</th>
                                    <th v-if="isHaveCoefficient" class="text-end">Koefisien</th>
                                    <th class="text-end min-w-70px">Actions</th>
                                </tr>
                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="fw-bold text-gray-600">
                                <!--begin::Table row-->
                                <tr v-cloak v-if="employeesByComponent.length < 1">
                                    <td colspan="3">
                                        <div v-cloak class="text-center">
                                            <em>Tidak ada data</em>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-cloak v-else v-for="(employee, index) in employeesByComponent" :key="employee.id">
                                    <!--begin::Category=-->
                                    <td>
                                        <div v-cloak>
                                            <div class="text-gray-800 text-hover-primary fs-5 fw-bolder mb-1">@{{ employee.name }}</div>
                                            <span class="text-muted">@{{ employee.number }}</span>
                                        </div>
                                    </td>
                                    <!--end::Category=-->
                                    <!--begin::Type=-->
                                    <td>
                                        <input v-model="employee.pivot.amount" type="text" class="form-control text-end">
                                    </td>
                                    <!--end::Type=-->
                                    <!--begin::Type=-->
                                    <td v-if="isHaveCoefficient">
                                        <input v-model="employee.pivot.coefficient" type="text" class="form-control text-end">
                                    </td>
                                    <!--end::Type=-->
                                    <!--begin::Action=-->
                                    <td class="text-end">
                                        <div v-cloak class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2" @click="removeFromAssignedEmployees(index)">
                                                <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                                <span class="svg-icon svg-icon-5 m-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black" />
                                                        <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black" />
                                                        <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </button>
                                        </div>
                                    </td>
                                    <!--end::Action=-->
                                </tr>
                                <!--end::Table row-->
                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                    </div>
                </div>
                <!--end::Card header-->
            </div>
            <!--end::Order details-->
            <div class="d-flex justify-content-end">
                <!--begin::Button-->
                <!-- <a href="../../demo1/dist/apps/ecommerce/catalog/products.html" id="kt_ecommerce_edit_order_cancel" class="btn btn-light me-5">Cancel</a> -->
                <!--end::Button-->
                <!--begin::Button-->
                <button type="button" class="btn btn-primary" :data-kt-indicator="submitLoading ? 'on' : null" @click="onSave">
                    <span class="indicator-label">Simpan Perubahan</span>
                    <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
                <!--end::Button-->
            </div>
        </div>
        <!--end::Main column-->
    </div>
    <!--end::Row-->
    <!-- end::card -->
    <div class="modal fade" tabindex="-1" id="modal_add_employee">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pegawai</h5>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-2x"></span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped gy-7 gs-7">
                            <thead>
                                <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                    <th>
                                        <input type="checkbox">
                                    </th>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(employee, index) in employeesNotInComponent" :key="employee.id">
                                    <td>
                                        <input type="checkbox" v-model="selectedUnassignedEmployeesIds" :value="employee.id">
                                    </td>
                                    <td>@{{ employee?.name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="assignEmployees">Tambah</button>
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


        // datatable = $('#department-datatable').DataTable({
        //     "drawCallback": function() {
        //         console.log('redraw table...')
        //     },
        //     "language": {
        //         "infoEmpty": " ",
        //         "zeroRecords": " "
        //     }
        // });

        // const handleSearchDatatable = () => {
        // const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
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

    const salaryComponents = <?php echo Illuminate\Support\Js::from($salary_components) ?>;
    const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                salaryComponents,
                employees,
                model: {
                    componentId: ''
                },
                selectedUnassignedEmployeesIds: [],
                submitLoading: false,
            }
        },
        computed: {
            selectedComponent() {
                const {
                    componentId
                } = this.model;
                if (componentId) {
                    const [component] = this.salaryComponents.filter(comp => comp.id == componentId);
                    if (component) {
                        return component;
                    }
                }

                return null;
            },
            isHaveCoefficient() {
                const {
                    selectedComponent
                } = this;
                if (selectedComponent) {
                    if (selectedComponent.salary_type == 'uang_harian' || selectedComponent.salary_type == 'lembur') {
                        return true;
                    }
                }

                return false;
            },
            employeesByComponent() {
                const self = this;
                const {
                    componentId
                } = self.model;
                if (componentId) {
                    const [component] = self.salaryComponents.filter(comp => comp.id == componentId);
                    if (component) {
                        const employees = component?.employees || [];
                        return employees;
                        // return employees.map(employee => {
                        //     const newAmount = new Intl.NumberFormat('De-de').format(employee?.pivot?.amount);
                        //     employee.pivot.amount = newAmount;
                        //     return employee;
                        // });
                    }
                }

                return [];
            },
            employeesNotInComponent() {
                const self = this;
                const allEmployees = self.employees;
                const componentId = self.model.componentId;
                if (componentId) {
                    const [component] = self.salaryComponents.filter(comp => comp.id == componentId);
                    if (component) {
                        const componentEmployees = component?.employees || [];
                        if (Array.isArray(componentEmployees)) {
                            const componentEmployeesIds = componentEmployees.map(employee => employee.id);
                            const unassignedEmployees = allEmployees.filter(employee => !componentEmployeesIds.includes(employee.id))
                            return unassignedEmployees;
                        }
                        return ['Component employees is not an array'];
                    }

                    return ['No component was found'];
                }

                return allEmployees;
            }
        },
        methods: {
            // JOB TITLE METHODS
            async onSave() {
                let self = this;
                try {
                    self.submitLoading = true;
                    const salaryComponents = self.salaryComponents;

                    const response = await axios.post('/settings/payrolls/assign-employees', {
                        salary_components: salaryComponents,
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
                return new Intl.NumberFormat('De-de').format(number);
            },
            assignEmployees() {
                const self = this;
                const {
                    employees,
                    selectedUnassignedEmployeesIds,
                    salaryComponents,
                } = self;

                const componentId = self.model.componentId;

                const selectedUnassignedEmployees = employees.filter(employee => selectedUnassignedEmployeesIds.includes(employee.id)).map(employee => {
                    return {
                        ...{
                            pivot: {
                                amount: 0,
                            }
                        },
                        ...employee
                    }
                });

                const [component] = self.salaryComponents.filter(comp => comp.id == componentId);
                if (component) {
                    component.employees = [...component.employees, ...selectedUnassignedEmployees];
                }

                self.resetSelectedUnassignedEmployeesIds();
                closeModal('#modal_add_employee');
            },
            resetSelectedUnassignedEmployeesIds() {
                return this.selectedUnassignedEmployeesIds = [];
            },
            removeFromAssignedEmployees(index) {
                const self = this;
                const {
                    salaryComponents,
                } = self;

                const componentId = self.model.componentId;


                const [component] = self.salaryComponents.filter(comp => comp.id == componentId);
                if (component) {
                    component.employees.splice(index, 1);
                }
            },
        },
    })

    var myModalEl = document.getElementById('modal_add_employee')
    myModalEl.addEventListener('hidden.bs.modal', function(event) {
        app.$data.selectedUnassignedEmployeesIds = [];
    })
</script>
@endsection