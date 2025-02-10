@extends('layouts.app')

@section('title', $employee->name . ' - Pinjaman')

@section('head')

@endsection

@section('content')
<?php
function statusColor($status)
{
    switch ($status) {
        case 'approved':
            return 'light-success';
        case 'pending':
            return 'light-warning';
        case 'rejected':
            return 'light-danger';
        default:
            return 'light';
    }
}
?>
<div id="kt_content_container" class="container-xxl">
    <x-employee-detail-card :employee="$employee" />
    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
        <!--begin::Card header-->
        <div class="card-header card-header-stretch">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <h3 class="m-0 text-gray-900">Time Off</h3>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <ul class="nav nav-tabs nav-line-tabs nav-stretch border-transparent fs-5 fw-bolder" id="kt_security_summary_tabs">
                    <li class="nav-item">
                        <a class="nav-link text-active-primary active" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#sick-tab">Sakit</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#permission-tab">Izin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#leave-tab">Cuti</a>
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
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="sick_applications_table">
                        <!--begin::Table head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th>Nama Pegawai</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Tanggal Sakit</th>
                                <th>Status</th>
                                <th class="text-end min-w-70px">Actions</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="fw-bold text-gray-600">
                            @foreach($sick_applications as $sick_application)
                            <tr>
                                @if(isset($sick_application->employee->name))
                                <td class="text-gray-800">{{ $sick_application->employee->name }}</td>
                                @else
                                <td></td>
                                @endif
                                <td>{{ $sick_application->date }}</td>
                                <td>{{ $sick_application->application_dates }}</td>
                                <td>
                                    <span class="badge badge-{{ statusColor($sick_application->approval_status) }} text-uppercase">
                                        {{$sick_application->approval_status}}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end">
                                        @if($sick_application->approval_status == 'pending')
                                        <!--begin::Share link-->
                                        <a href="/time-offs/{{ $sick_application->id }}/edit?type=sakit" class="btn btn-sm btn-icon btn-light-info ms-2">
                                            <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                            <span class="svg-icon svg-icon-5 m-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </a>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2" @click="openDeleteConfirmation({{ $sick_application->id }}, 'sakit', '{{$sick_application->approval_status}}')">
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
                            </tr>
                            @endforeach
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <div class="tab-pane fade" id="permission-tab" role="tab-panel">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="permission_applications_table">
                        <!--begin::Table head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th>Nama Pegawai</th>
                                <th>Kategori</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Tanggal Izin</th>
                                <th>Status</th>
                                <th class="text-end min-w-70px">Actions</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="fw-bold text-gray-600">
                            @foreach($permission_applications as $permission_application)
                            <tr>
                                @if(isset($permission_application->employee->name))
                                <td class="text-gray-800">{{ $permission_application->employee->name }}</td>
                                @else
                                <td></td>
                                @endif
                                @if(isset($permission_application->category->name))
                                <td class="text-gray-800">{{ $permission_application->category->name }}</td>
                                @else
                                <td></td>
                                @endif
                                <td>{{ $permission_application->date }}</td>
                                <td>{{ $permission_application->application_dates }}</td>
                                <td>
                                    <span class="badge badge-{{ statusColor($permission_application->approval_status) }} text-uppercase">
                                        {{$permission_application->approval_status}}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end">
                                        @if($permission_application->approval_status == 'pending')
                                        <!--begin::Share link-->
                                        <a href="/time-offs/{{ $permission_application->id }}/edit?type=izin" class="btn btn-sm btn-icon btn-light-info ms-2">
                                            <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                            <span class="svg-icon svg-icon-5 m-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </a>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2" @click="openDeleteConfirmation({{ $permission_application->id }}, 'izin', '{{$permission_application->approval_status}}')">
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
                            </tr>
                            @endforeach
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <div class="tab-pane fade" id="leave-tab" role="tab-panel">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="leave_applications_table">
                        <!--begin::Table head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th>Nama Pegawai</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Tanggal Cuti</th>
                                <th>Status</th>
                                <th class="text-end min-w-70px">Actions</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="fw-bold text-gray-600">
                            @foreach($leave_applications as $leave_application)
                            <tr>
                                @if(isset($leave_application->employee->name))
                                <td class="text-gray-800">{{ $leave_application->employee->name }}</td>
                                @else
                                <td></td>
                                @endif
                                <td>{{ $leave_application->date }}</td>
                                <td>{{ implode(', ', explode(',', $leave_application->application_dates)) }}</td>
                                <td>
                                    <span class="badge badge-{{ statusColor($leave_application->approval_status) }} text-uppercase">
                                        {{$leave_application->approval_status}}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end">
                                        @if($leave_application->approval_status == 'pending')
                                        <!--begin::Share link-->
                                        <a href="/time-offs/{{ $leave_application->id }}/edit?type=cuti" class="btn btn-sm btn-icon btn-light-info ms-2">
                                            <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                            <span class="svg-icon svg-icon-5 m-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </a>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2" @click="openDeleteConfirmation({{ $leave_application->id }}, 'cuti', '{{$leave_application->approval_status}}')">
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
                            </tr>
                            @endforeach
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
            </div>
        </div>
        <!--end::Card body-->
    </div>
</div>
@endsection
@section('script')
<!-- <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script> -->
<!-- <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script> -->
<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script> -->
@endsection
@section('pagescript')
<script>
    moment.locale('id');
    const closeModal = (selector) => {
        const element = document.querySelector(selector);
        const modal = bootstrap.Modal.getInstance(element);
        modal.hide();
    }

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

        const datatableOptions = {
            "columnDefs": [{
                "targets": 3,
                // "searchable": false
                "className": "text-end",
            }],
            // "drawCallback": function() {
            //     console.log('redraw table...')
            // },
            "language": {
                "infoEmpty": " ",
                "zeroRecords": " "
            }
        }

        sickDatatable = $('#sick_applications_table').DataTable(datatableOptions);
        permissionDatatable = $('#permission_applications_table').DataTable(datatableOptions);
        leaveDatatable = $('#leave_applications_table').DataTable(datatableOptions);

        // const handleSearchDatatable = () => {
        // const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        // filterSearch.addEventListener('keyup', function(e) {
        //     datatable.search(e.target.value).draw();
        // });
    })
</script>
<script src="{{ asset('assets/js/addons/employeeActivation.js') }}"></script>
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
            }
        },
        methods: {
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
</script>
<script>
    const bgColor = {
        primary: KTUtil.getCssVariableValue('--bs-primary'),
        success: KTUtil.getCssVariableValue('--bs-success'),
        danger: KTUtil.getCssVariableValue('--bs-danger'),
        info: KTUtil.getCssVariableValue('--bs-info'),
        warning: KTUtil.getCssVariableValue('--bs-warning'),
        light: KTUtil.getCssVariableValue('--bs-dark'),
    }
</script>
@endsection