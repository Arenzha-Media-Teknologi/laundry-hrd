@extends('layouts.app')

@section('title', 'Dashboard')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@inject('carbon', 'Carbon\Carbon')
@inject('carbonInterval', 'Carbon\CarbonInterval')

@section('content')
<?php
$groupPermissions = auth()->user()->employee->credential->group->permissions ?? "[]";
$permissions = json_decode($groupPermissions);
?>
<div id="kt_content_container" class="container-xxl">

    <div class="row gy-5 g-xl-8">
        <!--begin::Row-->
        <div class="col-xl-12">
            <div class="card card-flush  mb-xl-8">
                <!--begin::Heading-->
                <div class="card-header bgi-no-repeat bgi-size-cover bgi-position-y-top bgi-position-x-center align-items-start" style="background-image:url('assets/media/svg/shapes/top-green.png')">
                    <!--begin::Title-->
                    <h3 class="card-title align-items-start flex-column text-white pt-15">
                        <span class="fw-bolder fs-2x mb-3">Halo, Admin</span>
                        <div class="fs-4 text-white">
                            <span class="opacity-75">Hari ini {{ \Carbon\Carbon::now()->locale('id')->dayName }}, {{ \Carbon\Carbon::now()->isoFormat('LL') }}</span>
                        </div>
                        <div class="d-flex align-items-center my-5">
                            <div class="me-3">
                                <span class="svg-icon svg-icon-white svg-icon-2hx mt-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M11.2929 2.70711C11.6834 2.31658 12.3166 2.31658 12.7071 2.70711L15.2929 5.29289C15.6834 5.68342 15.6834 6.31658 15.2929 6.70711L12.7071 9.29289C12.3166 9.68342 11.6834 9.68342 11.2929 9.29289L8.70711 6.70711C8.31658 6.31658 8.31658 5.68342 8.70711 5.29289L11.2929 2.70711Z" fill="black" />
                                        <path d="M11.2929 14.7071C11.6834 14.3166 12.3166 14.3166 12.7071 14.7071L15.2929 17.2929C15.6834 17.6834 15.6834 18.3166 15.2929 18.7071L12.7071 21.2929C12.3166 21.6834 11.6834 21.6834 11.2929 21.2929L8.70711 18.7071C8.31658 18.3166 8.31658 17.6834 8.70711 17.2929L11.2929 14.7071Z" fill="black" />
                                        <path opacity="0.3" d="M5.29289 8.70711C5.68342 8.31658 6.31658 8.31658 6.70711 8.70711L9.29289 11.2929C9.68342 11.6834 9.68342 12.3166 9.29289 12.7071L6.70711 15.2929C6.31658 15.6834 5.68342 15.6834 5.29289 15.2929L2.70711 12.7071C2.31658 12.3166 2.31658 11.6834 2.70711 11.2929L5.29289 8.70711Z" fill="black" />
                                        <path opacity="0.3" d="M17.2929 8.70711C17.6834 8.31658 18.3166 8.31658 18.7071 8.70711L21.2929 11.2929C21.6834 11.6834 21.6834 12.3166 21.2929 12.7071L18.7071 15.2929C18.3166 15.6834 17.6834 15.6834 17.2929 15.2929L14.7071 12.7071C14.3166 12.3166 14.3166 11.6834 14.7071 11.2929L17.2929 8.70711Z" fill="black" />
                                    </svg>
                                </span>
                            </div>
                            @if(count($event_calendars) > 0)
                            <div>
                                @foreach($event_calendars as $event)
                                <span class="badge badge-white opacity-75 d-inline-block me-3">{{ $event->name }}</span>
                                @endforeach
                            </div>
                            @else
                            <span class="badge badge-white opacity-75 d-inline-block me-3">Tidak ada event hari ini</span>
                            @endif
                        </div>
                    </h3>
                    <!--end::Title-->
                </div>
                <!--end::Heading-->
                <!--begin::Body-->
                <div class="card-body">
                    <h6 class="text-gray-700">Pintasan</h6>
                    @if(in_array('view_dashboard', $permissions))
                    <div class="mt-2">
                        <a href="/employees" class="btn btn-light me-3 mt-3">
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M6.28548 15.0861C7.34369 13.1814 9.35142 12 11.5304 12H12.4696C14.6486 12 16.6563 13.1814 17.7145 15.0861L19.3493 18.0287C20.0899 19.3618 19.1259 21 17.601 21H6.39903C4.87406 21 3.91012 19.3618 4.65071 18.0287L6.28548 15.0861Z" fill="black" />
                                    <rect opacity="0.3" x="8" y="3" width="8" height="8" rx="4" fill="black" />
                                </svg>
                            </span>Pegawai
                        </a>
                        <a href="/attendances" class="btn btn-light me-3 mt-3">
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="black" />
                                    <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="black" />
                                    <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="black" />
                                </svg>
                            </span>Kehadiran
                        </a>
                        <a href="/payrolls/monthly" class="btn btn-light me-3 mt-3">
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M18 21.6C16.3 21.6 15 20.3 15 18.6V2.50001C15 2.20001 14.6 1.99996 14.3 2.19996L13 3.59999L11.7 2.3C11.3 1.9 10.7 1.9 10.3 2.3L9 3.59999L7.70001 2.3C7.30001 1.9 6.69999 1.9 6.29999 2.3L5 3.59999L3.70001 2.3C3.50001 2.1 3 2.20001 3 3.50001V18.6C3 20.3 4.3 21.6 6 21.6H18Z" fill="black" />
                                    <path d="M12 12.6H11C10.4 12.6 10 12.2 10 11.6C10 11 10.4 10.6 11 10.6H12C12.6 10.6 13 11 13 11.6C13 12.2 12.6 12.6 12 12.6ZM9 11.6C9 11 8.6 10.6 8 10.6H6C5.4 10.6 5 11 5 11.6C5 12.2 5.4 12.6 6 12.6H8C8.6 12.6 9 12.2 9 11.6ZM9 7.59998C9 6.99998 8.6 6.59998 8 6.59998H6C5.4 6.59998 5 6.99998 5 7.59998C5 8.19998 5.4 8.59998 6 8.59998H8C8.6 8.59998 9 8.19998 9 7.59998ZM13 7.59998C13 6.99998 12.6 6.59998 12 6.59998H11C10.4 6.59998 10 6.99998 10 7.59998C10 8.19998 10.4 8.59998 11 8.59998H12C12.6 8.59998 13 8.19998 13 7.59998ZM13 15.6C13 15 12.6 14.6 12 14.6H10C9.4 14.6 9 15 9 15.6C9 16.2 9.4 16.6 10 16.6H12C12.6 16.6 13 16.2 13 15.6Z" fill="black" />
                                    <path d="M15 18.6C15 20.3 16.3 21.6 18 21.6C19.7 21.6 21 20.3 21 18.6V12.5C21 12.2 20.6 12 20.3 12.2L19 13.6L17.7 12.3C17.3 11.9 16.7 11.9 16.3 12.3L15 13.6V18.6Z" fill="black" />
                                </svg>
                            </span>Gaji Bulanan
                        </a>
                        <a href="/payrolls/daily" class="btn btn-light me-3 mt-3">
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M14.1 15.013C14.6 16.313 14.5 17.813 13.7 19.113C12.3 21.513 9.29999 22.313 6.89999 20.913C5.29999 20.013 4.39999 18.313 4.39999 16.613C5.09999 17.013 5.99999 17.313 6.89999 17.313C8.39999 17.313 9.69998 16.613 10.7 15.613C11.1 15.713 11.5 15.813 11.9 15.813C12.7 15.813 13.5 15.513 14.1 15.013ZM8.5 12.913C8.5 12.713 8.39999 12.513 8.39999 12.313C8.39999 11.213 8.89998 10.213 9.69998 9.613C9.19998 8.313 9.30001 6.813 10.1 5.513C10.6 4.713 11.2 4.11299 11.9 3.71299C10.4 2.81299 8.49999 2.71299 6.89999 3.71299C4.49999 5.11299 3.70001 8.113 5.10001 10.513C5.80001 11.813 7.1 12.613 8.5 12.913ZM16.9 7.313C15.4 7.313 14.1 8.013 13.1 9.013C14.3 9.413 15.1 10.513 15.3 11.713C16.7 12.013 17.9 12.813 18.7 14.113C19.2 14.913 19.3 15.713 19.3 16.613C20.8 15.713 21.8 14.113 21.8 12.313C21.9 9.513 19.7 7.313 16.9 7.313Z" fill="black" />
                                    <path d="M9.69998 9.61307C9.19998 8.31307 9.30001 6.81306 10.1 5.51306C11.5 3.11306 14.5 2.31306 16.9 3.71306C18.5 4.61306 19.4 6.31306 19.4 8.01306C18.7 7.61306 17.8 7.31306 16.9 7.31306C15.4 7.31306 14.1 8.01306 13.1 9.01306C12.7 8.91306 12.3 8.81306 11.9 8.81306C11.1 8.81306 10.3 9.11307 9.69998 9.61307ZM8.5 12.9131C7.1 12.6131 5.90001 11.8131 5.10001 10.5131C4.60001 9.71306 4.5 8.91306 4.5 8.01306C3 8.91306 2 10.5131 2 12.3131C2 15.1131 4.2 17.3131 7 17.3131C8.5 17.3131 9.79999 16.6131 10.8 15.6131C9.49999 15.1131 8.7 14.1131 8.5 12.9131ZM18.7 14.1131C17.9 12.8131 16.7 12.0131 15.3 11.7131C15.3 11.9131 15.4 12.1131 15.4 12.3131C15.4 13.4131 14.9 14.4131 14.1 15.0131C14.6 16.3131 14.5 17.8131 13.7 19.1131C13.2 19.9131 12.6 20.5131 11.9 20.9131C13.4 21.8131 15.3 21.9131 16.9 20.9131C19.3 19.6131 20.1 16.5131 18.7 14.1131Z" fill="black" />
                                </svg>
                            </span>Gaji Harian Magenta
                        </a>
                        <a href="/payrolls/daily-aerplus" class="btn btn-light me-3 mt-3">
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M2.07664 11.85L2.87663 10.05C3.07663 9.55003 3.67665 9.35003 4.17665 9.55003L5.07664 9.95002C7.17664 10.85 9.17662 10.85 11.2766 9.95002C14.3766 8.55002 17.4766 8.55002 20.5766 9.95002L21.4766 10.35C21.9766 10.55 22.1766 11.15 21.9766 11.65L21.1766 13.45C20.9766 13.95 20.3766 14.15 19.8766 13.95L18.9766 13.55C16.8766 12.65 14.8766 12.65 12.7766 13.55C9.67662 14.95 6.57663 14.95 3.47663 13.55L2.57664 13.15C2.07664 12.95 1.87664 12.35 2.07664 11.85ZM2.57664 20.05L3.47663 20.45C6.57663 21.85 9.67662 21.85 12.7766 20.45C14.8766 19.55 16.8766 19.55 18.9766 20.45L19.8766 20.85C20.3766 21.05 20.9766 20.85 21.1766 20.35L21.9766 18.55C22.1766 18.05 21.9766 17.45 21.4766 17.25L20.5766 16.85C17.4766 15.45 14.3766 15.45 11.2766 16.85C9.17662 17.75 7.17664 17.75 5.07664 16.85L4.17665 16.45C3.67665 16.25 3.07663 16.45 2.87663 16.95L2.07664 18.75C1.87664 19.25 2.07664 19.85 2.57664 20.05Z" fill="black" />
                                    <path d="M2.07664 4.94999L2.87663 3.15C3.07663 2.65 3.67665 2.45 4.17665 2.65L5.07664 3.05C7.17664 3.95 9.17662 3.95 11.2766 3.05C14.3766 1.65 17.4766 1.65 20.5766 3.05L21.4766 3.44999C21.9766 3.64999 22.1766 4.25 21.9766 4.75L21.1766 6.55C20.9766 7.05 20.3766 7.25 19.8766 7.05L18.9766 6.65C16.8766 5.75 14.8766 5.75 12.7766 6.65C9.67662 8.05 6.57663 8.05 3.47663 6.65L2.57664 6.25C2.07664 6.05 1.87664 5.44999 2.07664 4.94999Z" fill="black" />
                                </svg>
                            </span>Gaji Harian Aerplus
                        </a>
                    </div>
                    @else
                    <div class="col-xl-12 text-center">
                        <i class="bi bi-shield-lock-fill fs-5x"></i>
                        <p class="text-muted fw-bold fs-6 mt-4">Anda tidak memiliki akses untu melihat dashboard</p>
                    </div>
                    @endif
                </div>
                <!--end::Body-->
            </div>
        </div>
    </div>
    @if(in_array('view_dashboard', $permissions))
    <div class="row gy-5 g-xl-8 align-items-start">
        <!--begin::Col-->
        <div class="col-xl-12">
            <!--begin::Tables Widget 9-->
            <div class="card card-xl-stretch mb-5 mb-xl-8">
                <!--begin::Header-->
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Perlu Tindakan</span>
                        <span class="text-muted mt-1 fw-bold fs-7">Daftar tindakan yang perlu dilakukan kepada pegawai yang hardir terlambat atau di luar area kantor</span>
                    </h3>
                    <div class="card-toolbar">
                        <!-- <a href="#" id="kt_drawer_example_dismiss_button" class="btn btn-icon btn-light-primary me-2 position-relative"><i class="bi bi-funnel fs-4 "></i>
                            @if($is_filter_active)
                            <span style="width: 1rem; height: 1rem" class="position-absolute top-0 start-0 translate-middle badge badge-circle badge-primary"></span>
                            @endif
                        </a> -->
                        <!--begin::Search-->
                        <!-- <div class="d-flex align-items-center position-relative my-1">
                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                </svg>
                            </span>
                            <input type="text" data-kt-action-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Pegawai" />
                        </div> -->
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body py-3">
                    <div class="mb-5 hover-scroll-x" style="border-bottom: none;">
                        <div class="d-grid">
                            <ul class="nav nav-tabs flex-nowrap text-nowrap" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0 active" data-bs-toggle="tab" href="#tab_late_attendance" aria-selected="false" role="tab" tabindex="-1">
                                        <i class="bi bi-clock-history align-middle"></i>
                                        <span class="align-middle">Terlambat</span>
                                        <span v-cloak v-if="lateAttendanceTodos.length > 0" class="badge badge-warning">@{{ lateAttendanceTodos.length }}</span>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0" data-bs-toggle="tab" href="#tab_outside_attendance" aria-selected="false" role="tab" tabindex="-1">
                                        <i class="bi bi-geo align-middle"></i>
                                        <span class="align-middle">Di Luar Area</span>
                                        <span v-cloak v-if="outsideAttendanceTodos.length > 0" class="badge badge-warning">@{{ outsideAttendanceTodos.length }}</span>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0" data-bs-toggle="tab" href="#running-activity" aria-selected="false" role="tab" tabindex="-1">
                                        <i class="bi bi-lightning-charge"></i>
                                        <span class="align-middle">Aktifitas Berjalan</span>
                                        <span v-cloak v-if="runningActivityTodos.length > 0" class="badge badge-warning">@{{ runningActivityTodos.length }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="tab_late_attendance" role="tabpanel">
                            <div class="d-flex align-items-center position-relative mb-5">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" v-model="model.lateAttendanceSearchKeyword" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Pegawai" />
                            </div>
                            <div class="table-responsive" style="max-height: 500px;">
                                <table class="table align-middle table-row-dashed fs-7 gy-5">
                                    <!--begin::Table head-->
                                    <thead class="bg-light-primary">
                                        <!--begin::Table row-->
                                        <tr class="text-center text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="text-start min-w-150px ps-2">Pegawai</th>
                                            <th class="text-center min-w-150px">Tanggal</th>
                                            <th class="text-center min-w-150px">Perusahaan</th>
                                            <th class="text-center min-w-150px">Divisi</th>
                                            <th class="text-center min-w-150px">Kantor</th>
                                            <th class="text-center min-w-150px">Alasan</th>
                                            <th class="text-center min-w-150px">Aksi</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody class="fw-bold text-gray-600">
                                        <tr v-cloak v-for="(lateAttendanceTodo, index) in filteredLateAttendanceTodos">
                                            <!--begin::Name=-->
                                            <td class="ps-2">
                                                <div>
                                                    <a :href="`/employees/${ lateAttendanceTodo?.employee?.id }/detail-v2`" class="text-gray-800 text-hover-primary">@{{ lateAttendanceTodo?.employee?.name ?? '#' }}</a>
                                                </div>
                                                <span class="text-muted d-block fs-7">
                                                    @{{ lateAttendanceTodo?.employee?.office?.division?.company?.initial ?? '#' }}-@{{ lateAttendanceTodo?.employee?.office?.division?.initial ?? '#' }}-@{{ lateAttendanceTodo?.employee?.number ?? '#' }}
                                                </span>
                                            </td>
                                            <td class="text-center">@{{ lateAttendanceTodo?.date ?? '#' }}</td>
                                            <td class="text-center">@{{ lateAttendanceTodo?.employee?.office?.division?.company?.name ?? '#' }}</td>
                                            <td class="text-center">@{{ lateAttendanceTodo?.employee?.office?.division?.name ?? '#' }}</td>
                                            <td class="text-center">@{{ lateAttendanceTodo?.employee?.office?.name ?? '#' }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-danger">Terlambat (@{{ lateAttendanceTodo?.time_late }} menit)</span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-primary btn-sm" @click="onClickLateAttendanceConfirmation(lateAttendanceTodo?.id)">Konfirmasi</button>
                                            </td>
                                        </tr>
                                        <tr v-cloak v-if="lateAttendanceTodosLoading" class="text-center">
                                            <td colspan="7">
                                                <div class="spinner-border" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab_outside_attendance" role="tabpanel">
                            <div class="d-flex align-items-center position-relative mb-5">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" v-model="model.outsideAttendanceSearchKeyword" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Pegawai" />
                            </div>
                            <div class="table-responsive" style="max-height: 500px;">
                                <table class="table align-middle table-row-dashed fs-7 gy-5">
                                    <!--begin::Table head-->
                                    <thead class="bg-light-primary">
                                        <!--begin::Table row-->
                                        <tr class="text-center text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="text-start min-w-150px ps-2">Pegawai</th>
                                            <th class="text-center min-w-150px">Tanggal</th>
                                            <th class="text-center min-w-150px">Perusahaan</th>
                                            <th class="text-center min-w-150px">Divisi</th>
                                            <th class="text-center min-w-150px">Kantor</th>
                                            <th class="text-center min-w-150px">Alasan</th>
                                            <th class="text-center min-w-150px">Aksi</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody class="fw-bold text-gray-600">
                                        <tr v-cloak v-for="(outsideAttendanceTodo, index) in filteredOutsideAttendanceTodos">
                                            <!--begin::Name=-->
                                            <td class="ps-2">
                                                <div>
                                                    <a :href="`/employees/${ outsideAttendanceTodo?.employee?.id }/detail-v2`" class="text-gray-800 text-hover-primary">@{{ outsideAttendanceTodo?.employee?.name ?? '#' }}</a>
                                                </div>
                                                <span class="text-muted d-block fs-7">
                                                    @{{ outsideAttendanceTodo?.employee?.office?.division?.company?.initial ?? '#' }}-@{{ outsideAttendanceTodo?.employee?.office?.division?.initial ?? '#' }}-@{{ outsideAttendanceTodo?.employee?.number ?? '#' }}
                                                </span>
                                            </td>
                                            <td class="text-center">@{{ outsideAttendanceTodo?.date ?? '#' }}</td>
                                            <td class="text-center">@{{ outsideAttendanceTodo?.employee?.office?.division?.company?.name ?? '#' }}</td>
                                            <td class="text-center">@{{ outsideAttendanceTodo?.employee?.office?.division?.name ?? '#' }}</td>
                                            <td class="text-center">@{{ outsideAttendanceTodo?.employee?.office?.name ?? '#' }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-danger">Di Luar Area</span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-primary btn-sm" @click="onClickOutsideAttendanceConfirmation(outsideAttendanceTodo.id)">Konfirmasi</button>
                                            </td>
                                        </tr>
                                        <tr v-cloak v-if="outsideAttendanceTodosLoading" class="text-center">
                                            <td colspan="7">
                                                <div class="spinner-border" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="running-activity" role="tabpanel">
                            <div class="d-flex align-items-center position-relative mb-5">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" v-model="model.runningActivitySearchKeyword" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Pegawai" />
                            </div>
                            <div class="table-responsive" style="max-height: 500px;">
                                <table class="table align-middle table-row-dashed fs-7 gy-5">
                                    <!--begin::Table head-->
                                    <thead class="bg-light-primary">
                                        <!--begin::Table row-->
                                        <tr class="text-center text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="text-start min-w-150px ps-2">Pegawai</th>
                                            <th class="text-center min-w-150px">Tanggal</th>
                                            <th class="text-center min-w-150px">Perusahaan</th>
                                            <th class="text-center min-w-150px">Divisi</th>
                                            <th class="text-center min-w-150px">Kantor</th>
                                            <th class="text-center min-w-150px">Alasan</th>
                                            <th class="text-center min-w-150px">Detail</th>
                                            <th class="text-center min-w-150px">Aksi</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody class="fw-bold text-gray-600">
                                        <tr v-cloak v-for="(runningActivityTodo, index) in filteredRunningActivityTodos">
                                            <!--begin::Name=-->
                                            <td class="ps-2">
                                                <div>
                                                    <a :href="`/employees/${ runningActivityTodo?.employee?.id }/detail-v2`" class="text-gray-800 text-hover-primary">@{{ runningActivityTodo?.employee?.name ?? '#' }}</a>
                                                </div>
                                                <span class="text-muted d-block fs-7">
                                                    @{{ runningActivityTodo?.employee?.office?.division?.company?.initial ?? '#' }}-@{{ runningActivityTodo?.employee?.office?.division?.initial ?? '#' }}-@{{ runningActivityTodo?.employee?.number ?? '#' }}
                                                </span>
                                            </td>
                                            <td class="text-center">@{{ runningActivityTodo?.date ?? '#' }}</td>
                                            <td class="text-center">@{{ runningActivityTodo?.employee?.office?.division?.company?.name ?? '#' }}</td>
                                            <td class="text-center">@{{ runningActivityTodo?.employee?.office?.division?.name ?? '#' }}</td>
                                            <td class="text-center">@{{ runningActivityTodo?.employee?.office?.name ?? '#' }}</td>
                                            <td class="text-center">
                                                <div class="text-center">
                                                    <span class="badge badge-danger">Aktifitas Masih Berjalan</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div>
                                                        <strong>Waktu Check In:</strong> @{{ runningActivityTodo?.check_in_time }}
                                                    </div>
                                                    <div>
                                                        <strong>Catatan:</strong> @{{ runningActivityTodo?.check_in_location }}
                                                    </div>
                                                    <div>
                                                        <strong>Ket: </strong> @{{ runningActivityTodo?.check_in_note }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-primary btn-sm" @click="onClickRunningActivityConfirmation(runningActivityTodo?.id)">Konfirmasi</button>
                                            </td>
                                        </tr>
                                        <tr v-cloak v-if="runningActivityTodosLoading" class="text-center">
                                            <td colspan="8">
                                                <div class="spinner-border" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">

                    </div>
                </div>
                <!--begin::Body-->
            </div>
            <!--end::Tables Widget 9-->
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->
    @endif
    @if(in_array('view_dashboard', $permissions))
    <div class="row gy-5 g-xl-8 align-items-start">
        <!--begin::Col-->
        <div class="col-xl-12">
            <!--begin::Tables Widget 9-->
            <div class="card card-xl-stretch mb-5 mb-xl-8">
                <!--begin::Header-->
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Pengajuan Time Off</span>
                        <span class="text-muted mt-1 fw-bold fs-7">Pengajuan time off dengan status pending dalam 7 hari</span>
                    </h3>
                    <!-- <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" title="Click to add a user">
                        <a href="#" class="btn btn-sm btn-light btn-active-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_invite_friends">
                          
                            <span class="svg-icon svg-icon-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="black" />
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="black" />
                                </svg>
                            </span>
                         
                            New Member
                        </a>
                    </div> -->
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body py-3">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-7 gy-5">
                            <!--begin::Table head-->
                            <thead class="bg-light-primary">
                                <!--begin::Table row-->
                                <tr class="text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-150px ps-2">Pegawai</th>
                                    <th class="min-w-140px">Jenis</th>
                                    <th class="min-w-120px">Tanggal Time Off</th>
                                    <th class="min-w-120px">Waktu Pengajuan</th>
                                    <th class="min-w-100px text-end pe-2">Action</th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                                @foreach($timeoffs as $timeoff)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="{{ $timeoff?->employee?->photo ?? asset('assets/media/svg/avatars/blank.svg') }}" alt="" loading="lazy" />
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <a href="/employees/{{ $timeoff?->employee?->id }}/detail" class="text-dark fw-bolder text-hover-primary fs-6">{{ $timeoff?->employee?->name }}</a>
                                                <span class="text-muted fw-bold text-muted d-block fs-7">{{ $timeoff?->employee?->number }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($timeoff->type == 'sakit')
                                        <span class="badge badge-warning">Sakit</span>
                                        @elseif($timeoff->type == 'izin')
                                        <span class="badge badge-primary">Izin</span>
                                        @elseif($timeoff->type == 'cuti')
                                        <span class="badge badge-info">Cuti ({{ $timeoff->category->name ?? "Jenis Cuti" }})</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ $timeoff->application_dates }}</span>
                                    </td>
                                    <td class="text-start">
                                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ \Carbon\Carbon::parse($timeoff->date)->diffForHumans() }}</span>
                                    </td>
                                    <td class="text-end">
                                        <!-- <div class="d-flex justify-content-end flex-shrink-0">
                                            <a href="#" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                <span class="svg-icon svg-icon-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="black" />
                                                        <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="black" />
                                                    </svg>
                                                </span>
                                            </a>
                                            <a href="#" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                <span class="svg-icon svg-icon-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                                    </svg>
                                                </span>
                                            </a>
                                        </div> -->
                                        <a href="/time-offs">Konfimasi</a>
                                    </td>
                                </tr>
                                @endforeach
                                @if($remaining_timeoffs_count > 0)
                                <tr>
                                    <td colspan="5" class="text-center fw-bold">
                                        <a href="/time-offs">Dan {{ $remaining_timeoffs_count }} pengajuan lainnya</a>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Table container-->
                </div>
                <!--begin::Body-->
            </div>
            <!--end::Tables Widget 9-->
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->
    @endif
    @if(in_array('view_dashboard', $permissions))
    <div class="row gy-5 g-xl-8 align-items-start">
        <!--begin::Col-->
        <div class="col-xl-12">
            <!--begin::Tables Widget 9-->
            <div class="card card-xl-stretch mb-5 mb-xl-8">
                <!--begin::Header-->
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Pegawai Tidak Hadir Hari Ini</span>
                        <span class="text-muted mt-1 fw-bold fs-7">Pegawai yang belum melakukan absen masuk hari ini</span>
                    </h3>
                    <div class="card-toolbar">
                        <a href="#" id="kt_drawer_example_dismiss_button" class="btn btn-icon btn-light-primary me-2 position-relative"><i class="bi bi-funnel fs-4 "></i>
                            @if($is_filter_active)
                            <span style="width: 1rem; height: 1rem" class="position-absolute top-0 start-0 translate-middle badge badge-circle badge-primary"></span>
                            @endif
                        </a>
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
                            <input type="text" data-kt-action-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Pegawai" />
                        </div>
                        <!--end::Search-->
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body py-3">
                    <div class="mb-5">
                        <button class="btn btn-light-warning me-5" @click="applyFilter('sakit')">
                            Sakit <span class="badge badge-circle badge-warning ms-2">{{ $statistic['sakit'] }}</span>
                        </button>
                        <button class="btn btn-light-info me-5" @click="applyFilter('cuti')">
                            Cuti <span class="badge badge-circle badge-info ms-2">{{ $statistic['cuti'] }}</span>
                        </button>
                        <button class="btn btn-light me-5" @click="applyFilter('na')">
                            N/A <span class="badge badge-circle badge-dark ms-2">{{ $statistic['na'] }}</span>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-7 gy-5" id="attendance_table">
                            <!--begin::Table head-->
                            <thead class="bg-light-primary">
                                <!--begin::Table row-->
                                <tr class="text-center text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="text-start min-w-150px ps-2">Pegawai</th>
                                    <th class="text-center min-w-150px">Perusahaan</th>
                                    <th class="text-center min-w-150px">Divisi</th>
                                    <th class="text-center min-w-150px">Kantor</th>
                                    <th class="text-center min-w-150px">Status</th>
                                </tr>
                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="fw-bold text-gray-600">
                                @foreach($employees_with_attendances as $employee)
                                <?php
                                $status = $employee->attendances[0]->status ?? 'na';
                                ?>
                                @if($status != "hadir")
                                <tr class="text-center">
                                    <!--begin::Name=-->
                                    <td class="text-start ps-2" style="max-width: 200px;">
                                        <div>
                                            <div>
                                                <a href="/employees/{{ $employee->id }}/detail-v2" class="text-gray-800 text-hover-primary">{{ $employee->name }}</a>
                                            </div>
                                            <span class="text-muted d-block fs-7">
                                                {{ $employee->office->division->company->initial ?? 'NA' }}-{{ $employee->office->division->initial ?? 'NA' }}-{{ $employee->number }}
                                            </span>
                                            <!-- <span class="text-muted d-block fs-7">@{{ employee?.office?.division?.company?.initial || 'NA' }}-@{{ employee?.office?.division?.initial || 'NA' }}-@{{ employee?.number }} | @{{ employee?.office?.division?.company?.name ?? 'PERUSAHAAN' }} - @{{ employee?.office?.division?.name ?? 'DIVISI' }} - @{{ employee?.office?.name ?? 'KANTOR' }}</span>
                                            <span class="text-muted d-block fs-7">@{{ employee?.active_career?.job_title?.name || '' }} - @{{ employee?.active_career?.job_title?.designation?.name || '' }}</span> -->
                                        </div>
                                    </td>
                                    <!--end::Name=-->
                                    <td class="text-center">{{ $employee->office->division->company->name ?? 'PERUSAHAAN' }}</td>
                                    <td class="text-center">{{ $employee->office->division->name ?? 'DIVISI' }}</td>
                                    <td class="text-center">{{ $employee->office->name ?? 'KANTOR' }}</td>
                                    <td class="text-center">
                                        @if($status == 'hadir')
                                        <span class="badge badge-success">Hadir</span>
                                        @elseif($status == 'sakit')
                                        <span class="badge badge-warning">Sakit</span>
                                        @elseif($status == 'izin')
                                        <span class="badge badge-primary">Izin</span>
                                        @elseif($status == 'cuti')
                                        <span class="badge badge-info">Cuti ({{ $employee->attendances[0]->leaveApplication->category->name ?? "Jenis Cuti" }})</span>
                                        @else
                                        <span class="badge badge-light">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                    </div>
                </div>
                <!--begin::Body-->
            </div>
            <!--end::Tables Widget 9-->
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->
    @endif
    <div id="kt_drawer_example_dismiss" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true" data-kt-drawer-toggle="#kt_drawer_example_dismiss_button" data-kt-drawer-close="#kt_drawer_example_dismiss_close" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'300px', 'md': '500px'}">
        <div class="card rounded-0 w-100">
            <!--begin::Card header-->
            <div class="card-header pe-5">
                <!--begin::Title-->
                <div class="card-title">
                    <!--begin::User-->
                    <div class="d-flex justify-content-center flex-column me-3">
                        <a href="#" class="fs-4 fw-bold text-gray-900 text-hover-primary me-1 lh-1">Filter</a>
                    </div>
                    <!--end::User-->
                </div>
                <!--end::Title-->

                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-light-primary p-relative" id="kt_drawer_example_dismiss_close">
                        <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body hover-scroll-overlay-y">
                <div class="mb-5">
                    <label class="form-label fw-bolder">Perusahaan</label>
                    <select v-model="filter.companyId" class="form-select">
                        <option value="">Semua Perusahaan</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-5">
                    <label class="form-label fw-bolder">Divisi</label>
                    <select v-model="filter.divisionId" class="form-select">
                        <option value="">Semua Divisi</option>
                        <option v-for="(division, index) in filteredDivisions" :value="division.id">@{{ division.name }}</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label class="form-label fw-bolder">Kantor</label>
                    <select v-model="filter.officeId" class="form-select">
                        <option value="">Semua Kantor</option>
                        <option v-for="(office, index) in filteredOffices" :value="office.id">@{{ office.name }}</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label class="form-label fw-bolder">Status</label>
                    <select v-model="filter.status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="sakit">Sakit</option>
                        <option value="cuti">Cuti</option>
                        <option value="na">N/A (Tidak Ada Keterangan)</option>
                    </select>
                </div>
            </div>
            <!--end::Card body-->

            <!--begin::Card footer-->
            <div class="card-footer">
                <!--begin::Dismiss button-->
                <button class="btn btn-primary" @click="applyFilter()">Terapkan</button>
                <!--end::Dismiss button-->
            </div>
            <!--end::Card footer-->
        </div>
    </div>
    <!-- Issue Confirmation Modal -->
    <div class="modal fade" id="lateAttendanceConfirmationModal" tabindex="-1" aria-labelledby="lateAttendanceConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="lateAttendanceConfirmationModalLabel">Penyelesaian</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-cloak v-if="selectedLateAttendanceTodo">
                        <div class="mb-3 text-end">
                            <span class="badge badge-danger">Terlambat @{{ selectedLateAttendanceTodo?.time_late }} menit</span>
                        </div>
                        <div>
                            <div class="row mb-3">
                                <div class="col-sm-4">Nama Pegawai</div>
                                <div class="col-sm-8"><strong>@{{ selectedLateAttendanceTodo?.employee?.name || '-' }}</strong></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">Tanggal</div>
                                <div class="col-sm-8"><strong>@{{ selectedLateAttendanceTodo?.date }}</strong></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">Perusahaan</div>
                                <div class="col-sm-8"><strong>@{{ selectedLateAttendanceTodo?.employee?.office?.division?.company?.name || '-' }}</strong></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">Divisi</div>
                                <div class="col-sm-8"><strong>@{{ selectedLateAttendanceTodo?.employee?.office?.division?.name || '-' }}</strong></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">Kantor</div>
                                <div class="col-sm-8"><strong>@{{ selectedLateAttendanceTodo?.employee?.office?.name || '-' }}</strong></div>
                            </div>
                        </div>
                        <div class="separator my-5"></div>
                        <div>
                            <label class="form-label"><strong>Catatan</strong></label>
                            <textarea v-model="model.lateAttendanceSettlementNote" class="form-control form-control-sm" rows="3" placeholder="Masukkan catatan"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" :data-kt-indicator="saveLateAttendanceTodosLoading ? 'on' : null" class="btn btn-primary" @click="saveLateAttendanceTodoConfirmation()" :disabled="saveLateAttendanceTodosLoading">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">Menyimpan...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Issue Confirmation Modal -->
    <!-- Issue Confirmation Modal -->
    <div class="modal fade" id="outsideAttendanceConfirmationModal" tabindex="-1" aria-labelledby="outsideAttendanceConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="outsideAttendanceConfirmationModalLabel">Penyelesaian</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-cloak v-if="selectedOutsideAttendanceTodo">
                        <div class="mb-3 text-end">
                            <span class="badge badge-danger">Di Luar Area</span>
                        </div>
                        <div>
                            <div class="row mb-3">
                                <div class="col-sm-4">Nama Pegawai</div>
                                <div class="col-sm-8"><strong>@{{ selectedOutsideAttendanceTodo?.employee?.name || '-' }}</strong></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">Tanggal</div>
                                <div class="col-sm-8"><strong>@{{ selectedOutsideAttendanceTodo?.date }}</strong></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">Perusahaan</div>
                                <div class="col-sm-8"><strong>@{{ selectedOutsideAttendanceTodo?.employee?.office?.division?.company?.name || '-' }}</strong></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">Divisi</div>
                                <div class="col-sm-8"><strong>@{{ selectedOutsideAttendanceTodo?.employee?.office?.division?.name || '-' }}</strong></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">Kantor</div>
                                <div class="col-sm-8"><strong>@{{ selectedOutsideAttendanceTodo?.employee?.office?.name || '-' }}</strong></div>
                            </div>
                        </div>
                        <div class="separator my-5"></div>
                        <div>
                            <label class="form-label"><strong>Catatan</strong></label>
                            <textarea v-model="model.outsideAttendanceSettlementNote" class="form-control form-control-sm" rows="3" placeholder="Masukkan catatan"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" :data-kt-indicator="saveOutsideAttendanceTodosLoading ? 'on' : null" class="btn btn-primary" @click="saveOutsideAttendanceTodoConfirmation()" :disabled="saveOutsideAttendanceTodosLoading">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">Menyimpan...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Issue Confirmation Modal -->
    <!-- Issue Confirmation Modal -->
    <div class="modal fade" id="runningActivityConfirmationModal" tabindex="-1" aria-labelledby="runningActivityConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="runningActivityConfirmationModalLabel">Penyelesaian</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-cloak v-if="selectedRunningActivityTodo">
                        <div class="mb-3 text-end">
                            <span class="badge badge-danger">Di Luar Area</span>
                        </div>
                        <div>
                            <div class="row mb-3">
                                <div class="col-sm-4">Nama Pegawai</div>
                                <div class="col-sm-8"><strong>@{{ selectedRunningActivityTodo?.employee?.name || '-' }}</strong></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">Tanggal</div>
                                <div class="col-sm-8"><strong>@{{ selectedRunningActivityTodo?.date }}</strong></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">Perusahaan</div>
                                <div class="col-sm-8"><strong>@{{ selectedRunningActivityTodo?.employee?.office?.division?.company?.name || '-' }}</strong></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">Divisi</div>
                                <div class="col-sm-8"><strong>@{{ selectedRunningActivityTodo?.employee?.office?.division?.name || '-' }}</strong></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4">Kantor</div>
                                <div class="col-sm-8"><strong>@{{ selectedRunningActivityTodo?.employee?.office?.name || '-' }}</strong></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">Detail</div>
                                <div class="col-sm-8">
                                    <ul>
                                        <li>
                                            <span>Waktu Check In: </span><strong>@{{ selectedRunningActivityTodo?.check_in_time || '-' }}</strong>
                                        </li>
                                        <li>
                                            <span>Catatan: </span><strong>@{{ selectedRunningActivityTodo?.check_in_note || '-' }}</strong>
                                        </li>
                                        <li>
                                            <span>Lokasi: </span><strong>@{{ selectedRunningActivityTodo?.check_in_location || '-' }}</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="separator my-5"></div>
                        <div>
                            <label class="form-label"><strong>Catatan</strong></label>
                            <textarea v-model="model.runningActivitySettlementNote" class="form-control form-control-sm" rows="3" placeholder="Masukkan catatan"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" :data-kt-indicator="saveRunningActivityTodosLoading ? 'on' : null" class="btn btn-primary" @click="saveRunningActivityTodoConfirmation()" :disabled="saveRunningActivityTodosLoading">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">Menyimpan...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Issue Confirmation Modal -->
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection

@section('pagescript')
<script>
    $(function() {
        const actionTable = $('#action_table').DataTable({
            ordering: false,
        });

        const actionFilterSearch = document.querySelector('[data-kt-action-table-filter="search"]');
        actionFilterSearch.addEventListener('keyup', function(e) {
            actionTable.search(e.target.value).draw();
        });

        const datatable = $('#attendance_table').DataTable({
            ordering: false,
        });

        const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });
    })
