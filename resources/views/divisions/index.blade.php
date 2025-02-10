@extends('layouts.app')

@section('title', 'Divisi')

@section('prehead')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('pagestyle')
<style>
    .dataTables_empty {
        display: none;
    }

    /* table.dataTable.table-striped>tbody>tr.odd {
        background-color: #fafafa;
    } */
</style>
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!-- <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_4">Divisi</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_5">Link 2</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_6">Link 3</a>
        </li>
    </ul> -->
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0">Daftar Divisi</h1>
            <p class="text-muted mb-0">Daftar semua divisi</p>
        </div>
        <div class="d-flex">
            @can('create', App\Models\Division::class)
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <!--begin::Add customer-->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_division"><i class="bi bi-plus-lg"></i> Tambah Divisi</button>
                <!--end::Add customer-->
            </div>
            @endcan
        </div>
    </div>
    <div class="separator my-5" style="border-bottom-width: 3px;"></div>
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                    <span class="svg-icon svg-icon-1 position-absolute ms-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <input type="text" data-kt-customer-table-filter="search" class="form-control w-300px ps-15" placeholder="Cari Divisi" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6" id="kt_customers_table">
                <!--begin::Table head-->
                <thead class="bg-light-primary">
                    <!--begin::Table row-->
                    <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                        <!-- <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_customers_table .form-check-input" value="1" />
                            </div>
                        </th> -->
                        <th class="min-w-125px ps-2">Nama</th>
                        <th>Perusahaan</th>
                        <th class="min-w-125px">ID</th>
                        <th class="min-w-125px">Alamat</th>
                        <th class="text-end min-w-70px pe-2">Actions</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="fw-bold text-gray-600">
                    <tr v-for="(division, index) in divisions" :key="division.id">
                        <!--begin::Checkbox-->
                        <!-- <td>
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" />
                            </div>
                        </td> -->
                        <!--end::Checkbox-->
                        <!--begin::Name=-->
                        <td class="ps-2">
                            <span class="text-gray-800" v-cloak>@{{ division.name }}</span>
                        </td>
                        <!--end::Name=-->
                        <!--begin::Name=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ division?.company?.name || "-" }}</span>
                        </td>
                        <!--end::Name=-->
                        <!--begin::Email=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ division.initial }}</span>
                        </td>
                        <!--end::Email=-->
                        <!--begin::Division=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ division.address }}</span>
                        </td>
                        <!--end::Division=-->
                        <!--begin::Action=-->
                        <td class="text-end pe-2">
                            <div class="d-flex justify-content-end">
                                @can('update', App\Models\Division::class)
                                <!--begin::Share link-->
                                <button type="button" class="btn btn-sm btn-icon btn-light-info ms-2" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_division" @click="openDivisionEditModal(division.id, index)">
                                    <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </button>
                                @endcan
                                @can('delete', App\Models\Division::class)
                                <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2" @click="openDivisionDeleteConfirmation(division.id)">
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
                                @endcan
                            </div>
                        </td>
                        <!--end::Action=-->
                    </tr>
                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
    <!--begin::Modals-->
    <!--begin::Modal - Division - Add-->
    <div class="modal fade" id="kt_modal_add_division" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Form-->
                <form class="form" action="#" id="kt_modal_add_division_form" data-kt-redirect="../../demo1/dist/apps/customers/list.html">
                    <!--begin::Modal header-->
                    <div class="modal-header" id="kt_modal_add_division_header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bolder">Tambah Divisi</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div id="kt_modal_add_division_close" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
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
                        <div class="scroll-y me-n7 pe-7" id="kt_modal_add_division_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_division_header" data-kt-scroll-wrappers="#kt_modal_add_division_scroll" data-kt-scroll-offset="300px">
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Perusahaan</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="divisionModel.add.companyId" class="form-control form-control-solid" placeholder="Pilih Perusahaan">
                                    <option value="">Pilih Perusahaan</option>
                                    <option v-for="(company, index) in companies" :key="company.id" :value="company.id">@{{ company.name }}</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Nama</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" v-model="divisionModel.add.name" class="form-control form-control-solid" placeholder="Masukkan nama" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold mb-2">
                                    <span class="required">ID</span>
                                    <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" v-model="divisionModel.add.initial" class="form-control form-control-solid" placeholder="Masukkan ID (Contoh: MM, CC, OL)" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-15">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold mb-2">Alamat</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <!-- <input type="text" class="form-control form-control-solid" placeholder="" name="description" /> -->
                                <!--end::Input-->
                                <textarea v-model="divisionModel.add.address" class="form-control form-control-solid" cols="4" placeholder="Masukkan alamat"></textarea>
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Scroll-->
                    </div>
                    <!--end::Modal body-->
                    <!--begin::Modal footer-->
                    <div class="modal-footer flex-center">
                        <!--begin::Button-->
                        <button type="reset" id="kt_modal_add_division_cancel" class="btn btn-light me-3">Batal</button>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="button" id="kt_modal_add_division_submit" :data-kt-indicator="submitDivisionLoading ? 'on' : null" class="btn btn-primary" @click="onSubmitDivision">
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
    <!--end::Modal - Division - Add-->
    <!--begin::Modal - Division - Edit-->
    <div class="modal fade" id="kt_modal_edit_division" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Form-->
                <form class="form" action="#" id="kt_modal_edit_division_form" data-kt-redirect="../../demo1/dist/apps/customers/list.html">
                    <!--begin::Modal header-->
                    <div class="modal-header" id="kt_modal_edit_division_header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bolder">Ubah Divisi</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div id="kt_modal_edit_division_close" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
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
                        <div class="scroll-y me-n7 pe-7" id="kt_modal_add_division_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_division_header" data-kt-scroll-wrappers="#kt_modal_add_division_scroll" data-kt-scroll-offset="300px">
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Perusahaan</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="divisionModel.edit.companyId" class="form-control form-control-solid" placeholder="Pilih Perusahaan">
                                    <option value="">Pilih Perusahaan</option>
                                    <option v-for="(company, index) in companies" :key="company.id" :value="company.id">@{{ company.name }}</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Nama</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" v-model="divisionModel.edit.name" class="form-control form-control-solid" placeholder="Masukkan nama" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold mb-2">
                                    <span class="required">ID</span>
                                    <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" v-model="divisionModel.edit.initial" class="form-control form-control-solid" placeholder="Masukkan ID (Contoh: MM, CC, OL)" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-15">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold mb-2">Alamat</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <!-- <input type="text" class="form-control form-control-solid" placeholder="" name="description" /> -->
                                <!--end::Input-->
                                <textarea v-model="divisionModel.edit.address" class="form-control form-control-solid" cols="4" placeholder="Masukkan alamat"></textarea>
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Scroll-->
                    </div>
                    <!--end::Modal body-->
                    <!--begin::Modal footer-->
                    <div class="modal-footer flex-center">
                        <!--begin::Button-->
                        <button type="reset" id="kt_modal_edit_division_cancel" class="btn btn-light me-3">Batal</button>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="button" id="kt_modal_edit_division_submit" :data-kt-indicator="submitEditDivisionLoading ? 'on' : null" class="btn btn-primary" @click="onSubmitEditDivision">
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
    <!--end::Modal - Division - Edit-->
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


        datatable = $('#kt_customers_table').DataTable({
            "columnDefs": [{
                "targets": 3,
                // "searchable": false
                "className": "text-end",
            }],
            "drawCallback": function() {
                console.log('redraw table...')
            },
            "language": {
                "infoEmpty": " ",
                "zeroRecords": " "
            }
        });

        // const handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });
    })
