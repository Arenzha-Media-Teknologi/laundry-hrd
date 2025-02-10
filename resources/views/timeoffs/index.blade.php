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
    @php
    $canAddTimeOff = Auth::user()->can('create', App\Models\SickApplication::class) || Auth::user()->can('create', App\Models\LeaveApplication::class)
    @endphp
    <div class="d-flex justify-content-between align-items-center mb-10">
        <div class="d-flex align-items-center me-3">
            <div class="me-2">Tahun</div>
            <div>
                <select class="form-select w-150px" id="select-year">
                    @for($yearOption = 2023; $yearOption <= date('Y'); $yearOption++) <option value="{{ $yearOption }}" <?= $yearOption == $year ? 'selected' : '' ?>>{{ $yearOption }}</option>
                        @endfor
                </select>
            </div>
        </div>
        @if ($canAddTimeOff)
        <div class="text-end">
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
                <h3 class="m-0 text-gray-900">Time Off</h3>

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
                            <span class="ms-2">Sakit <span class="badge badge-warning">{{ $pending_sick_applications_count }}</span></span>
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
                            <span class="ms-2">Cuti</span>
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link text-active-primary" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#event-leave-tab">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16">
                                <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z" />
                                <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z" />
                            </svg>
                            <span class="ms-2 align-middle">Off Karena Event</span>
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
                        <input type="text" data-kt-sick-application-table-filter="search" class="form-control form-control-sm w-250px ps-15" placeholder="Cari Pegawai" />
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
                <div class="tab-pane fade" id="permission-tab" role="tab-panel">
                    <!--begin::Table-->
                    <div class="d-flex align-items-center position-relative my-1">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <input type="text" data-kt-permission-application-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Pegawai" />
                    </div>
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="permission_applications_table">
                        <!--begin::Table head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th>Nama Pegawai</th>
                                <th>Kategori</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Tanggal Izin</th>
                                <th>Alasan</th>
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
                                <td class="text-gray-800">{{ $permission_application->employee->name ?? '' }}</td>
                                <td class="text-gray-800">{{ $permission_application->category->name ?? '' }}</td>
                                <td>{{ \Carbon\Carbon::parse($sick_application->date)->isoFormat('LL')  }}</td>
                                <td class="text-start">
                                    <?php
                                    $applicationDates = explode(',', $permission_application->application_dates);
                                    $limit = 2;
                                    ?>
                                    @for($i = 0; $i < $limit; $i++) @isset($applicationDates[$i]) <span class="badge badge-light">{{ $applicationDates[$i] }}</span>
                                        @endisset
                                        @break(!isset($applicationDates[$i]))
                                        @endfor

                                        @if(count($applicationDates) > $limit)
                                        <?php $remainingDays = count($applicationDates) - $limit ?>
                                        <span type="button" class="badge badge-dark" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="
                                        <?php
                                        for ($i = ($limit); $i < count($applicationDates); $i++) {
                                            echo $applicationDates[$i] . ($i < (count($applicationDates) - 1) ? ', ' : '');
                                        }
                                        ?>
                                        ">
                                            {{ $remainingDays }} tanggal lainnya
                                        </span>
                                        @endif
                                </td>
                                <td class="text-gray-800">{{ $permission_application->note }}</td>
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
                    <div class="d-flex align-items-center position-relative mb-5">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <input type="text" data-kt-leave-application-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Pegawai" />
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
                        <input type="text" data-kt-event-leave-application-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Pegawai" />
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
                            @foreach($event_leave_applications as $leave_application)
                            <tr>
                                <td class="text-gray-800 ps-2">
                                    <div>{{ $leave_application->employee->name ?? '' }}</div>
                                    <div>
                                        <small class="text-gray-500">{{ $leave_application->employee->office->division->name ?? '' }}</small>
                                    </div>
                                </td>
                                <td>{{ $leave_application->category->name ?? ""  }}</td>
                                <td>{{ \Carbon\Carbon::parse($leave_application->date)->isoFormat('LL')  }}</td>
                                <td class="text-start">
                                    <?php
                                    $applicationDates = explode(',', $leave_application->application_dates);
                                    $limit = 2;
                                    ?>
                                    @for($i = 0; $i < $limit; $i++) @isset($applicationDates[$i]) <span class="badge badge-light">{{ $applicationDates[$i] }}</span>
                                        @endisset
                                        @break(!isset($applicationDates[$i]))
                                        @endfor

                                        @if(count($applicationDates) > $limit)
                                        <?php $remainingDays = count($applicationDates) - $limit ?>
                                        <span type="button" class="badge badge-dark" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="
                                        <?php
                                        for ($i = ($limit); $i < count($applicationDates); $i++) {
                                            echo $applicationDates[$i] . ($i < (count($applicationDates) - 1) ? ', ' : '');
                                        }
                                        ?>
                                        ">
                                            {{ $remainingDays }} tanggal lainnya
                                        </span>
                                        @endif
                                </td>
                                <td>{{ $leave_application->note  }}</td>
                                <td>
                                    <span class="badge badge-{{ statusColor($leave_application->approval_status) }} text-uppercase">
                                        {{$leave_application->approval_status}}
                                    </span>
                                </td>
                                <td class="pe-2">
                                    <div class="d-flex justify-content-end">
                                        @can('update', App\Models\LeaveApplication::class)
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
                                        @endcan
                                        @can('delete', App\Models\LeaveApplication::class)
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
                                        @endcan
                                        @can('update', App\Models\LeaveApplication::class)
                                        @if($leave_application->approval_status == 'pending')
                                        <button class="btn btn-sm btn-icon btn-light-danger ms-2" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Tolak" @click="openApprovalConfirmation({{ $leave_application->id }}, 'cuti', 'reject')">
                                            <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                            <span class="svg-icon svg-icon-5 m-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </button>
                                        <button class="btn btn-sm btn-icon btn-light-success ms-2" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Setujui" @click="openApprovalConfirmation({{ $leave_application->id }}, 'cuti', 'approve')">
                                            <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                            <span class="svg-icon svg-icon-5 m-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z" fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </button>
                                        @endif
                                        @endcan
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
    <!--end::Card-->
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
        sickDatatable = $('#sick_applications_table').DataTable({
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

        leaveDatatable = $('#leave_applications_table').DataTable({
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

        eventLeaveDatatable = $('#event_leave_applications_table').DataTable({
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

        // openApprovalConfirmation(id, applicationType, type) {
        //         const self = this;
        //         Swal.fire({
        //             title: 'Apakah anda yakin?',
        //             text: "Pengajuan akan " + (type == 'approve' ? 'Disetujui' : 'Ditolak'),
        //             icon: 'warning',
        //             reverseButtons: true,
        //             showCancelButton: true,
        //             confirmButtonText: (type == 'approve' ? 'Setujui' : 'Tolak'),
        //             cancelButtonText: 'Batal',
        //             customClass: {
        //                 confirmButton: "btn " + (type == 'approve' ? 'btn-success' : 'btn-danger'),
        //                 cancelButton: "btn btn-light"
        //             },
        //             showLoaderOnConfirm: true,
        //             preConfirm: () => {
        //                 return self.sendConfirmationRequest(id, applicationType, type);
        //             },
        //             allowOutsideClick: () => !Swal.isLoading(),
        //             backdrop: true,
        //         })
        //     },
        //     // COMPANY METHODS
        //     openDeleteConfirmation(id, type, status) {
        //         const self = this;

        //         let html = '<span>Data akan dihapus</span>';
        //         if (status == 'approved') {
        //             html = `<div class="alert alert-warning" role="alert">
        //             Pengajuan ini sudah disetujui. Data yang terhubung seperti absensi akan ikut terhapus
        //             </div>`;
        //         }

        //         Swal.fire({
        //             title: 'Apakah anda yakin?',
        //             // text: "Data akan dihapus",
        //             html,
        //             icon: 'warning',
        //             reverseButtons: true,
        //             showCancelButton: true,
        //             confirmButtonText: 'Hapus',
        //             cancelButtonText: 'Batal',
        //             customClass: {
        //                 confirmButton: "btn btn-danger",
        //                 cancelButton: "btn btn-light"
        //             },
        //             showLoaderOnConfirm: true,
        //             preConfirm: () => {
        //                 return self.sendDeleteRequest(id, type);
        //             },
        //             allowOutsideClick: () => !Swal.isLoading(),
        //             backdrop: true,
        //         })
        //     },
        //     sendConfirmationRequest(id, applicationType, type) {
        //         const self = this;
        //         let url = null;
        //         if (type == 'approve') {
        //             if (applicationType == 'sakit') {
        //                 url = `/sick-applications/${id}/approve`;
        //             } else if (applicationType == 'izin') {
        //                 url = `/permission-applications/${id}/approve`;
        //             } else if (applicationType == 'cuti') {
        //                 url = `/leave-applications/${id}/approve`;
        //             }
        //         } else if (type == 'reject') {
        //             if (applicationType == 'sakit') {
        //                 url = `/sick-applications/${id}/reject`;
        //             } else if (applicationType == 'izin') {
        //                 url = `/permission-applications/${id}/reject`;
        //             } else if (applicationType == 'cuti') {
        //                 url = `/leave-applications/${id}/reject`;
        //             }
        //         }

        //         console.log(url);

        //         if (url) {
        //             return axios.post(url)
        //                 .then(function(response) {
        //                     let message = response?.data?.message;
        //                     if (!message) {
        //                         message = 'Data berhasil ' + type
        //                     }
        //                     // self.deleteCompany(id);
        //                     // redrawDatatable();
        //                     toastr.success(message);
        //                 })
        //                 .catch(function(error) {
        //                     console.error(error)
        //                     // console.log(error.data);
        //                     let message = error?.response?.data?.message;
        //                     if (!message) {
        //                         message = 'Something wrong...'
        //                     }
        //                     toastr.error(message);
        //                 });
        //         }
        //         return null;
        //     },
        //     sendDeleteRequest(id, type) {
        //         const self = this;
        //         let url = null;
        //         if (type == 'sakit') {
        //             url = `/sick-applications/${id}`;
        //         } else if (type == 'izin') {
        //             url = `/permission-applications/${id}`;
        //         } else if (type == 'cuti') {
        //             url = `/leave-applications/${id}`;
        //         }

        //         if (url) {
        //             return axios.delete(url)
        //                 .then(function(response) {
        //                     let message = response?.data?.message;
        //                     if (!message) {
        //                         message = 'Data berhasil disimpan'
        //                     }
        //                     // self.deleteCompany(id);
        //                     // redrawDatatable();
        //                     toastr.success(message);
        //                     document.location.reload();
        //                 })
        //                 .catch(function(error) {
        //                     console.error(error)
        //                     // console.log(error.data);
        //                     let message = error?.response?.data?.message;
        //                     if (!message) {
        //                         message = 'Something wrong...'
        //                     }
        //                     toastr.error(message);
        //                 });
        //         }
        //         return null;
        //     },
    });

    $('#select-year').on('change', function(e) {
        document.location.href = "/time-offs?year=" + $(this).val();
    })
</script>
@endsection