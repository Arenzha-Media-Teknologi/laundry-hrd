@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@inject('carbon', 'Carbon\Carbon')

@section('content')
<div id="kt_content_container" class="container-xxl">
    @include('settings.menu')
    <!-- begin::card -->
    <div class="card card-flush">
        <!--begin::Card header-->
        <div class="card-header p-0">
            @include('settings.payrolls.menu')
            <!--begin::Card title-->
            <!-- <div class="card-title">
                <h2>Komponen Gaji</h2>
            </div> -->
            <!--end::Card title-->
            <!--begin::Card title-->
            <!-- <div class="card-toolbar">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#salaryComponent_add_modal">Tambah Komponen</button>
            </div> -->
            <!--end::Card title-->
            <div class="d-flex w-100 justify-content-between align-items-center px-8">
                <div>
                    <h2>Komponen Gaji</h2>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#salaryComponent_add_modal">Tambah Komponen</button>
                </div>
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <div>
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_category_table">
                    <!--begin::Table head-->
                    <thead>
                        <!--begin::Table row-->
                        <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-250px">Nama</th>
                            <th class="min-w-150px">Tipe</th>
                            <th class="text-end min-w-70px">Actions</th>
                        </tr>
                        <!--end::Table row-->
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="fw-bold text-gray-600">
                        <!--begin::Table row-->
                        <tr v-cloak v-if="salaryComponents.length < 1">
                            <td colspan="3">
                                <div v-cloak class="text-center">
                                    <em>Tidak ada data</em>
                                </div>
                            </td>
                        </tr>
                        <tr v-cloak v-else v-for="(component, index) in salaryComponents" :key="component.id">
                            <!--begin::Category=-->
                            <td>
                                <span v-cloak class="text-gray-700 text-hover-primary fs-6 fw-bold mb-1">@{{ component.name }}</span>
                            </td>
                            <!--end::Category=-->
                            <!--begin::Type=-->
                            <td>
                                <div v-cloak>
                                    <!--begin::Badges-->
                                    <div v-if="component.type == 'income'" class="badge badge-light-success">Pendapatan</div>
                                    <div v-else-if="component.type == 'deduction'" class="badge badge-light-danger">Potongan</div>
                                    <div v-if="component.default" class="badge badge-light-info">Default</div>
                                    <!--end::Badges-->
                                </div>
                            </td>
                            <!--end::Type=-->
                            <!--begin::Action=-->
                            <td class="text-end">
                                <div v-cloak v-if="component.default == 0" class="d-flex justify-content-end">
                                    <!--begin::Share link-->
                                    <button type="button" class="btn btn-sm btn-icon btn-light-info ms-2" data-bs-toggle="modal" data-bs-target="#salaryComponent_edit_modal" @click="openSalaryComponentEditModal(component.id, index)">
                                        <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                        <span class="svg-icon svg-icon-5 m-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2" @click="openSalaryComponentDeleteConfirmation(component.id)">
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
        <!--end::Card body-->
    </div>
    <!-- end::card -->
    <!--begin::Modal - SalaryComponent - Add-->
    <div class="modal fade" id="salaryComponent_add_modal" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Form-->
                <form class="form" action="#" id="salaryComponent_add_modal_form">
                    <!--begin::Modal header-->
                    <div class="modal-header" id="salaryComponent_add_modal_header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bolder">Tambah Komponen Gaji</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div id="salaryComponent_add_modal_close" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body py-10 px-lg-17">
                        <!--begin::Scroll-->
                        <div class="scroll-y me-n7 pe-7" id="salaryComponent_add_modal_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#salaryComponent_add_modal_header" data-kt-scroll-wrappers="#salaryComponent_add_modal_scroll" data-kt-scroll-offset="300px">
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Nama</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" v-model="salaryComponent.model.add.name" class="form-control " placeholder="Masukkan nama" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Tipe</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="salaryComponent.model.add.type" class="form-select">
                                    <option value="income">Pendapatan</option>
                                    <option value="deduction">Potongan</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Scroll-->
                    </div>
                    <!--end::Modal body-->
                    <!--begin::Modal footer-->
                    <div class="modal-footer flex-end">
                        <!--begin::Button-->
                        <button type="reset" id="salaryComponent_add_modal_cancel" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="button" id="salaryComponent_add_modal_submit" :data-kt-indicator="salaryComponent.submitLoading ? 'on' : null" class="btn btn-primary" @click="onSubmitSalaryComponent">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Mengirim data...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <!--end::Button-->
                    </div>
                    <!--end::Modal footer-->
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>
    <!--end::Modal - SalaryComponent - Add-->
    <!--begin::Modal - SalaryComponent - Edit-->
    <div class="modal fade" id="salaryComponent_edit_modal" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Form-->
                <form class="form" action="#" id="salaryComponent_edit_modal_form">
                    <!--begin::Modal header-->
                    <div class="modal-header" id="salaryComponent_edit_modal_header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bolder">Ubah Komponen Gaji</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div id="salaryComponent_edit_modal_close" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body py-10 px-lg-17">
                        <!--begin::Scroll-->
                        <div class="scroll-y me-n7 pe-7" id="salaryComponent_edit_modal_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#salaryComponent_edit_modal_header" data-kt-scroll-wrappers="#salaryComponent_edit_modal_scroll" data-kt-scroll-offset="300px">
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Nama</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" v-model="salaryComponent.model.edit.name" class="form-control" placeholder="Masukkan nama" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Tipe</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="salaryComponent.model.edit.type" class="form-select">
                                    <option value="income">Pendapatan</option>
                                    <option value="deduction">Potongan</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Scroll-->
                    </div>
                    <!--end::Modal body-->
                    <!--begin::Modal footer-->
                    <div class="modal-footer flex-end">
                        <!--begin::Button-->
                        <button type="reset" id="salaryComponent_edit_modal_cancel" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="button" id="salaryComponent_edit_modal_submit" :data-kt-indicator="salaryComponent.submitLoading ? 'on' : null" class="btn btn-primary" @click="onSubmitEditSalaryComponent">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Mengirim data...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <!--end::Button-->
                    </div>
                    <!--end::Modal footer-->
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>
    <!--end::Modal - SalaryComponent - Edit-->
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
    const closeAddModal = (selector) => {
        const addDepartmentModal = document.querySelector(selector);
        const modal = bootstrap.Modal.getInstance(addDepartmentModal);
        modal.hide();
    }

    const closeEditModal = (selector) => {
        const editDepartmentModal = document.querySelector(selector);
        const modal = bootstrap.Modal.getInstance(editDepartmentModal);
        modal.hide();
    }

    const salaryComponents = <?php echo Illuminate\Support\Js::from($salary_components) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                salaryComponents,
                salaryComponent: {
                    model: {
                        add: {
                            name: '',
                            type: 'income',
                        },
                        edit: {
                            index: null,
                            id: null,
                            name: '',
                            type: '',
                        }
                    },
                    submitLoading: false,
                }
            }
        },
        methods: {
            // JOB TITLE METHODS
            async onSubmitSalaryComponent() {
                let self = this;
                try {
                    const {
                        name,
                        type,
                    } = self.salaryComponent.model.add;

                    self.salaryComponent.submitLoading = true;

                    const response = await axios.post('/salary-components', {
                        name,
                        type: type,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        // const rowData = [
                        //     textFormatter(name),
                        //     textFormatter(initial),
                        //     textFormatter(address),
                        //     actionButton(data?.id)
                        // ]

                        // addTableRow(rowData)
                        this.addSalaryComponent(data);
                        // redrawDatatable();
                        closeAddModal('#salaryComponent_add_modal');
                        this.resetSalaryComponentForm();
                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.salaryComponent.submitLoading = false;
                }
            },
            addSalaryComponent(data) {
                if (data) {
                    this.salaryComponents.push(data)
                }
            },
            resetSalaryComponentForm() {
                this.salaryComponent.model.add.name = '';
                this.salaryComponent.model.add.type = 'income';

                this.salaryComponent.model.edit.name = '';
                this.salaryComponent.model.edit.type = '';
            },
            openSalaryComponentEditModal(id, index) {
                const [salaryComponent] = this.salaryComponents.filter(salaryComponent => salaryComponent.id == id);
                console.log(id, salaryComponent)
                if (salaryComponent) {
                    this.salaryComponent.model.edit.index = index
                    this.salaryComponent.model.edit.id = id
                    this.salaryComponent.model.edit.name = salaryComponent.name
                    this.salaryComponent.model.edit.type = salaryComponent.type
                }
            },
            async onSubmitEditSalaryComponent() {
                let self = this;
                try {
                    const {
                        id,
                        index,
                        name,
                        type,
                    } = self.salaryComponent.model.edit;

                    self.salaryComponent.submitLoading = true;

                    const response = await axios.post(`/salary-components/${id}`, {
                        name,
                        type: type,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        this.editSalaryComponent(index, data);
                        // redrawDatatable();
                        closeEditModal('#salaryComponent_edit_modal');
                        this.resetSalaryComponentForm();
                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.salaryComponent.submitLoading = false;
                }
            },
            editSalaryComponent(index, data) {
                this.salaryComponents.splice(index, 1, data);
            },
            openSalaryComponentDeleteConfirmation(id) {
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
                        return self.sendSalaryComponentDeleteRequest(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            sendSalaryComponentDeleteRequest(id) {
                const self = this;
                return axios.delete('/salary-components/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        self.deleteSalaryComponent(id);
                        // redrawDatatable();
                        toastr.success(message);
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
            deleteSalaryComponent(id) {
                this.salaryComponents = this.salaryComponents.filter(salaryComponent => salaryComponent.id !== id);
            },
        },
    })
</script>
@endsection