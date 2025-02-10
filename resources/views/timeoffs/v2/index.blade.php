@extends('layouts.app')

@section('title', 'Time Off')

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
    @php
    $canAddTimeOff = Auth::user()->can('create', App\Models\SickApplication::class) || Auth::user()->can('create', App\Models\LeaveApplication::class)
    @endphp
    <div class="d-flex justify-content-end mb-5">
        <div class="me-3">
            <button class="btn btn-secondary position-relative" id="btn-filter">
                <i class="bi bi-funnel"></i>
                <span>Filter</span>
                <!-- <span class="position-absolute top-0 start-100 translate-middle  badge badge-circle badge-warning" style="width: 0.75rem; height: 0.75rem"></span> -->
            </button>
        </div>
        @if ($canAddTimeOff)
        <div>
            <a href="/time-offs/create" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Time Off</a>
        </div>
        @endif
    </div>
    <!-- <div class="text-end">
        <a href="/time-offs/create" class="btn btn-primary">Tambah Time Off</a>
    </div> -->
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header card-header-stretch">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <h3 class="m-0 text-gray-900">Daftar Time Off</h3>

                <!--end::Search-->
            </div>
            <!--begin::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!-- <a href="/time-offs/create" class="btn btn-primary">Tambah Time Off</a> -->
                <ul class="nav nav-tabs nav-line-tabs nav-stretch border-transparent fs-5 fw-bolder" id="kt_security_summary_tabs">
                    @can('view', App\Models\SickApplication::class)
                    <li class="nav-item">
                        <a class="nav-link text-active-primary active" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#sick-tab">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-pulse-fill" viewBox="0 0 16 16">
                                <path d="M1.475 9C2.702 10.84 4.779 12.871 8 15c3.221-2.129 5.298-4.16 6.525-6H12a.5.5 0 0 1-.464-.314l-1.457-3.642-1.598 5.593a.5.5 0 0 1-.945.049L5.889 6.568l-1.473 2.21A.5.5 0 0 1 4 9z" />
                                <path d="M.88 8C-2.427 1.68 4.41-2 7.823 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C11.59-2 18.426 1.68 15.12 8h-2.783l-1.874-4.686a.5.5 0 0 0-.945.049L7.921 8.956 6.464 5.314a.5.5 0 0 0-.88-.091L3.732 8z" />
                            </svg>
                            <span class="ms-2">
                                <span>Sakit</span>
                                @if($pending_sick_applications_count > 0)
                                <span class="badge badge-warning ms-2">{{ $pending_sick_applications_count }}</span>
                                @endif
                            </span>
                        </a>
                    </li>
                    @endif
                    <!-- <li class="nav-item">
                        <a class="nav-link text-active-primary" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#permission-tab">Izin</a>
                    </li> -->
                    @can('view', App\Models\LeaveApplication::class)
                    <li class="nav-item">
                        <a class="nav-link text-active-primary" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#leave-tab">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-briefcase-fill" viewBox="0 0 16 16">
                                <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v1.384l7.614 2.03a1.5 1.5 0 0 0 .772 0L16 5.884V4.5A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5" />
                                <path d="M0 12.5A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5V6.85L8.129 8.947a.5.5 0 0 1-.258 0L0 6.85z" />
                            </svg>
                            <span class="ms-2">
                                <span>Cuti</span>
                                @if($pending_leave_applications_count > 0)
                                <span class="badge badge-warning ms-2">{{ $pending_leave_applications_count }}</span>
                                @endif
                            </span>
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link text-active-primary" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#event-leave-tab">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16">
                                <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z" />
                                <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z" />
                            </svg>
                            <span class="ms-2">
                                <span>Off Event</span>
                                @if($pending_event_leave_applications_count > 0)
                                <span class="badge badge-warning ms-2">{{ $pending_event_leave_applications_count }}</span>
                                @endif
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade active show" id="sick-tab" role="tab-panel">
                    <!--begin::Table-->
                    <div class="d-flex align-items-center position-relative mb-5">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <input type="text" data-kt-sick-application-table-filter="search" class="form-control form-control-sm w-250px ps-15" placeholder="Cari Pengajuan Sakit" />
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-7" id="sick_applications_table">
                            <!--begin::Table head-->
                            <thead class="bg-light-primary align-middle">
                                <!--begin::Table row-->
                                <tr class="text-center text-gray-800 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="ps-2">Nama Pegawai</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Tanggal Sakit</th>
                                    <th>Alasan</th>
                                    <th>Lampiran</th>
                                    <th>Status</th>
                                    <th class="text-end min-w-70px pe-2">Actions</th>
                                </tr>
                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="fw-bold text-gray-700">
                            </tbody>
                            <!--end::Table body-->
                        </table>
                    </div>
                    <!--end::Table-->
                </div>
                <div class="tab-pane fade" id="leave-tab" role="tab-panel">
                    <!--begin::Table-->
                    <div class="d-flex align-items-center position-relative mb-5">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <input type="text" data-kt-leave-application-table-filter="search" class="form-control form-control-sm w-250px ps-15" placeholder="Cari Pengajuan Cuti" />
                    </div>
                    <table class="table align-middle table-row-dashed fs-7" id="leave_applications_table">
                        <!--begin::Table head-->
                        <thead class="bg-light-primary align-middle">
                            <!--begin::Table row-->
                            <tr class="text-center text-gray-800 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="ps-2">Nama Pegawai</th>
                                <th>Jenis Cuti</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Tanggal Cuti</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th class="text-end min-w-70px pe-2">Actions</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="fw-bold text-gray-700">
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <div class="tab-pane fade" id="event-leave-tab" role="tab-panel">
                    <!--begin::Table-->
                    <div class="d-flex align-items-center position-relative mb-5">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <input type="text" data-kt-event-leave-application-table-filter="search" class="form-control form-control-sm w-250px ps-15" placeholder="Cari Off Event" />
                    </div>
                    <table class="table align-middle table-row-dashed fs-7" id="event_leave_applications_table">
                        <!--begin::Table head-->
                        <thead class="bg-light-primary align-middle">
                            <!--begin::Table row-->
                            <tr class="text-center text-gray-800 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="ps-2">Nama Pegawai</th>
                                <th>Jenis Cuti</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Tanggal Cuti</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th class="text-end min-w-70px pe-2">Actions</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="fw-bold text-gray-700">

                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
    <div id="drawer_filter" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true" data-kt-drawer-toggle="#btn-filter" data-kt-drawer-close="#drawer_filter_basic_close" data-kt-drawer-width="400px">

        <!--begin::Card-->
        <div class="card rounded-0 w-100">
            <!--begin::Card header-->
            <div class="card-header pe-5">
                <!--begin::Title-->
                <div class="card-title">
                    <!--begin::User-->
                    <div class="d-flex justify-content-center flex-column me-3">
                        <span class="fs-4 fw-bold text-gray-900 text-hover-primary me-1 lh-1">
                            <i class="bi bi-funnel-fill me-2 text-dark"></i>
                            <span>Filter</span>
                        </span>
                    </div>
                    <!--end::User-->
                </div>
                <!--end::Title-->

                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-light-primary" id="drawer_filter_dismiss_close">
                        <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body hover-scroll-overlay-y">
                <div class="mb-10">
                    <label class="form-label fs-7">Perusahaan</label>
                    <select v-model="model.filter.companyId" name="company_id" class="form-select form-select-sm">
                        <option value="">Semua Perusahaan</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- <div class="mb-10">
                    <label class="form-label fs-7">Tanggal Pengajuan</label>
                    <div class="form-check form-switch mt-3 mb-5">
                        <input class="form-check-input" type="checkbox" v-model="model.filter.isAllDate" role="switch" id="switchFilterDate">
                        <label class="form-check-label fs-7" for="switchFilterDate">Semua Tanggal</label>
                    </div>
                    <div :class="!model.filter.isAllDate ? 'd-block' : 'd-none'">
                        <input type="text" class="form-control form-control-sm" id="filter-date">
                    </div>
                </div>
                <div class="mb-10">
                    <label for="" class="form-label fs-7">Tanggal Izin</label>
                    <div class="form-check form-switch mt-3 mb-5">
                        <input class="form-check-input" type="checkbox" v-model="model.filter.isAllApplicationDate" role="switch" id="switchFilterApplicationDate">
                        <label class="form-check-label fs-7" for="switchFilterApplicationDate">Semua Tanggal</label>
                    </div>
                    <div :class="!model.filter.isAllApplicationDate ? 'd-block' : 'd-none'">
                        <input type="text" class="form-control form-control-sm" id="filter-application-date">
                    </div>
                </div> -->
                <div class="mb-10">
                    <label for="" class="form-label fs-7">Status</label>
                    <select v-model="model.filter.status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>
            <!--end::Card body-->

            <!--begin::Card footer-->
            <div class="card-footer text-end">
                <!--begin::Dismiss button-->
                <button class="btn btn-secondary me-3" data-kt-drawer-dismiss="true">Tutup</button>
                <button class="btn btn-primary" id="btn-apply-filter">Terapkan</button>
                <!--end::Dismiss button-->
            </div>
            <!--end::Card footer-->
        </div>
        <!--end::Card-->
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



    })