</script>
<script>
    const divisions = <?php echo Illuminate\Support\Js::from($divisions) ?>;
    const offices = <?php echo Illuminate\Support\Js::from($offices) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                lateAttendanceTodos: [],
                lateAttendanceTodosLoading: false,
                saveLateAttendanceTodosLoading: false,
                outsideAttendanceTodos: [],
                outsideAttendanceTodosLoading: false,
                saveOutsideAttendanceTodosLoading: false,
                runningActivityTodos: [],
                runningActivityTodosLoading: false,
                saveRunningActivityTodosLoading: false,
                model: {
                    lateAttendanceSearchKeyword: '',
                    lateAttendanceSettlementNote: '',
                    outsideAttendanceSearchKeyword: '',
                    outsideAttendanceSettlementNote: '',
                    runningActivitySearchKeyword: '',
                    runningActivitySettlementNote: '',
                },
                divisions,
                offices,
                filter: {
                    companyId: '{{ $filter["company_id"] }}',
                    divisionId: '{{ $filter["division_id"] }}',
                    officeId: '{{ $filter["office_id"] }}',
                    status: '{{ $filter["status"] }}',
                },
                selectedLateAttendanceTodo: null,
                selectedOutsideAttendanceTodo: null,
                selectedRunningActivityTodo: null,
            }
        },
        mounted() {
            this.getLateAttendanceTodos();
            this.getOutsideAttendanceTodos();
            this.getRunningActivityTodos();
        },
        methods: {
            applyFilter(customStatus) {
                const {
                    companyId,
                    divisionId,
                    officeId,
                    status
                } = this.filter;

                let filterStatus = status;

                if (customStatus) {
                    filterStatus = customStatus;
                }

                const url = `/dashboard?company_id=${companyId}&division_id=${divisionId}&office_id=${officeId}&status=${filterStatus}`;

                document.location.href = url;
            },
            async getLateAttendanceTodos() {
                const self = this;
                try {
                    self.lateAttendanceTodosLoading = true;

                    const response = await axios.get(`/dashboard/late-attendance-todos`);

                    if (response) {
                        const data = response?.data?.data || [];
                        self.lateAttendanceTodos = data;
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.lateAttendanceTodosLoading = false
                }
            },
            async getLateAttendanceTodos() {
                const self = this;
                try {
                    self.lateAttendanceTodosLoading = true;

                    const response = await axios.get(`/dashboard/late-attendance-todos`);

                    if (response) {
                        const data = response?.data?.data || [];
                        self.lateAttendanceTodos = data;
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.lateAttendanceTodosLoading = false
                }
            },
            async getOutsideAttendanceTodos() {
                const self = this;
                try {
                    self.outsideAttendanceTodosLoading = true;

                    const response = await axios.get(`/dashboard/outside-attendance-todos`);

                    if (response) {
                        const data = response?.data?.data || [];
                        self.outsideAttendanceTodos = data;
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.outsideAttendanceTodosLoading = false
                }
            },
            async getRunningActivityTodos() {
                const self = this;
                try {
                    self.runningActivityTodosLoading = true;

                    const response = await axios.get(`/dashboard/running-activity-todos`);

                    if (response) {
                        const data = response?.data?.data || [];
                        self.runningActivityTodos = data;
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.runningActivityTodosLoading = false
                }
            },
            onClickLateAttendanceConfirmation(id) {
                const self = this;
                const [selectedLateAttendanceTodo] = self.lateAttendanceTodos.filter(lateAttendanceTodo => {
                    return lateAttendanceTodo.id == id;
                });

                if (selectedLateAttendanceTodo) {
                    self.selectedLateAttendanceTodo = selectedLateAttendanceTodo;
                }

                var myModal = new bootstrap.Modal(document.getElementById('lateAttendanceConfirmationModal'), {
                    keyboard: false
                });
                myModal.show();
            },
            onClickOutsideAttendanceConfirmation(id) {
                const self = this;
                const [selectedOutsideAttendanceTodo] = self.outsideAttendanceTodos.filter(outsideAttendanceTodo => {
                    return outsideAttendanceTodo.id == id;
                });

                if (selectedOutsideAttendanceTodo) {
                    self.selectedOutsideAttendanceTodo = selectedOutsideAttendanceTodo;
                }

                var myModal = new bootstrap.Modal(document.getElementById('outsideAttendanceConfirmationModal'), {
                    keyboard: false
                });
                myModal.show();
            },
            onClickRunningActivityConfirmation(id) {
                const self = this;
                const [selectedRunningActivityTodo] = self.runningActivityTodos.filter(runningActivityTodo => {
                    return runningActivityTodo.id == id;
                });

                if (selectedRunningActivityTodo) {
                    self.selectedRunningActivityTodo = selectedRunningActivityTodo;
                }

                var myModal = new bootstrap.Modal(document.getElementById('runningActivityConfirmationModal'), {
                    keyboard: false
                });
                myModal.show();
            },
            async saveLateAttendanceTodoConfirmation() {
                const self = this;
                try {
                    // return self.closeModal('#lateAttendanceConfirmationModal');
                    self.saveLateAttendanceTodosLoading = true;

                    if (!self.model.lateAttendanceSettlementNote) {
                        return toastr.warning('Catatan harus diisi');
                    }

                    const payload = {
                        note: self.model.lateAttendanceSettlementNote,
                        source: 'late_attendance',
                        source_id: self.selectedLateAttendanceTodo?.id || null,
                    }

                    const response = await axios.post(`/issue-settlements`, payload);

                    if (response) {
                        // const data = response?.data?.data || [];
                        const message = response?.data?.message || 'Konfirmasi berhasil disimpan';
                        // self.runningActivityTodos = data;
                        // console.log(response);
                        toastr.success(message);
                        self.lateAttendanceTodos = self.lateAttendanceTodos.filter(lateAttendanceTodo => {
                            return lateAttendanceTodo.id != payload.source_id;
                        });
                        self.closeModal('#lateAttendanceConfirmationModal');
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.saveLateAttendanceTodosLoading = false
                }
            },
            async saveOutsideAttendanceTodoConfirmation() {
                const self = this;
                try {
                    self.saveOutsideAttendanceTodosLoading = true;

                    if (!self.model.outsideAttendanceSettlementNote) {
                        return toastr.warning('Catatan harus diisi');
                    }

                    const payload = {
                        note: self.model.outsideAttendanceSettlementNote,
                        source: 'outside_attendance',
                        source_id: self.selectedOutsideAttendanceTodo?.id || null,
                    }

                    const response = await axios.post(`/issue-settlements`, payload);

                    if (response) {
                        // const data = response?.data?.data || [];
                        const message = response?.data?.message || 'Konfirmasi berhasil disimpan';
                        // self.runningActivityTodos = data;
                        // console.log(response);
                        toastr.success(message);
                        self.outsideAttendanceTodos = self.outsideAttendanceTodos.filter(outsideAttendanceTodo => {
                            return outsideAttendanceTodo.id != payload.source_id;
                        });
                        self.closeModal('#outsideAttendanceConfirmationModal');
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.saveOutsideAttendanceTodosLoading = false
                }
            },
            async saveRunningActivityTodoConfirmation() {
                const self = this;
                try {
                    self.saveRunningActivityTodosLoading = true;

                    if (!self.model.runningActivitySettlementNote) {
                        return toastr.warning('Catatan harus diisi');
                    }

                    const payload = {
                        note: self.model.runningActivitySettlementNote,
                        source: 'running_activity',
                        source_id: self.selectedRunningActivityTodo?.id || null,
                    }

                    const response = await axios.post(`/issue-settlements`, payload);

                    if (response) {
                        // const data = response?.data?.data || [];
                        const message = response?.data?.message || 'Konfirmasi berhasil disimpan';
                        // self.runningActivityTodos = data;
                        // console.log(response);
                        toastr.success(message);
                        self.runningActivityTodos = self.runningActivityTodos.filter(runningActivityTodo => {
                            return runningActivityTodo.id != payload.source_id;
                        });
                        self.closeModal('#runningActivityConfirmationModal');
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.saveRunningActivityTodosLoading = false
                }
            },
            closeModal(modalId) {
                const element = document.querySelector(modalId);
                console.log(element);
                const modal = bootstrap.Modal.getInstance(element);
                console.log(modal);
                modal.hide();
            }
        },
        computed: {
            filteredLateAttendanceTodos() {
                const self = this;
                return this.lateAttendanceTodos.filter(lateAttendanceTodo => {
                    const employeeName = lateAttendanceTodo?.employee?.name.toLowerCase();
                    const searchKeyword = self.model.lateAttendanceSearchKeyword.toLowerCase();
                    return employeeName.toLowerCase().indexOf(self.model.lateAttendanceSearchKeyword.toLowerCase()) > -1;
                });
            },
            filteredOutsideAttendanceTodos() {
                const self = this;
                return this.outsideAttendanceTodos.filter(outsideAttendanceTodo => {
                    const employeeName = outsideAttendanceTodo?.employee?.name.toLowerCase();
                    const searchKeyword = self.model.outsideAttendanceSearchKeyword.toLowerCase();
                    return employeeName.toLowerCase().indexOf(self.model.outsideAttendanceSearchKeyword.toLowerCase()) > -1;
                });
            },
            filteredRunningActivityTodos() {
                const self = this;
                return this.runningActivityTodos.filter(runningActivityTodo => {
                    const employeeName = runningActivityTodo?.employee?.name.toLowerCase();
                    const searchKeyword = self.model.runningActivitySearchKeyword.toLowerCase();
                    return employeeName.toLowerCase().indexOf(self.model.runningActivitySearchKeyword.toLowerCase()) > -1;
                });
            },
            filteredDivisions() {
                const companyId = this.filter.companyId;
                return this.divisions.filter(division => division.company_id == companyId);
            },
            filteredOffices() {
                const divisionId = this.filter.divisionId;
                return this.offices.filter(office => office.division_id == divisionId);
            }
        },
        watch: {
            'filter.companyId': function(newValue, oldValue) {
                this.filter.divisionId = '';
                this.filter.officeId = '';
            },
            'filter.divisionId': function(newValue, oldValue) {
                this.filter.officeId = '';
            },
        }

    })
</script>
@endsection