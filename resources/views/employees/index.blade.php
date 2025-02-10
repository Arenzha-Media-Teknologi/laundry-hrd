@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@inject('carbon', 'Carbon\Carbon')
@inject('carbonInterval', 'Carbon\CarbonInterval')

@section('content')
<div id="kt_content_container" class="container-xxl">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0">Daftar Pegawai</h1>
            <p class="text-muted mb-0">Daftar semua pegawai</p>
        </div>
        <div class="d-flex">
            <div class="me-3">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        Lainnya
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="/employees/data-completion">Kelengkapan Data Pegawai</a></li>
                    </ul>
                </div>
            </div>
            @can('create', App\Models\Employee::class)
            <div>
                <a href="/employees/create-v2" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Pegawai</a>
            </div>
            @endcan
        </div>
    </div>
    <div class="separator my-5" style="border-bottom-width: 3px;"></div>
    <div class="row g-5 g-xl-10 mb-xl-6">
        <div class="col-md-6 mb-5 mb-xl-0">
            <!--begin::Chart widget 3-->
            <div class="card card-flush bgi-no-repeat bgi-size-contain overflow-hidden" style="background-color: #E0F2F1; position: relative;">
                <!--begin::Header-->
                <div class="card-header py-5">
                    <!--begin::Title-->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-5">
                            <img src="{{ asset('assets/media/illustrations/custom-1/002-verified-user.png') }}" alt="Illustration" height="50">
                        </div>
                        <div>
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder" style="color: #009688;">Pegawai Aktif</span>
                                <span class="mt-1 fw-bold fs-6" style="color: #009688;">Jumlah seluruh pegawai aktif</span>
                            </h3>
                        </div>
                    </div>
                    <!--end::Title-->
                    <!--begin::Toolbar-->

                    <!--end::Toolbar-->
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body px-9">
                    <div class="d-flex justify-content-between align-items-end">
                        <div>
                            <div class="fs-3x fw-bolder">{{ collect($companies)->sum('active_employees_count') }}</div>
                            <div>Pegawai dari <strong>{{ count($companies) }}</strong> perusahaan</div>
                        </div>
                        <div class="text-end">
                            <a class="btn btn-success" style="background-color: #009688 !important;" data-bs-toggle="collapse" href="#collapseEmployeeStatistic" role="button" aria-expanded="false" aria-controls="collapseEmployeeStatistic">Detail</a>
                        </div>
                    </div>
                    <!-- <div class="fs-3" style="color: #009688;">Pegawai</div> -->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Chart widget 3-->
        </div>
        <div class="col-md-6 mb-5 mb-xl-0">
            <!--begin::Chart widget 3-->
            <div class="card card-flush bgi-no-repeat bgi-size-contain overflow-hidden" style="background-color: #FFEBEE;">
                <!--begin::Header-->
                <div class="card-header py-5">
                    <!--begin::Title-->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-5">
                            <img src="{{ asset('assets/media/illustrations/custom-1/001-delete-user.png') }}" alt="Illustration" height="50">
                        </div>
                        <div>
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder" style="color: #D32F2F;">Pegawai Nonaktif</span>
                                <span class="mt-1 fw-bold fs-6" style="color: #D32F2F;">Jumlah seluruh pegawai Nonaktif</span>
                            </h3>
                        </div>
                    </div>
                    <!--end::Title-->
                    <!--begin::Toolbar-->

                    <!--end::Toolbar-->
                </div>
                <!--end::Header-->
                <!--begin::Card body-->
                <div class="card-body px-9">
                    <div class="d-flex justify-content-between align-items-end">
                        <div>
                            <div class="fs-3x fw-bolder">{{ collect($companies)->sum('inactive_employees_count') }}</div>
                            <div>Pegawai dari <strong>{{ count($companies) }}</strong> perusahaan</div>
                        </div>
                        <div class="text-end">
                            <a class="btn btn-success" style="background-color: #D32F2F !important;" data-bs-toggle="collapse" href="#collapseEmployeeStatistic" role="button" aria-expanded="false" aria-controls="collapseEmployeeStatistic">Detail</a>
                        </div>
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Chart widget 3-->
        </div>
    </div>
    <div class="row g-5 g-xl-10 mb-xl-6 collapse" id="collapseEmployeeStatistic">
        <div class="col-md-6 mb-5 mb-xl-0 ">
            <div class="card card-flush overflow-hidden">
                <div class="card-body px-9">
                    @foreach($companies as $company)
                    <div class="row pb-2">
                        <div class="col-sm-8">
                            <a href="/employees?company_id={{ $company->id }}&active=1" class="fs-5 fw-bold">
                                {{ $company->name }}
                                <i class="bi bi-funnel"></i>
                            </a>
                        </div>
                        <div class="col-sm-4 text-end">
                            <span class="fs-5 text-gray-800 fw-bolder">{{ $company->active_employees_count }}</span>
                            <span class="fs-6 text-gray-600">Pegawai</span>
                        </div>
                    </div>
                    @if(!$loop->last)
                    <div class="separator separator-dashed mb-2"></div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-5 mb-xl-0 ">
            <div class="card card-flush overflow-hidden">
                <div class="card-body px-9">
                    @foreach($companies as $company)
                    <div class="row pb-2">
                        <div class="col-sm-8">
                            <a href="/employees?company_id={{ $company->id }}&active=0" class="fs-5 fw-bold">
                                {{ $company->name }}
                                <i class="bi bi-funnel"></i>
                            </a>
                        </div>
                        <div class="col-sm-4 text-end">
                            <span class="fs-5 text-gray-800 fw-bolder">{{ $company->inactive_employees_count }}</span>
                            <span class="fs-6 text-gray-600">Pegawai</span>
                        </div>
                    </div>
                    @if(!$loop->last)
                    <div class="separator separator-dashed mb-2"></div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <?php
    $searchKeyword = request()->query('search');
    $companyId = request()->query('company_id');
    $divisionId = request()->query('division_id');
    $active = $filter['active'];
    ?>
    <div class="justify-content-center" id="collapseExample">
        <div class="mb-4">
            <form action="/employees" action="GET">
                <div class="row">
                    <div class="col-md-3">
                        <select name="company_id" class="form-select form-select-sm">
                            <option value="">Semua Perusahaan</option>
                            @foreach($all_companies as $single_company)
                            <option value="{{ $single_company->id }}">{{ $single_company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="division_id" class="form-select form-select-sm">
                            <option value="">Semua Divisi</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="active" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="1" <?= $active == '1' ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= $active == '0' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" value="{{ $searchKeyword }}" placeholder="Cari pegawai.." name="search" />
                            <!-- <button type="submit" class="btn btn-secondary"><i class="bi bi-search"></i></button> -->
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary btn-sm w-100"><i class="bi bi-funnel"></i> Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @if((isset($searchKeyword) && $searchKeyword !== "") || isset($companyId) || isset($divisionId) || isset($active))
    <div class="mb-5">
        <!-- <span class="fs-5 fw-bold text-gray-500">Hasil filter untuk <span class="fw-bolder text-gray-600">{{ $searchKeyword }}</span>:</span> -->
        <p class="fs-5 fw-bold text-gray-500 mb-2">Hasil filter untuk:</p>
        <div class="d-flex align-items-center">
            <div class="me-3">
                <span class="badge badge-secondary">{{ $filtered_company_name }}</span>
            </div>
            <div class="me-3">
                <span class="badge badge-secondary">{{ $filtered_division_name }}</span>
            </div>
            <div class="me-3">
                <span class="badge badge-secondary">{{ $filtered_active }}</span>
            </div>
            <div class="me-3">
                <span class="badge badge-secondary"><i class="bi bi-search me-2"></i> {{ $searchKeyword }}</span>
            </div>
            <div>
                <a href="/employees" class="badge badge-danger text-white align-middle"><i class="bi bi-x text-white"></i> Clear</a>
            </div>
        </div>
    </div>
    @endif
    <div class="mb-xl-10">
        <div class="row g-6 g-xl-9">
            @if(count($employees) == 0)
            <div class="text-center">
                <i class="bi bi-people-fill fs-5x"></i>
                <p class="text-muted fs-3">Tidak ada data</p>
            </div>
            @endif
            @foreach ($employees as $employee)
            <div class="col-sm-12 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"></div>
                        <div class="card-toolbar">
                            <div class="me-0">
                                <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    <i class="bi bi-three-dots fs-3"></i>
                                </button>
                                <!--begin::Menu 3-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px py-3" data-kt-menu="true">
                                    @can('update', App\Models\Employee::class)
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="/employees/{{ $employee->id }}/edit-v2" class="menu-link px-3 text-primary"><i class="bi bi-pencil text-primary"></i>&nbsp;&nbsp;Ubah</a>
                                    </div>
                                    <!--end::Menu item-->
                                    @endcan
                                    @can('update', App\Models\Employee::class)
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 text-danger btn-delete-employee" data-id="{{ $employee->id }}"><i class="bi bi-trash text-danger"></i>&nbsp;&nbsp;Hapus</a>
                                    </div>
                                    <!--end::Menu item-->
                                    @endcan
                                    @can('viewCareer', App\Models\Employee::class)
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="/employees/{{ $employee->id }}/careers" class="menu-link px-3">Karir</a>
                                    </div>
                                    <!--end::Menu item-->
                                    @endcan
                                    @can('viewAttendance', App\Models\Employee::class)
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3 my-1">
                                        <a href="/employees/{{ $employee->id }}/attendances" class="menu-link px-3">Kehadiran</a>
                                    </div>
                                    @endcan
                                    @can('viewLoan', App\Models\Employee::class)
                                    <div class="menu-item px-3 my-1">
                                        <a href="/employees/{{ $employee->id }}/loans" class="menu-link px-3">Pinjaman</a>
                                    </div>
                                    @endcan
                                    @can('viewTimeOff', App\Models\Employee::class)
                                    <div class="menu-item px-3 my-1">
                                        <a href="/employees/{{ $employee->id }}/time-offs" class="menu-link px-3">Time Off</a>
                                    </div>
                                    @endcan
                                    @can('viewInsurance', App\Models\Employee::class)
                                    <div class="menu-item px-3 my-1">
                                        <a href="/employees/{{ $employee->id }}/insurances" class="menu-link px-3">Asuransi</a>
                                    </div>
                                    @endcan
                                    @can('viewFile', App\Models\Employee::class)
                                    <div class="menu-item px-3 my-1">
                                        <a href="/employees/{{ $employee->id }}/files" class="menu-link px-3">File</a>
                                    </div>
                                    @endcan
                                    @can('viewSetting', App\Models\Employee::class)
                                    <div class="menu-item px-3 my-1">
                                        <a href="/employees/{{ $employee->id }}/setting" class="menu-link px-3">Pengaturan</a>
                                    </div>
                                    @endcan
                                    <!--end::Menu item-->
                                </div>
                                <!--end::Menu 3-->
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!--begin::Details-->
                        <div class="d-flex flex-wrap flex-sm-nowrap">
                            <!--begin: Pic-->
                            <div class="me-7 mb-4">
                                <div class="symbol symbol-100px symbol-fixed position-relative">
                                    @if(isset($employee?->photo))
                                    <img src="{{ $employee?->photo }}" alt="image" loading="lazy" />
                                    @else
                                    <img src="{{ asset('assets/media/svg/avatars/blank.svg') }}" alt="image" />
                                    @endif
                                </div>
                            </div>
                            <!--end::Pic-->
                            <!--begin::Info-->
                            <div class="flex-grow-1">
                                <!--begin::Title-->
                                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                    <!--begin::User-->
                                    <div class="d-flex flex-column w-100">
                                        <!--begin::Name-->
                                        <div class="d-flex align-items-top justify-content-between mb-2">
                                            <div class="d-flex align-items-center mb-2">
                                                <a href="/employees/{{ $employee->id }}/detail-v2" class="text-gray-900 text-hover-primary fs-3 fw-bolder me-1">{{ $employee->name }}</a>

                                                @if($employee->active == 1)
                                                <!--begin::Svg Icon | path: icons/duotune/general/gen026.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                                                        <path d="M10.4343 12.4343L8.75 10.75C8.33579 10.3358 7.66421 10.3358 7.25 10.75C6.83579 11.1642 6.83579 11.8358 7.25 12.25L10.2929 15.2929C10.6834 15.6834 11.3166 15.6834 11.7071 15.2929L17.25 9.75C17.6642 9.33579 17.6642 8.66421 17.25 8.25C16.8358 7.83579 16.1642 7.83579 15.75 8.25L11.5657 12.4343C11.2533 12.7467 10.7467 12.7467 10.4343 12.4343Z" fill="black" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                                @else
                                                <!--begin::Svg Icon | path: icons/duotune/general/gen026.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-danger">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                                                        <rect x="7" y="15.3137" width="12" height="2" rx="1" transform="rotate(-45 7 15.3137)" fill="black" />
                                                        <rect x="8.41422" y="7" width="12" height="2" rx="1" transform="rotate(45 8.41422 7)" fill="black" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                                @endif
                                            </div>
                                            <div class="d-flex">
                                                <!--begin::Menu-->
                                                <div class="me-0">
                                                    <!-- <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                        <i class="bi bi-three-dots fs-3"></i>
                                                    </button> -->
                                                    <!--begin::Menu 3-->
                                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px py-3" data-kt-menu="true">
                                                        <!--begin::Heading-->
                                                        <div class="menu-item px-3">
                                                            <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Payments</div>
                                                        </div>
                                                        <!--end::Heading-->
                                                        <!--begin::Menu item-->
                                                        <div class="menu-item px-3">
                                                            <a href="#" class="menu-link px-3">Create Invoice</a>
                                                        </div>
                                                        <!--end::Menu item-->
                                                    </div>
                                                    <!--end::Menu 3-->
                                                </div>
                                                <!--end::Menu-->
                                            </div>
                                        </div>
                                        <!--end::Name-->
                                        <!--begin::Info-->
                                        <div class="d-flex flex-wrap fw-bold fs-6 pe-2">
                                            <span class="d-flex align-items-center text-gray-400 me-5 mb-2">
                                                <!--begin::Svg Icon | path: icons/duotune/communication/com006.svg-->
                                                <span class="svg-icon svg-icon-4 me-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 7C10.3 7 9 8.3 9 10C9 11.7 10.3 13 12 13C13.7 13 15 11.7 15 10C15 8.3 13.7 7 12 7Z" fill="black" />
                                                        <path d="M12 22C14.6 22 17 21 18.7 19.4C17.9 16.9 15.2 15 12 15C8.8 15 6.09999 16.9 5.29999 19.4C6.99999 21 9.4 22 12 22Z" fill="black" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->{{ $employee?->activeCareer?->jobTitle?->name }}
                                            </span>
                                            <span class="d-flex align-items-center text-gray-400 me-5 mb-2">
                                                <!--begin::Svg Icon | path: icons/duotune/communication/com011.svg-->
                                                <span class="svg-icon svg-icon-4 me-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path d="M20 14H18V10H20C20.6 10 21 10.4 21 11V13C21 13.6 20.6 14 20 14ZM21 19V17C21 16.4 20.6 16 20 16H18V20H20C20.6 20 21 19.6 21 19ZM21 7V5C21 4.4 20.6 4 20 4H18V8H20C20.6 8 21 7.6 21 7Z" fill="black" />
                                                        <path opacity="0.3" d="M17 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H17C17.6 2 18 2.4 18 3V21C18 21.6 17.6 22 17 22ZM10 7C8.9 7 8 7.9 8 9C8 10.1 8.9 11 10 11C11.1 11 12 10.1 12 9C12 7.9 11.1 7 10 7ZM13.3 16C14 16 14.5 15.3 14.3 14.7C13.7 13.2 12 12 10.1 12C8.10001 12 6.49999 13.1 5.89999 14.7C5.59999 15.3 6.19999 16 7.39999 16H13.3Z" fill="black" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->{{ $employee?->phone ? $employee?->phone : '-' }}
                                            </span>
                                            <span class="d-flex align-items-center text-gray-400 mb-2">
                                                <!--begin::Svg Icon | path: icons/duotune/communication/com011.svg-->
                                                <span class="svg-icon svg-icon-4 me-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path opacity="0.3" d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19Z" fill="black" />
                                                        <path d="M21 5H2.99999C2.69999 5 2.49999 5.10005 2.29999 5.30005L11.2 13.3C11.7 13.7 12.4 13.7 12.8 13.3L21.7 5.30005C21.5 5.10005 21.3 5 21 5Z" fill="black" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->{{ $employee?->email ? $employee?->email : '-' }}
                                            </span>
                                        </div>
                                        <!--end::Info-->
                                    </div>
                                    <!--end::User-->
                                    <!--begin::Actions-->
                                    <!--end::Actions-->
                                </div>
                                <div>
                                    <span class="text-gray-400 mb-2">Bekerja sejak {{ $employee->start_work_date }}</span>
                                    <span class="text-gray-400 mb-2 d-block">({{ \Carbon\Carbon::parse($employee->start_work_date)->diffForHumans(date('Y-m-d'), ['parts' => 4]) }})</span>
                                </div>
                                <!--end::Title-->
                                <div class="separator my-5"></div>
                                <!--begin::Stats-->
                                <div>
                                    <div class="row mb-4">
                                        <!--begin::Label-->
                                        <label class="col-lg-6 fw-bold text-muted">ID User</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-6">
                                            <span class="fw-bolder fs-6 text-gray-800">
                                                {{ $employee->id }}
                                            </span>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <div class="row mb-4">
                                        <!--begin::Label-->
                                        <label class="col-lg-6 fw-bold text-muted">ID Pegawai</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-6">
                                            <span class="fw-bolder fs-6 text-gray-800">
                                                {{ $employee->office->division->company->initial ?? 'NA' }}-{{ $employee->office->division->initial ?? 'NA' }}-{{ $employee->number }}
                                            </span>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!-- <div class="row mb-4">
                                        <label class="col-lg-6 fw-bold text-muted">Tanggal Awal Bekerja</label>
                                        <div class="col-lg-6">
                                            @isset($employee->start_work_date)
                                            <?php $startWorkDate = $carbon::parse($employee->start_work_date) ?>
                                            <span class="fw-bolder fs-6 text-gray-800">{{ $carbon::parse($employee->start_work_date)->isoFormat('D MMMM Y') }}</span>
                                            @endisset
                                        </div>
                                    </div> -->
                                </div>
                                <!--end::Stats-->
                            </div>
                            <!--end::Info-->
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            {{ $employees->links() }}
            <!-- <div class="d-flex flex-stack flex-wrap pt-10">
                <div class="fs-6 fw-bold text-gray-700">Showing 1 to 10 of 50 entries</div>
                <ul class="pagination">
                    <li class="page-item previous">
                        <a href="#" class="page-link">
                            <i class="previous"></i>
                        </a>
                    </li>
                    <li class="page-item active">
                        <a href="#" class="page-link">1</a>
                    </li>
                    <li class="page-item next">
                        <a href="#" class="page-link">
                            <i class="next"></i>
                        </a>
                    </li>
                </ul>
            </div> -->
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection

@section('pagescript')
<script>
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

        $('.btn-delete-employee').on('click', function(e) {
            e.preventDefault();
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
                    const id = $(this).attr('data-id');
                    return axios.delete('/employees/' + id)
                        .then(function(response) {
                            let message = response?.data?.message;
                            if (!message) {
                                message = 'Data berhasil disimpan'
                            }
                            // redrawDatatable();
                            toastr.success(message);
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
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
                allowOutsideClick: () => !Swal.isLoading(),
                backdrop: true,
            })
        })

        const divisions = <?php echo Illuminate\Support\Js::from($all_divisions) ?>;

        const companyId = '{{ request()->query("company_id") ?? "" }}';
        if (companyId) {
            $('select[name="company_id"]').val(companyId);
            const divisionId = '{{ request()->query("division_id") ?? "" }}'
            let options = divisions.filter(division => division.company_id == companyId).map(division => `<option value="${division.id}" ${division.id == divisionId ? 'selected' : ''}>${division.name}</option>`);
            // options.unshift('<option value="">Semua Divisi</option>');
            options = options.join("");
            // console.log(options);
            $('select[name="division_id"]').append(options);
        }

        $('select[name="company_id"]').on('change', function() {
            // console.log($(this).val());
            const companyId = $(this).val();
            $('select[name="division_id"]').empty();
            let options = divisions.filter(division => division.company_id == companyId).map(division => `<option value="${division.id}">${division.name}</option>`);
            options.unshift('<option value="">Semua Divisi</option>');
            options = options.join("");
            // console.log(options);
            $('select[name="division_id"]').append(options);
        });
    })
</script>
@endsection