</script>
<script>
    const redrawDatatable = () => {
        datatable.draw();
    }

    const closeDivisionAddModal = () => {
        const addDivisionModal = document.querySelector('#kt_modal_add_division');
        const modal = bootstrap.Modal.getInstance(addDivisionModal);
        modal.hide();
    }

    const closeDivisionEditModal = () => {
        const addDivisionModal = document.querySelector('#kt_modal_edit_division');
        const modal = bootstrap.Modal.getInstance(addDivisionModal);
        modal.hide();
    }

    const divisions = <?php echo Illuminate\Support\Js::from($divisions) ?>;
    const companies = <?php echo Illuminate\Support\Js::from($companies) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                companies: companies,
                divisions: divisions,
                divisionModel: {
                    add: {
                        name: '',
                        initial: '',
                        companyId: '',
                        address: '',
                    },
                    edit: {
                        index: null,
                        id: null,
                        name: '',
                        initial: '',
                        companyId: '',
                        address: '',
                    }
                },
                submitDivisionLoading: false,
                submitEditDivisionLoading: false,
            }
        },
        methods: {
            // COMPANY METHODS
            async onSubmitDivision() {
                let self = this;
                try {
                    const {
                        name,
                        initial,
                        address,
                        companyId,
                    } = self.divisionModel.add;

                    self.submitDivisionLoading = true;

                    const response = await axios.post('/divisions', {
                        name,
                        initial,
                        address,
                        company_id: companyId
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        this.addDivision(data);
                        closeDivisionAddModal();
                        this.resetDivisionForm();
                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.submitDivisionLoading = false;
                }
            },
            addDivision(data) {
                if (data) {
                    this.divisions.push(data)
                }
            },
            openDivisionDeleteConfirmation(id) {
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
                        return self.sendDivisionDeleteRequest(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            sendDivisionDeleteRequest(id) {
                const self = this;
                return axios.delete('/divisions/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        self.deleteDivision(id);
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
            deleteDivision(id) {
                this.divisions = this.divisions.filter(division => division.id !== id);
            },
            openDivisionEditModal(id, index) {
                const [division] = this.divisions.filter(division => division.id === id);
                if (division) {
                    this.divisionModel.edit.index = index
                    this.divisionModel.edit.id = division.id
                    this.divisionModel.edit.companyId = division.company_id
                    this.divisionModel.edit.name = division.name
                    this.divisionModel.edit.initial = division.initial
                    this.divisionModel.edit.address = division.address
                }
            },
            async onSubmitEditDivision() {
                let self = this;
                try {
                    const {
                        index,
                        id,
                        name,
                        initial,
                        address,
                        companyId,
                    } = self.divisionModel.edit;

                    self.submitEditDivisionLoading = true;

                    const response = await axios.post(`/divisions/${id}`, {
                        name,
                        initial,
                        address,
                        company_id: companyId,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        this.editDivision(index, data);
                        closeDivisionEditModal();
                        this.resetDivisionForm();
                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.submitEditDivisionLoading = false;
                }
            },
            editDivision(index, data) {
                this.divisions.splice(index, 1, data);
            },
            resetDivisionForm() {
                this.divisionModel = {
                    add: {
                        name: '',
                        initial: '',
                        address: '',
                        companyId: null,
                    },
                    edit: {
                        index: null,
                        id: null,
                        name: '',
                        initial: '',
                        address: '',
                        companyId: null,
                    }
                }
            }
        },
    })
</script>
@endsection