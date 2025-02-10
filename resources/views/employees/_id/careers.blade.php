@extends('layouts.app')

@section('title', $employee->name . ' - Karir')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@inject('carbon', 'Carbon\Carbon')

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!--begin::Navbar-->
    <x-employee-detail-card :employee="$employee" />
    <!--end::Navbar-->
    <!--begin::Row-->
    <div class="row gy-5 g-xl-10">
        <!--begin::Col-->
        <div class="col-xl-4 mb-xl-10">
            <!--begin::List widget 16-->
            <div class="card card-flush h-xl-100">
                <!--begin::Header-->
                <div class="card-header pt-7">
                    <!--begin::Title-->
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder text-gray-800">Riwayat Karir</span>
                        <span class="text-gray-400 mt-1 fw-bold fs-6">Riwayat karir pegawai</span>
                    </h3>
                    <!--end::Title-->
                    <!--begin::Toolbar-->
                    <!-- <div class="card-toolbar">
                        <a href="#" class="btn btn-sm btn-light">Tambah</a>
                    </div> -->
                    <!--end::Toolbar-->
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body pt-4">
                    <!-- begin:Card content -->
                    <div>
                        <!--begin::Timeline-->
                        <div v-cloak class="timeline ms-n1">
                            <!--begin::Timeline item-->
                            <div v-for="(career, index) in careers" class="timeline-item align-items-center mb-4">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-20px mt-9 mb-n14"></div>
                                <!--end::Timeline line-->
                                <!--begin::Timeline icon-->
                                <div class="timeline-icon pt-1" style="margin-left: 0.7px">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen015.svg-->
                                    <span class="svg-icon svg-icon-2 svg-icon-info">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z" fill="black" />
                                            <path d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z" fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </div>
                                <!--end::Timeline icon-->
                                <!--begin::Timeline content-->
                                <div class="timeline-content m-0">
                                    <!--begin::Label-->
                                    <span class="fs-8 fw-boldest text-success text-uppercase">@{{ career?.effective_date }}</span>
                                    <!--begin::Label-->
                                    <!--begin::Title-->
                                    <a href="#" class="fs-6 text-gray-800 fw-bolder d-block text-hover-primary">@{{ career?.job_title?.designation?.department?.name }} - @{{ career?.job_title?.designation?.name }} - @{{ career?.job_title?.name }}</a>
                                    <!--end::Title-->
                                    <!--begin::Title-->
                                    <span class="fw-bold text-gray-400 text-capitalize">Pegawai @{{ career?.status }}</span>
                                    <!--end::Title-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->
                        </div>
                        <!--end::Timeline-->
                    </div>
                    <!-- end:Card content -->
                </div>
                <!--end: Card Body-->
            </div>
            <!--end::List widget 16-->
        </div>
        <!--end::Col-->
        <!--begin::Col-->
        <div class="col-xl-8 mb-5 mb-xl-10">
            <!--begin::Chart widget 12-->
            <div class="card card-flush h-xl-100">
                <!--begin::Header-->
                <div class="card-header pt-7 mb-3">
                    <!--begin::Title-->
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder text-gray-800">Data Karir</span>
                        <span class="text-gray-400 mt-1 fw-bold fs-6">Data karir pegawai</span>
                    </h3>
                    <!--end::Title-->
                    <!--begin::Toolbar-->
                    <div class="card-toolbar">
                        @can('createCareer', App\Models\Employee::class)
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#career_add_modal">
                            Karir Baru
                        </button>
                        @endcan
                    </div>
                    <!--end::Toolbar-->
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body p-0">
                    <!--begin::Table wrapper-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table table-flush align-middle table-row-bordered table-row-solid gy-4 gs-9">
                            <!--begin::Thead-->
                            <thead class="border-gray-200 fs-5 fw-bold bg-lighten">
                                <tr>
                                    <th>Tanggal Berlaku</th>
                                    <th>Status</th>
                                    <th>Struktur</th>
                                    <th class="text-end min-w-70px">Action</th>
                                </tr>
                            </thead>
                            <!--end::Thead-->
                            <!--begin::Tbody-->
                            <tbody class="fw-6 fw-bold text-gray-600">
                                <tr v-cloak v-for="(career, index) in careers">
                                    <td>
                                        <span v-if="career.active" class="badge badge-light-success">Aktif</span>
                                        <span>@{{ career.effective_date }}</span>
                                    </td>
                                    <td>
                                        <span class="text-capitalize">Pegawai @{{ career.status }}</span>
                                    </td>
                                    <td>
                                        <span>@{{ career?.job_title?.designation?.department?.name }} - @{{ career?.job_title?.designation?.name }} - @{{ career?.job_title?.name }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end">
                                            <!--begin::Share link-->
                                            @can('updateCareer', App\Models\Employee::class)
                                            <button v-if="career?.active == 1" type="button" class="btn btn-sm btn-icon btn-light-info ms-2" data-bs-toggle="modal" data-bs-target="#career_edit_modal" @click="openEditModal(career.id, index)">
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
                                            @can('deleteCareer', App\Models\Employee::class)
                                            <button v-if="career?.active == 0" type="button" class="btn btn-sm btn-icon btn-light-danger ms-2" @click="openDeleteConfirmation(career.id)">
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
                                </tr>
                            </tbody>
                            <!--end::Tbody-->
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Table wrapper-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Chart widget 12-->
        </div>
        <!--end::Col-->
    </div>
    <!--begin::Modal - Career - Add-->
    <div class="modal fade" id="career_add_modal" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Form-->
                <form class="form" action="#" id="career_add_modal_form">
                    <!--begin::Modal header-->
                    <div class="modal-header" id="career_add_modal_header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bolder">Tambah Karir Pegawai</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div id="career_add_modal_close" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
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
                        <div class="scroll-y me-n7 pe-7" id="career_add_modal_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#career_add_modal_header" data-kt-scroll-wrappers="#career_add_modal_scroll" data-kt-scroll-offset="300px">
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Tanggal Berlaku</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="date" v-model="model.add.effectiveDate" class="form-control form-control-solid" id="effective-date-input" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Status</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="model.add.status" class="form-select form-select-solid">
                                    <option value="tetap">Pegawai Tetap</option>
                                    <option value="tidak tetap">Pegawai Tidak Tetap</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Departemen</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="model.add.departmentId" class="form-select form-select-solid">
                                    <option value="">Pilih Departemen</option>
                                    <option v-for="department in departments" :value="department.id">@{{ department.name }}</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Bagian</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="model.add.designationId" class="form-select form-select-solid">
                                    <option value="">Pilih Departemen</option>
                                    <option v-for="designation in filteredAddDesignations" :value="designation.id">@{{ designation.name }}</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Job Title</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="model.add.jobTitleId" class="form-select form-select-solid">
                                    <option value="">Pilih Job Title</option>
                                    <option v-for="jobTitle in filteredAddJobTitles" :value="jobTitle.id">@{{ jobTitle.name }}</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Scroll-->
                    </div>
                    <!--end::Modal body-->
                    <!--begin::Modal footer-->
                    <div class="modal-footer flex-center">
                        <!--begin::Button-->
                        <button type="reset" id="career_add_modal_cancel" class="btn btn-light me-3">Batal</button>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="button" id="career_add_modal_submit" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-primary" @click="onSubmit">
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
    <!--end::Modal - Career - Add-->
    <!--begin::Modal - Career - Edit-->
    <div class="modal fade" id="career_edit_modal" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Form-->
                <form class="form" action="#" id="career_edit_modal_form">
                    <!--begin::Modal header-->
                    <div class="modal-header" id="career_edit_modal_header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bolder">Ubah Karir Pegawai</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div id="career_edit_modal_close" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
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
                        <div class="scroll-y me-n7 pe-7" id="career_edit_modal_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#career_edit_modal_header" data-kt-scroll-wrappers="#career_edit_modal_scroll" data-kt-scroll-offset="300px">
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Tanggal Berlaku</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="date" v-model="model.edit.effectiveDate" class="form-control form-control-solid" id="effective-date-input" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Status</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="model.edit.status" class="form-select form-select-solid">
                                    <option value="tetap">Pegawai Tetap</option>
                                    <option value="tidak tetap">Pegawai Tidak Tetap</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Departemen</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="model.edit.departmentId" class="form-select form-select-solid">
                                    <option value="">Pilih Departemen</option>
                                    <option v-for="department in departments" :value="department.id">@{{ department.name }}</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Bagian</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="model.edit.designationId" class="form-select form-select-solid">
                                    <option value="">Pilih Departemen</option>
                                    <option v-for="designation in filteredEditDesignations" :value="designation.id">@{{ designation.name }}</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-bold mb-2">Job Title</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="model.edit.jobTitleId" class="form-select form-select-solid">
                                    <option value="">Pilih Job Title</option>
                                    <option v-for="jobTitle in filteredEditJobTitles" :value="jobTitle.id">@{{ jobTitle.name }}</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Scroll-->
                    </div>
                    <!--end::Modal body-->
                    <!--begin::Modal footer-->
                    <div class="modal-footer flex-center">
                        <!--begin::Button-->
                        <button type="reset" id="career_edit_modal_cancel" class="btn btn-light me-3">Batal</button>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="button" id="career_edit_modal_submit" class="btn btn-primary" :data-kt-indicator="submitLoading == true ? 'on' : 'null'" @click="onSubmitEdit">
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
    <!--end::Modal - Career - Add-->
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection

@section('pagescript')
<script>
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

    const closeModal = (selector) => {
        const modalElement = document.querySelector(selector);
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();
    }

    const departments = <?php echo Illuminate\Support\Js::from($departments) ?>;
    const designations = <?php echo Illuminate\Support\Js::from($designations) ?>;
    const jobTitles = <?php echo Illuminate\Support\Js::from($jobTitles) ?>;
    const careers = <?php echo Illuminate\Support\Js::from($employee->careers) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                careers,
                departments,
                designations,
                jobTitles,
                lastDateCareer: '{{ $last_date_career }}',
                model: {
                    add: {
                        effectiveDate: '',
                        status: 'tetap',
                        jobTitleId: '',
                        departmentId: '',
                        designationId: '',
                        employeeId: '{{ $employee->id }}'
                    },
                    edit: {
                        index: null,
                        id: null,
                        effectiveDate: '',
                        status: '',
                        jobTitleId: '',
                        departmentId: '',
                        designationId: '',
                        employeeId: '{{ $employee->id }}'
                    }
                },
                submitLoading: false,
            }
        },
        computed: {
            filteredAddDesignations() {
                const {
                    departmentId
                } = this.model.add;
                if (departmentId) {
                    return this.designations.filter(designation => designation.department_id === Number(departmentId))
                }

                return [];
            },
            filteredAddJobTitles() {
                const {
                    designationId
                } = this.model.add;
                if (designationId) {
                    return this.jobTitles.filter(jobTitle => jobTitle.designation_id === Number(designationId))
                }

                return [];
            },
            filteredEditDesignations() {
                const {
                    departmentId
                } = this.model.edit;
                if (departmentId) {
                    return this.designations.filter(designation => designation.department_id === Number(departmentId))
                }

                return [];
            },
            filteredEditJobTitles() {
                const {
                    designationId
                } = this.model.edit;
                if (designationId) {
                    return this.jobTitles.filter(jobTitle => jobTitle.designation_id === Number(designationId))
                }

                return [];
            },
        },
        methods: {
            async onSubmit() {
                let self = this;
                try {
                    const {
                        effectiveDate,
                        status,
                        jobTitleId,
                        departmentId,
                        designationId,
                        employeeId,
                    } = self.model.add;

                    const lastDateCareer = self.lastDateCareer;

                    self.submitLoading = true;

                    const response = await axios.post('/careers', {
                        status,
                        effective_date: effectiveDate,
                        job_title_id: jobTitleId,
                        employee_id: employeeId,
                        last_date_career: lastDateCareer,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data?.career;
                        const careers = response?.data?.data?.careers

                        this.replace(careers);
                        closeModal('#career_add_modal');
                        this.resetForm();
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
            add(data) {
                if (data) {
                    this.careers.push(data)
                }
            },
            replace(data) {
                if (data) {
                    this.careers = data;
                }
            },
            resetForm() {
                this.model.add.effectiveDate = '';
                this.model.add.status = 'tetap';
                this.model.add.jobTitleId = '';
                this.model.add.departmentId = '';
                this.model.add.designationId = '';
            },
            openEditModal(id, index) {
                const [career] = this.careers.filter(career => career.id === Number(id));
                console.log(career, id, index)
                if (career) {
                    this.model.edit.index = index
                    this.model.edit.id = career.id
                    this.model.edit.status = career.status
                    this.model.edit.effectiveDate = career.effective_date
                    this.model.edit.departmentId = career?.job_title?.designation?.department?.id
                    this.model.edit.designationId = career?.job_title?.designation?.id
                    this.model.edit.jobTitleId = career?.job_title?.id

                    console.log(career?.job_title?.designation?.department?.id, career?.job_title?.designation?.id, career?.job_title?.id)
                }
            },
            async onSubmitEdit() {
                let self = this;
                try {
                    const {
                        effectiveDate,
                        status,
                        jobTitleId,
                        departmentId,
                        designationId,
                        employeeId,
                        id,
                        index,
                    } = self.model.edit;

                    const lastDateCareer = self.lastDateCareer;

                    self.submitLoading = true;

                    const response = await axios.post(`/careers/${id}`, {
                        status,
                        effective_date: effectiveDate,
                        job_title_id: jobTitleId,
                        employee_id: employeeId,
                        last_date_career: lastDateCareer,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data?.career;
                        const careers = response?.data?.data?.careers

                        this.edit(index, data);
                        closeModal('#career_edit_modal');
                        // this.resetForm();
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
            edit(index, data) {
                this.careers.splice(index, 1, data);
            },
            openDeleteConfirmation(id) {
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
                        return self.sendDeleteRequest(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            sendDeleteRequest(id) {
                const self = this;
                return axios.delete('/careers/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        self.delete(id);
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
            delete(id) {
                this.careers = this.careers.filter(career => career.id !== id);
            },
        },
        watch: {
            'model.add.departmentId': function(val) {
                this.model.add.designationId = null;
            },
            'model.add.designationId': function(val) {
                this.model.add.jobTitleId = null;
            },
            // 'model.edit.departmentId': function(val) {
            //     this.model.edit.designationId = null;
            // },
            // 'model.edit.designationId': function(val) {
            //     this.model.edit.jobTitleId = null;
            // },
        }
    })
</script>
<script src="{{ asset('assets/js/addons/employeeActivation.js') }}"></script>
<script>
    $(function() {
        // $('#effective-date-input').daterangepicker({
        //     singleDatePicker: true,
        //     showDropdowns: true,
        //     minDate: '{{ $last_date_career }}' || undefined,
        // }, function(start, end, label) {
        //     var years = moment().diff(start, 'years');
        //     alert("You are " + years + " years old!");
        // });
    });
</script>
@endsection