@extends('layouts.app')

@section('title', 'Shift Jadwal Kerja')

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
            <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_4">Shift</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_5">Link 2</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_6">Link 3</a>
        </li>
    </ul> -->
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
                    <input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Shift" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end align-items-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_division">Tambah Shift</button>
                </div>
                <!--end::Toolbar-->
                <!--begin::Group actions-->
                <div class="d-flex justify-content-end align-items-center d-none" data-kt-customer-table-toolbar="selected">
                    <div class="fw-bolder me-5">
                        <span class="me-2" data-kt-customer-table-select="selected_count"></span>Selected
                    </div>
                    <button type="button" class="btn btn-danger" data-kt-customer-table-select="delete_selected">Delete Selected</button>
                </div>
                <!--end::Group actions-->
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6" id="kt_customers_table">
                <!--begin::Table head-->
                <thead class="bg-light-primary">
                    <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                        <th>Nama</th>
                        <th class="text-center">Jam Masuk</th>
                        <th class="text-center">Jam Keluar</th>
                        <th class="text-center">Warna</th>
                        <th class="text-center">Hitung Lembur</th>
                        <th class="text-center">Jam Mulai Lembur</th>
                        <th class="text-end min-w-70px pe-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="fw-bold text-gray-600">
                    <tr v-for="(workScheduleWorkingPattern, index) in workScheduleWorkingPatterns" :key="workScheduleWorkingPattern.id">
                        <td class="ps-2">
                            <span class="text-gray-800">@{{ workScheduleWorkingPattern.name }}</span>
                        </td>
                        <td class="text-center">
                            <span class="text-gray-800">@{{ workScheduleWorkingPattern.start_time }}</span>
                        </td>
                        <td class="text-center">
                            <span class="text-gray-800">@{{ workScheduleWorkingPattern.end_time }}</span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <div :style="`width: 50px; height: 50px; border-radius: 100%; background-color: ${workScheduleWorkingPattern.color}`">

                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <i v-if="workScheduleWorkingPattern.have_overtime == 1" class="text-success bi bi-check-circle-fill fs-1"></i>
                        </td>
                        <td class="text-center">
                            <span v-if="workScheduleWorkingPattern.have_overtime == 1" class="text-gray-800">@{{ workScheduleWorkingPattern.overtime_start_time }}</span>
                        </td>
                        <td class="text-end pe-2">
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-sm btn-icon btn-light-info ms-2" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_division" @click="openEditModal(workScheduleWorkingPattern.id, index)">
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                        </svg>
                                    </span>
                                </button>
                                <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2" @click="openDeleteConfirmation(workScheduleWorkingPattern.id)">
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
                        <h2 class="fw-bolder">Tambah Shift</h2>
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
                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-bold mb-2">Nama</label>
                                <input type="text" v-model="model.add.name" class="form-control" placeholder="Masukkan nama shift" />
                            </div>
                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-bold mb-2">Jam Masuk</label>
                                <input type="time" v-model="model.add.startTime" class="form-control" />
                            </div>
                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-bold mb-2">Jam Keluar</label>
                                <input type="time" v-model="model.add.endTime" class="form-control" />
                            </div>
                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-bold mb-2">Warna</label>
                                <input type="color" v-model="model.add.color" class="form-control" style="height: 50px;" />
                            </div>
                            <div class="fv-row mb-7">
                                <div class="form-check">
                                    <input class="form-check-input" v-model="model.add.haveOvertime" type="checkbox" id="addHasOvertimeCheckbox" />
                                    <label class="form-check-label" for="addHasOvertimeCheckbox">
                                        Hitung Lembur
                                    </label>
                                </div>
                            </div>
                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-bold mb-2">Jam Mulai Lembur</label>
                                <input type="time" v-model="model.add.overtimeStartTime" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="reset" id="kt_modal_add_division_cancel" class="btn btn-light me-3">Batal</button>
                        <button type="button" id="kt_modal_add_division_submit" :data-kt-indicator="loading ? 'on' : null" class="btn btn-primary" @click="onSubmit">
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
                        <h2 class="fw-bolder">Ubah Shift</h2>
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
                        <div class="scroll-y me-n7 pe-7" id="kt_modal_add_division_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_division_header" data-kt-scroll-wrappers="#kt_modal_add_division_scroll" data-kt-scroll-offset="300px">
                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-bold mb-2">Nama</label>
                                <input type="text" v-model="model.edit.name" class="form-control" placeholder="Masukkan nama shift" />
                            </div>
                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-bold mb-2">Jam Masuk</label>
                                <input type="time" v-model="model.edit.startTime" class="form-control" />
                            </div>
                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-bold mb-2">Jam Keluar</label>
                                <input type="time" v-model="model.edit.endTime" class="form-control" />
                            </div>
                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-bold mb-2">Warna</label>
                                <input type="color" v-model="model.edit.color" class="form-control" style="height: 50px;" />
                            </div>
                            <div class="fv-row mb-7">
                                <div class="form-check">
                                    <input class="form-check-input" v-model="model.edit.haveOvertime" type="checkbox" id="editHasOvertimeCheckbox" />
                                    <label class="form-check-label" for="editHasOvertimeCheckbox">
                                        Hitung Lembur
                                    </label>
                                </div>
                            </div>
                            <div class="fv-row mb-7">
                                <label class="required fs-6 fw-bold mb-2">Jam Mulai Lembur</label>
                                <input type="time" v-model="model.edit.overtimeStartTime" class="form-control" />
                            </div>
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
                        <button type="button" id="kt_modal_edit_division_submit" :data-kt-indicator="loading ? 'on' : null" class="btn btn-primary" @click="onSubmitEdit">
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


        datatable = $('#kt_customers_table').DataTable({
            "order": false,
            "columnDefs": [{
                "targets": 3,
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

    const closeModal = (selector) => {
        if (selector) {
            const element = document.querySelector(selector);
            const modal = bootstrap.Modal.getInstance(element);
            modal.hide();
        }
    }

    // const closeDivisionAddModal = () => {
    //     const addDivisionModal = document.querySelector('#kt_modal_add_division');
    //     const modal = bootstrap.Modal.getInstance(addDivisionModal);
    //     modal.hide();
    // }

    // const closeDivisionEditModal = () => {
    //     const addDivisionModal = document.querySelector('#kt_modal_edit_division');
    //     const modal = bootstrap.Modal.getInstance(addDivisionModal);
    //     modal.hide();
    // }

    const divisions = [];
    const companies = [];
    const workScheduleWorkingPatterns = <?php echo Illuminate\Support\Js::from($work_schedule_working_patterns) ?>;

    Vue.prototype.moment = moment;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                workScheduleWorkingPatterns,
                model: {
                    add: {
                        name: '',
                        startTime: '',
                        endTime: '',
                        haveOvertime: false,
                        color: '#000000',
                        overtimeStartTime: '',
                    },
                    edit: {
                        index: null,
                        id: null,
                        name: '',
                        startTime: '',
                        endTime: '',
                        haveOvertime: false,
                        color: '',
                        overtimeStartTime: '',
                    },
                },
                loading: false,
                year: '{{ request()->query("year") ?? date("Y") }}'
            }
        },
        methods: {
            // COMPANY METHODS
            async onSubmit() {
                let self = this;
                try {
                    const {
                        name,
                        startTime,
                        endTime,
                        haveOvertime,
                        color,
                        overtimeStartTime,
                    } = self.model.add;

                    self.loading = true;

                    const response = await axios.post('/work-schedule-working-patterns', {
                        name,
                        start_time: startTime,
                        end_time: endTime,
                        have_overtime: haveOvertime ? 1 : 0,
                        color,
                        overtime_start_time: overtimeStartTime,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        this.addData(data);
                        // closeDivisionAddModal();
                        closeModal('#kt_modal_add_division')
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
                    self.loading = false;
                }
            },
            addData(data) {
                if (data) {
                    this.workScheduleWorkingPatterns.push(data)
                }
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
                return axios.delete('/work-schedule-working-patterns/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        self.deleteData(id);
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
            deleteData(id) {
                this.workScheduleWorkingPatterns = this.workScheduleWorkingPatterns.filter(workScheduleWorkingPattern => workScheduleWorkingPattern.id !== id);
            },
            openEditModal(id, index) {
                const [workScheduleWorkingPattern] = this.workScheduleWorkingPatterns.filter(workScheduleWorkingPattern => workScheduleWorkingPattern.id === id);
                if (workScheduleWorkingPattern) {
                    this.model.edit.index = index;
                    this.model.edit.id = workScheduleWorkingPattern.id;
                    this.model.edit.name = workScheduleWorkingPattern.name;
                    this.model.edit.startTime = workScheduleWorkingPattern.start_time;
                    this.model.edit.endTime = workScheduleWorkingPattern.end_time;
                    this.model.edit.color = workScheduleWorkingPattern.color;
                    this.model.edit.haveOvertime = workScheduleWorkingPattern.have_overtime;
                    this.model.edit.overtimeStartTime = workScheduleWorkingPattern.overtime_start_time;
                }
            },
            async onSubmitEdit() {
                let self = this;
                try {
                    const {
                        index,
                        id,
                        name,
                        startTime,
                        endTime,
                        haveOvertime,
                        color,
                        overtimeStartTime,
                    } = self.model.edit;

                    self.loading = true;

                    const response = await axios.post(`/work-schedule-working-patterns/${id}`, {
                        name,
                        start_time: startTime,
                        end_time: endTime,
                        color,
                        have_overtime: haveOvertime,
                        overtime_start_time: overtimeStartTime,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        this.editData(index, data);
                        closeModal('#kt_modal_edit_division')
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
                    self.loading = false;
                }
            },
            editData(index, data) {
                this.workScheduleWorkingPatterns.splice(index, 1, data);
            },
            resetForm() {
                this.divisionModel = {
                    add: {
                        name: '',
                        startTime: '',
                        endTime: '',
                        haveOvertime: false,
                        color: '#000000',
                        overtimeStartTime: '',
                    },
                    edit: {
                        index: null,
                        id: null,
                        name: '',
                        startTime: '',
                        endTime: '',
                        haveOvertime: false,
                        color: '#000000',
                        overtimeStartTime: '',
                    },
                }
            },
            applyFilter() {
                const {
                    year
                } = this;
                document.location.href = "/work-schedule-working-patterns?year=" + year;
            }
        },
    })
</script>
@endsection