</script>
<script>
    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                sick: {
                    loading: false,
                },
                permission: {
                    loading: false,
                },
                leave: {
                    loading: false,
                },
                model: {
                    filter: {
                        companyId: '',
                        isAllDate: true,
                        date: '',
                        isAllApplicationDate: true,
                        applicationDate: '',
                        status: '',
                    }
                }
            }
        },
        methods: {
            // COMPANY METHODS
            openApprovalConfirmation(id, applicationType, type) {
                const self = this;
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Pengajuan akan " + (type == 'approve' ? 'Disetujui' : 'Ditolak'),
                    icon: 'warning',
                    reverseButtons: true,
                    showCancelButton: true,
                    confirmButtonText: (type == 'approve' ? 'Setujui' : 'Tolak'),
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: "btn " + (type == 'approve' ? 'btn-success' : 'btn-danger'),
                        cancelButton: "btn btn-light"
                    },
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return self.sendConfirmationRequest(id, applicationType, type);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            // COMPANY METHODS
            openDeleteConfirmation(id, type, status) {
                const self = this;

                let html = '<span>Data akan dihapus</span>';
                if (status == 'approved') {
                    html = `<div class="alert alert-warning" role="alert">
                    Pengajuan ini sudah disetujui. Data yang terhubung seperti absensi akan ikut terhapus
                    </div>`;
                }

                Swal.fire({
                    title: 'Apakah anda yakin?',
                    // text: "Data akan dihapus",
                    html,
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
                        return self.sendDeleteRequest(id, type);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            sendConfirmationRequest(id, applicationType, type) {
                const self = this;
                let url = null;
                if (type == 'approve') {
                    if (applicationType == 'sakit') {
                        url = `/sick-applications/${id}/approve`;
                    } else if (applicationType == 'izin') {
                        url = `/permission-applications/${id}/approve`;
                    } else if (applicationType == 'cuti') {
                        url = `/leave-applications/${id}/approve`;
                    }
                } else if (type == 'reject') {
                    if (applicationType == 'sakit') {
                        url = `/sick-applications/${id}/reject`;
                    } else if (applicationType == 'izin') {
                        url = `/permission-applications/${id}/reject`;
                    } else if (applicationType == 'cuti') {
                        url = `/leave-applications/${id}/reject`;
                    }
                }

                console.log(url);

                if (url) {
                    return axios.post(url)
                        .then(function(response) {
                            let message = response?.data?.message;
                            if (!message) {
                                message = 'Data berhasil ' + type
                            }
                            // self.deleteCompany(id);
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
                }
                return null;
            },
            sendDeleteRequest(id, type) {
                const self = this;
                let url = null;
                if (type == 'sakit') {
                    url = `/sick-applications/${id}`;
                } else if (type == 'izin') {
                    url = `/permission-applications/${id}`;
                } else if (type == 'cuti') {
                    url = `/leave-applications/${id}`;
                }

                if (url) {
                    return axios.delete(url)
                        .then(function(response) {
                            let message = response?.data?.message;
                            if (!message) {
                                message = 'Data berhasil disimpan'
                            }
                            // self.deleteCompany(id);
                            // redrawDatatable();
                            toastr.success(message);
                            document.location.reload();
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
                }
                return null;
            },
            // deleteCompany(id) {
            //     this.companies = this.companies.filter(company => company.id !== id);
            // },
        },
    })

    $(function() {
        const sickDatatable = $('#sick_applications_table').DataTable({
            // autoWidth: false,
            order: false,
            ajax: '/time-offs/datatables/sick-applications',
            processing: true,
            serverSide: true,
            // columnDefs: [{
            //     width: 500,
            //     targets: 0
            // }],
            columns: [{
                    data: 'employee',
                    name: 'employee.name',
                    class: "ps-2",
                    // width: 500,
                },
                {
                    data: 'formatted_date',
                    name: 'date',
                    class: "text-center"
                },
                {
                    data: 'formatted_application_dates',
                    name: 'application_dates',
                    class: "text-center"
                },
                {
                    data: 'note',
                    name: 'note',
                },
                {
                    data: 'formatted_attachment',
                    name: 'attachment',
                },
                {
                    data: 'status',
                    name: 'approval_status',
                    class: "text-center"
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    class: "text-end"
                },
            ],
            "fnDrawCallback": function(oSettings) {
                // console.log(app)
                // app.$forceUpdate();
                // $('[data-toggle="popover"]').popover();
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
                var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl)
                })

                // var popover = new bootstrap.Popover(document.querySelector('.popover-dismiss'), {
                //     trigger: 'focus'
                // });
            }
        });

        const filterSearch = document.querySelector('[data-kt-sick-application-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            sickDatatable.search(e.target.value).draw();
        });


        $('#sick_applications_table').on('click', 'td .btn-delete', function(e) {
            openDeleteConfirmation($(this).attr('data-id'), 'sakit', $(this).attr('data-status'));
        })

        $('#sick_applications_table').on('click', 'td .btn-reject', function(e) {
            openApprovalConfirmation($(this).attr('data-id'), 'sakit', 'reject');
        })

        $('#sick_applications_table').on('click', 'td .btn-approve', function(e) {
            openApprovalConfirmation($(this).attr('data-id'), 'sakit', 'approve');
        })

        const leaveDatatable = $('#leave_applications_table').DataTable({
            order: false,
            ajax: '/time-offs/datatables/leave-applications',
            processing: true,
            serverSide: true,
            columns: [{
                    // data: 'employee.name',
                    // name: 'employee.name',
                    // class: "ps-2"
                    data: 'employee',
                    name: 'employee.name',
                    class: "ps-2",
                },
                {
                    data: 'leave_category_name',
                    name: 'category.name',
                },
                {
                    data: 'formatted_date',
                    name: 'date',
                    class: "text-center"
                },
                {
                    data: 'formatted_application_dates',
                    name: 'application_dates',
                    class: "text-center"
                },
                {
                    data: 'note',
                    name: 'note',
                },
                // {
                //     data: 'formatted_attachment',
                //     name: 'attachment',
                // },
                {
                    data: 'status',
                    name: 'approval_status',
                    class: "text-center"
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    class: "text-end"
                },
            ],
            "fnDrawCallback": function(oSettings) {
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
                var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl)
                })
            }
        });

        $('#leave_applications_table').on('click', 'td .btn-delete', function(e) {
            openDeleteConfirmation($(this).attr('data-id'), 'cuti', $(this).attr('data-status'));
        })

        $('#leave_applications_table').on('click', 'td .btn-reject', function(e) {
            openApprovalConfirmation($(this).attr('data-id'), 'cuti', 'reject');
        })

        $('#leave_applications_table').on('click', 'td .btn-approve', function(e) {
            openApprovalConfirmation($(this).attr('data-id'), 'cuti', 'approve');
        })


        const filterSearch3 = document.querySelector('[data-kt-leave-application-table-filter="search"]');
        filterSearch3.addEventListener('keyup', function(e) {
            leaveDatatable.search(e.target.value).draw();
        });

        const eventLeaveDatatable = $('#event_leave_applications_table').DataTable({
            order: false,
            ajax: '/time-offs/datatables/event-leave-applications',
            processing: true,
            serverSide: true,
            columns: [{
                    data: 'employee',
                    name: 'employee.name',
                    class: "ps-2",
                },
                {
                    data: 'leave_category_name',
                    name: 'category.name',
                },
                {
                    data: 'formatted_date',
                    name: 'date',
                    class: "text-center"
                },
                {
                    data: 'formatted_application_dates',
                    name: 'application_dates',
                    class: "text-center"
                },
                {
                    data: 'note',
                    name: 'note',
                },
                {
                    data: 'status',
                    name: 'approval_status',
                    class: "text-center"
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    class: "text-end"
                },
            ],
            "fnDrawCallback": function(oSettings) {
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
                var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl)
                })
            }
        });

        $('#event_leave_applications_table').on('click', 'td .btn-delete', function(e) {
            openDeleteConfirmation($(this).attr('data-id'), 'cuti', $(this).attr('data-status'));
        })

        $('#event_leave_applications_table').on('click', 'td .btn-reject', function(e) {
            openApprovalConfirmation($(this).attr('data-id'), 'cuti', 'reject');
        })

        $('#event_leave_applications_table').on('click', 'td .btn-approve', function(e) {
            openApprovalConfirmation($(this).attr('data-id'), 'cuti', 'approve');
        })

        const filterSearch4 = document.querySelector('[data-kt-event-leave-application-table-filter="search"]');
        filterSearch4.addEventListener('keyup', function(e) {
            eventLeaveDatatable.search(e.target.value).draw();
        });


        const datatableOptions = {
            "ordering": false,
        }

        // const handleSearchDatatable = () => {
        // const filterSearch2 = document.querySelector('[data-kt-permission-application-table-filter="search"]');
        // filterSearch2.addEventListener('keyup', function(e) {
        //     permissionDatatable.search(e.target.value).draw();
        // });

        function openApprovalConfirmation(id, applicationType, type) {
            // const self = this;
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Pengajuan akan " + (type == 'approve' ? 'Disetujui' : 'Ditolak'),
                icon: 'warning',
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonText: (type == 'approve' ? 'Setujui' : 'Tolak'),
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: "btn " + (type == 'approve' ? 'btn-success' : 'btn-danger'),
                    cancelButton: "btn btn-light"
                },
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return sendConfirmationRequest(id, applicationType, type);
                },
                allowOutsideClick: () => !Swal.isLoading(),
                backdrop: true,
            })
        }

        function sendConfirmationRequest(id, applicationType, type) {
            const self = this;
            let url = null;
            if (type == 'approve') {
                if (applicationType == 'sakit') {
                    url = `/sick-applications/${id}/approve`;
                } else if (applicationType == 'izin') {
                    url = `/permission-applications/${id}/approve`;
                } else if (applicationType == 'cuti') {
                    url = `/leave-applications/${id}/approve`;
                }
            } else if (type == 'reject') {
                if (applicationType == 'sakit') {
                    url = `/sick-applications/${id}/reject`;
                } else if (applicationType == 'izin') {
                    url = `/permission-applications/${id}/reject`;
                } else if (applicationType == 'cuti') {
                    url = `/leave-applications/${id}/reject`;
                }
            }

            console.log(url);

            if (url) {
                return axios.post(url)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil ' + type
                        }
                        toastr.success(message);
                    })
                    .catch(function(error) {
                        console.error(error)
                        let message = error?.response?.data?.message;
                        if (!message) {
                            message = 'Something wrong...'
                        }
                        toastr.error(message);
                    });
            }
            return null;
        }

        function openDeleteConfirmation(id, type, status) {
            let html = '<span>Data akan dihapus</span>';
            if (status == 'approved') {
                html = `<div class="alert alert-warning" role="alert">
                    Pengajuan ini sudah disetujui. Data yang terhubung seperti absensi akan ikut terhapus
                    </div>`;
            }

            Swal.fire({
                title: 'Apakah anda yakin?',
                // text: "Data akan dihapus",
                html,
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
                    return sendDeleteRequest(id, type);
                },
                allowOutsideClick: () => !Swal.isLoading(),
                backdrop: true,
            })
        }

        function sendDeleteRequest(id, type) {
            // const self = this;
            let url = null;
            if (type == 'sakit') {
                url = `/sick-applications/${id}`;
            } else if (type == 'izin') {
                url = `/permission-applications/${id}`;
            } else if (type == 'cuti') {
                url = `/leave-applications/${id}`;
            }

            if (url) {
                return axios.delete(url)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        // self.deleteCompany(id);
                        // redrawDatatable();
                        toastr.success(message);
                        document.location.reload();
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
            }
            return null;
        }

        $('#filter-date').daterangepicker();
        $('#filter-application-date').daterangepicker();
        // $('#filter-select-status').select2();

        $('#btn-apply-filter').on('click', function(e) {
            applyFitler();
            var drawerElement = document.querySelector("#drawer_filter");
            var drawer = KTDrawer.getInstance(drawerElement);
            drawer.hide();
        })

        const applyFitler = () => {
            const queries = [];
            const status = app.$data.model.filter.status;
            const companyId = app.$data.model.filter.companyId;
            if (status) {
                queries.push('status=' + status);
            }
            if (companyId) {
                queries.push('company_id=' + companyId);
            }
            const queryString = queries.join('&');

            const sickApplicationUrl = '/time-offs/datatables/sick-applications?' + queryString;
            const leaveApplicationUrl = '/time-offs/datatables/leave-applications?' + queryString;
            const eventLeaveApplicationUrl = '/time-offs/datatables/event-leave-applications?' + queryString;

            sickDatatable.ajax.url(sickApplicationUrl).load();
            leaveDatatable.ajax.url(leaveApplicationUrl).load();
            eventLeaveDatatable.ajax.url(eventLeaveApplicationUrl).load();
        }
    });

    $('#select-year').on('change', function(e) {
        document.location.href = "/time-offs?year=" + $(this).val();
    })
</script>
@endsection