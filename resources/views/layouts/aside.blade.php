<?php
$groupPermissions = auth()->user()->group->permissions ?? "[]";
$permissions = json_decode($groupPermissions);
// dd($permissions);
?>
<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    <!--begin::Brand-->
    <div class="aside-logo flex-column-auto" id="kt_aside_logo">
        <!--begin::Logo-->
        <a href="/" class="text-white fs-1">
            <!-- <img alt="Logo" src="{{asset('assets/media/logos/logo-1-dark.svg')}}" class="h-25px logo" /> -->
            <span class="logo">Laundry<strong class="text-success">HR</strong></span>
        </a>
        <!--end::Logo-->
        <!--begin::Aside toggler-->
        <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr079.svg-->
            <span class="svg-icon svg-icon-1 rotate-180">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="black" />
                    <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="black" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Aside toggler-->
    </div>
    <!--end::Brand-->
    <!--begin::Aside menu-->
    <div class="aside-menu flex-column-fluid">
        <!--begin::Aside Menu-->
        <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
            <!--begin::Menu-->
            <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true" data-kt-menu-expand="false">
                <?php

                use Illuminate\Support\Facades\Auth;

                $isDashboardRoute = request()->is('/') || request()->is('/dashboard');
                $isDashboardRouteGroup = $isDashboardRoute;
                ?>
                @if(in_array('view_dashboard', $permissions))
                <div class="menu-item">
                    <a class="menu-link <?= $isDashboardRoute ? 'active' : '' ?>" href="/">
                        <span class="menu-icon">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect x="2" y="2" width="9" height="9" rx="2" fill="black" />
                                    <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="black" />
                                    <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="black" />
                                    <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>
                @endif
                <div class="menu-item">
                    <div class="menu-content pt-8 pb-2">
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">MENU</span>
                    </div>
                </div>
                <!-- begin:employee -->
                <?php
                $isEmployeesRoute = request()->is('employees*');
                $isEmployeeRouteGroup = $isEmployeesRoute;
                ?>
                @can('view', App\Models\Employee::class)
                <div class="menu-item">
                    <a class="menu-link <?= $isEmployeesRoute ? 'active' : '' ?>" href="/employees">
                        <span class="menu-icon">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M6.28548 15.0861C7.34369 13.1814 9.35142 12 11.5304 12H12.4696C14.6486 12 16.6563 13.1814 17.7145 15.0861L19.3493 18.0287C20.0899 19.3618 19.1259 21 17.601 21H6.39903C4.87406 21 3.91012 19.3618 4.65071 18.0287L6.28548 15.0861Z" fill="black" />
                                    <rect opacity="0.3" x="8" y="3" width="8" height="8" rx="4" fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Pegawai</span>
                    </a>
                </div>
                @endcan
                <!-- end:employee -->
                <!-- begin:company -->
                <?php
                $isCompaniesRoute = request()->is('companies*');
                $isDivisionsRoute = request()->is('divisions*');
                $isOfficesRoute = request()->is('offices*');
                $isStructuresRoute = request()->is('structures*');
                $isCompanyRouteGroup = $isCompaniesRoute || $isDivisionsRoute || $isOfficesRoute || $isStructuresRoute;

                $canViewStructure = Auth::user()->can('view', App\Models\Department::class) || Auth::user()->can('view', App\Models\Designation::class) || Auth::user()->can('view', App\Models\JobTitle::class);

                $hasCompanyGroupPermission = auth()->user()->can('view', App\Models\Company::class) || auth()->user()->can('view', App\Models\Division::class) || auth()->user()->can('view', App\Models\Office::class) || $canViewStructure;
                ?>
                @if($hasCompanyGroupPermission)
                <div data-kt-menu-trigger="click" class="menu-item <?= $isCompanyRouteGroup ? 'here show' : '' ?> menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="black" />
                                    <path d="M8.70001 6C8.10001 5.7 7.39999 5.40001 6.79999 5.10001C7.79999 4.00001 8.90001 3 10.1 2.2C10.7 2.1 11.4 2 12 2C12.7 2 13.3 2.1 13.9 2.2C12 3.2 10.2 4.5 8.70001 6ZM12 8.39999C13.9 6.59999 16.2 5.30001 18.7 4.60001C18.1 4.00001 17.4 3.6 16.7 3.2C14.4 4.1 12.2 5.40001 10.5 7.10001C11 7.50001 11.5 7.89999 12 8.39999ZM7 20C7 20.2 7 20.4 7 20.6C6.2 20.1 5.49999 19.6 4.89999 19C4.59999 18 4.00001 17.2 3.20001 16.6C2.80001 15.8 2.49999 15 2.29999 14.1C4.99999 14.7 7 17.1 7 20ZM10.6 9.89999C8.70001 8.09999 6.39999 6.9 3.79999 6.3C3.39999 6.9 2.99999 7.5 2.79999 8.2C5.39999 8.6 7.7 9.80001 9.5 11.6C9.8 10.9 10.2 10.4 10.6 9.89999ZM2.20001 10.1C2.10001 10.7 2 11.4 2 12C2 12 2 12 2 12.1C4.3 12.4 6.40001 13.7 7.60001 15.6C7.80001 14.8 8.09999 14.1 8.39999 13.4C6.89999 11.6 4.70001 10.4 2.20001 10.1ZM11 20C11 14 15.4 9.00001 21.2 8.10001C20.9 7.40001 20.6 6.8 20.2 6.2C13.8 7.5 9 13.1 9 19.9C9 20.4 9.00001 21 9.10001 21.5C9.80001 21.7 10.5 21.8 11.2 21.9C11.1 21.3 11 20.7 11 20ZM19.1 19C19.4 18 20 17.2 20.8 16.6C21.2 15.8 21.5 15 21.7 14.1C19 14.7 16.9 17.1 16.9 20C16.9 20.2 16.9 20.4 16.9 20.6C17.8 20.2 18.5 19.6 19.1 19ZM15 20C15 15.9 18.1 12.6 22 12.1C22 12.1 22 12.1 22 12C22 11.3 21.9 10.7 21.8 10.1C16.8 10.7 13 14.9 13 20C13 20.7 13.1 21.3 13.2 21.9C13.9 21.8 14.5 21.7 15.2 21.5C15.1 21 15 20.5 15 20Z" fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Perusahaan</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        @can('view', App\Models\Company::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isCompaniesRoute ? 'active' : '' ?>" href="/companies">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Perusahaan</span>
                            </a>
                        </div>
                        @endcan
                        @can('view', App\Models\Division::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isDivisionsRoute ? 'active' : '' ?>" href="/divisions">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Divisi</span>
                            </a>
                        </div>
                        @endcan
                        @can('view', App\Models\Office::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isOfficesRoute ? 'active' : '' ?>" href="/offices">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Kantor</span>
                            </a>
                        </div>
                        @endcan
                        @if ($canViewStructure)
                        <div class="menu-item">
                            <a class="menu-link <?= $isStructuresRoute ? 'active' : '' ?>" href="/structures">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Struktur Organisasi</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                <!-- end:company -->
                <!-- begin:kehadiran -->
                <?php
                $isAttendancesRoute = request()->is('attendances');
                $isImportAttendancesRoute = request()->is('attendances/action/upload');
                $isTimeOffsRoute = request()->is('time-offs*');
                $isLeavesRoute = request()->is('leaves*');
                $isWorkingPatternsRoute = request()->is('working-patterns*');
                $isOvertimeApplicationRoute = request()->is('overtime-applications-v2*');
                $isWorkScheduleRoute = request()->is('work-schedules*');
                $isAttendanceRouteGroup = $isAttendancesRoute || $isTimeOffsRoute || $isWorkingPatternsRoute || $isLeavesRoute || $isImportAttendancesRoute || $isOvertimeApplicationRoute || $isWorkScheduleRoute;

                $canViewTimeOff = Auth::user()->can('view', App\Models\SickApplication::class) || Auth::user()->can('view', App\Models\LeaveApplication::class);

                $hasAttendanceGroupPermission = auth()->user()->can('view', App\Models\Attendance::class) || auth()->user()->can('create', App\Models\Attendance::class) || $canViewTimeOff || auth()->user()->can('view', App\Models\Leave::class) || auth()->user()->can('view', App\Models\WorkingPattern::class);
                ?>
                @if($hasAttendanceGroupPermission)
                <div data-kt-menu-trigger="click" class="menu-item <?= $isAttendanceRouteGroup ? 'show here' : '' ?> menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="black" />
                                    <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="black" />
                                    <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Kehadiran</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        @can('view', App\Models\Attendance::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isAttendancesRoute ? 'active' : '' ?>" href="/attendances?status=1">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Kehadiran</span>
                            </a>
                        </div>
                        @endcan
                        @can('create', App\Models\Attendance::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isImportAttendancesRoute ? 'active' : '' ?>" href="/attendances/action/upload">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Impor Kehadiran</span>
                            </a>
                        </div>
                        @endcan
                        @php
                        $canViewTimeOff = Auth::user()->can('view', App\Models\SickApplication::class) || Auth::user()->can('view', App\Models\LeaveApplication::class)
                        @endphp
                        @if ($canViewTimeOff)
                        <div class="menu-item">
                            <a class="menu-link <?= $isTimeOffsRoute ? 'active' : '' ?>" href="/time-offs">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Time Off</span>
                            </a>
                        </div>
                        @endif
                        @can('view', App\Models\Leave::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isLeavesRoute ? 'active' : '' ?>" href="/leaves">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Data Cuti</span>
                            </a>
                        </div>
                        @endcan
                        @can('view', App\Models\WorkingPattern::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isWorkingPatternsRoute ? 'active' : '' ?>" href="/working-patterns">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Pola Kerja</span>
                            </a>
                        </div>
                        @endcan
                        @if(in_array('view_overtime_application', $permissions))
                        <div class="menu-item">
                            <a class="menu-link <?= $isOvertimeApplicationRoute ? 'active' : '' ?>" href="/overtime-applications-v2">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Pengajuan Lembur</span>
                            </a>
                        </div>
                        @endif
                        @if(in_array('view_work_schedule', $permissions))
                        <div class="menu-item">
                            <a class="menu-link <?= $isWorkScheduleRoute ? 'active' : '' ?>" href="/work-schedules">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Jadwal Kerja</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                <!-- end:kehadiran -->
                <!-- begin:penggajian -->
                <?php
                $isMonthlyPayrollRoute = request()->is('payrolls/monthly*');
                $isMagentaDailyPayrollRoute = request()->is('payrolls/daily') || request()->is('payrolls/daily/show-by-period*') || request()->is('payrolls/daily/generate*');
                $isAerplusDailyPayrollRoute = request()->is('payrolls/daily-aerplus*');
                $isSalaryComponentRoute = request()->is('settings/payrolls/components');
                $isSalaryValueRoute = request()->is('salary-values');
                $isSalaryIncreaseRoute = request()->is('salary-increases*');
                $isThrRoute = request()->is('thr*');
                $isPayrollBca = request()->is('payroll-bca*');
                // $isInsuranceRoute = request()->is('insurances/create*');
                $isPayrollRouteGroup = $isMonthlyPayrollRoute || $isMagentaDailyPayrollRoute || $isAerplusDailyPayrollRoute || $isSalaryIncreaseRoute || $isSalaryComponentRoute || $isSalaryValueRoute || $isThrRoute || $isPayrollBca;

                $hasPayrollGroupPermission = auth()->user()->can('view', App\Models\Salary::class) || auth()->user()->can('view', App\Models\DailySalary::class) || auth()->user()->can('viewAerplus', App\Models\DailySalary::class) || auth()->user()->can('view', App\Models\SalaryComponent::class) || auth()->user()->can('viewSalaryValue', App\Models\Salary::class) || auth()->user()->can('viewSalaryIncrease', App\Models\Salary::class) || auth()->user()->can('viewThr', App\Models\Salary::class);
                ?>
                @if($hasPayrollGroupPermission)
                <div data-kt-menu-trigger="click" class="menu-item <?= $isPayrollRouteGroup ? 'show here' : '' ?> menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M18 21.6C16.3 21.6 15 20.3 15 18.6V2.50001C15 2.20001 14.6 1.99996 14.3 2.19996L13 3.59999L11.7 2.3C11.3 1.9 10.7 1.9 10.3 2.3L9 3.59999L7.70001 2.3C7.30001 1.9 6.69999 1.9 6.29999 2.3L5 3.59999L3.70001 2.3C3.50001 2.1 3 2.20001 3 3.50001V18.6C3 20.3 4.3 21.6 6 21.6H18Z" fill="black" />
                                    <path d="M12 12.6H11C10.4 12.6 10 12.2 10 11.6C10 11 10.4 10.6 11 10.6H12C12.6 10.6 13 11 13 11.6C13 12.2 12.6 12.6 12 12.6ZM9 11.6C9 11 8.6 10.6 8 10.6H6C5.4 10.6 5 11 5 11.6C5 12.2 5.4 12.6 6 12.6H8C8.6 12.6 9 12.2 9 11.6ZM9 7.59998C9 6.99998 8.6 6.59998 8 6.59998H6C5.4 6.59998 5 6.99998 5 7.59998C5 8.19998 5.4 8.59998 6 8.59998H8C8.6 8.59998 9 8.19998 9 7.59998ZM13 7.59998C13 6.99998 12.6 6.59998 12 6.59998H11C10.4 6.59998 10 6.99998 10 7.59998C10 8.19998 10.4 8.59998 11 8.59998H12C12.6 8.59998 13 8.19998 13 7.59998ZM13 15.6C13 15 12.6 14.6 12 14.6H10C9.4 14.6 9 15 9 15.6C9 16.2 9.4 16.6 10 16.6H12C12.6 16.6 13 16.2 13 15.6Z" fill="black" />
                                    <path d="M15 18.6C15 20.3 16.3 21.6 18 21.6C19.7 21.6 21 20.3 21 18.6V12.5C21 12.2 20.6 12 20.3 12.2L19 13.6L17.7 12.3C17.3 11.9 16.7 11.9 16.3 12.3L15 13.6V18.6Z" fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Penggajian</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        @can('view', App\Models\Salary::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isMonthlyPayrollRoute ? 'active' : '' ?>" href="/payrolls/monthly">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Gaji Bulanan</span>
                            </a>
                        </div>
                        @endcan
                        @can('view', App\Models\DailySalary::class)
                        <!-- <div class="menu-item">
                            <a class="menu-link <?= $isMagentaDailyPayrollRoute ? 'active' : '' ?>" href="/payrolls/daily">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Gaji Harian Magenta</span>
                            </a>
                        </div> -->
                        @endcan
                        @can('viewAerplus', App\Models\DailySalary::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isAerplusDailyPayrollRoute ? 'active' : '' ?>" href="/payrolls/daily-aerplus">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Gaji Harian</span>
                            </a>
                        </div>
                        @endcan
                        <div class="menu-item">
                            <a class="menu-link <?= $isPayrollBca ? 'active' : '' ?>" href="/payroll-bca">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Payroll BCA</span>
                            </a>
                        </div>
                        @can('view', App\Models\SalaryComponent::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isSalaryComponentRoute ? 'active' : '' ?>" href="/settings/payrolls/components">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Komponen Gaji</span>
                            </a>
                        </div>
                        @endcan
                        @can('viewSalaryValue', App\Models\Salary::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isSalaryValueRoute ? 'active' : '' ?>" href="/salary-values">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Nilai Gaji</span>
                            </a>
                        </div>
                        @endcan
                        @can('viewSalaryIncrease', App\Models\Salary::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isSalaryIncreaseRoute ? 'active' : '' ?>" href="/salary-increases">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Kenaikan Gaji</span>
                            </a>
                        </div>
                        @endcan
                        @can('viewThr', App\Models\Salary::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isThrRoute ? 'active' : '' ?>" href="/thr/create">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Tunjangan Hari Raya</span>
                            </a>
                        </div>
                        @endcan

                    </div>
                </div>
                @endif
                <!-- end:penggajian -->
                <!-- begin:pinjaman -->
                <?php
                $isLoansRoute = request()->is('loans*');
                $isLoanRouteGroup = $isLoansRoute;
                ?>
                @can('view', App\Models\Loan::class)
                <div class="menu-item">
                    <a class="menu-link <?= $isLoanRouteGroup ? 'active' : '' ?>" href="/loans">
                        <span class="menu-icon">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M20 19.725V18.725C20 18.125 19.6 17.725 19 17.725H5C4.4 17.725 4 18.125 4 18.725V19.725H3C2.4 19.725 2 20.125 2 20.725V21.725H22V20.725C22 20.125 21.6 19.725 21 19.725H20Z" fill="black" />
                                    <path opacity="0.3" d="M22 6.725V7.725C22 8.325 21.6 8.725 21 8.725H18C18.6 8.725 19 9.125 19 9.725C19 10.325 18.6 10.725 18 10.725V15.725C18.6 15.725 19 16.125 19 16.725V17.725H15V16.725C15 16.125 15.4 15.725 16 15.725V10.725C15.4 10.725 15 10.325 15 9.725C15 9.125 15.4 8.725 16 8.725H13C13.6 8.725 14 9.125 14 9.725C14 10.325 13.6 10.725 13 10.725V15.725C13.6 15.725 14 16.125 14 16.725V17.725H10V16.725C10 16.125 10.4 15.725 11 15.725V10.725C10.4 10.725 10 10.325 10 9.725C10 9.125 10.4 8.725 11 8.725H8C8.6 8.725 9 9.125 9 9.725C9 10.325 8.6 10.725 8 10.725V15.725C8.6 15.725 9 16.125 9 16.725V17.725H5V16.725C5 16.125 5.4 15.725 6 15.725V10.725C5.4 10.725 5 10.325 5 9.725C5 9.125 5.4 8.725 6 8.725H3C2.4 8.725 2 8.325 2 7.725V6.725L11 2.225C11.6 1.925 12.4 1.925 13.1 2.225L22 6.725ZM12 3.725C11.2 3.725 10.5 4.425 10.5 5.225C10.5 6.025 11.2 6.725 12 6.725C12.8 6.725 13.5 6.025 13.5 5.225C13.5 4.425 12.8 3.725 12 3.725Z" fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Pinjaman</span>
                    </a>
                </div>
                <!-- end:pinjaman -->
                @endcan
                <!-- begin:pinjaman -->
                <?php
                $isAnnouncementsRoute = request()->is('announcements*');
                $isAnnouncementRouteGroup = $isAnnouncementsRoute;
                ?>
                @if(in_array('view_announcement', $permissions))
                <div class="menu-item">
                    <a class="menu-link <?= $isAnnouncementRouteGroup ? 'active' : '' ?>" href="/announcements">
                        <span class="menu-icon">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor" />
                                    <path d="M19 10.4C19 10.3 19 10.2 19 10C19 8.9 18.1 8 17 8H16.9C15.6 6.2 14.6 4.29995 13.9 2.19995C13.3 2.09995 12.6 2 12 2C11.9 2 11.8 2 11.7 2C12.4 4.6 13.5 7.10005 15.1 9.30005C15 9.50005 15 9.7 15 10C15 11.1 15.9 12 17 12C17.1 12 17.3 12 17.4 11.9C18.6 13 19.9 14 21.4 14.8C21.4 14.8 21.5 14.8 21.5 14.9C21.7 14.2 21.8 13.5 21.9 12.7C20.9 12.1 19.9 11.3 19 10.4Z" fill="currentColor" />
                                    <path d="M12 15C11 13.1 10.2 11.2 9.60001 9.19995C9.90001 8.89995 10 8.4 10 8C10 7.1 9.40001 6.39998 8.70001 6.09998C8.40001 4.99998 8.20001 3.90005 8.00001 2.80005C7.30001 3.10005 6.70001 3.40002 6.20001 3.90002C6.40001 4.80002 6.50001 5.6 6.80001 6.5C6.40001 6.9 6.10001 7.4 6.10001 8C6.10001 9 6.80001 9.8 7.80001 10C8.30001 11.6 9.00001 13.2 9.70001 14.7C7.10001 13.2 4.70001 11.5 2.40001 9.5C2.20001 10.3 2.10001 11.1 2.10001 11.9C4.60001 13.9 7.30001 15.7 10.1 17.2C10.2 18.2 11 19 12 19C12.6 20 13.2 20.9 13.9 21.8C14.6 21.7 15.3 21.5 15.9 21.2C15.4 20.5 14.9 19.8 14.4 19.1C15.5 19.5 16.5 19.9 17.6 20.2C18.3 19.8 18.9 19.2 19.4 18.6C17.6 18.1 15.7 17.5 14 16.7C13.9 15.8 13.1 15 12 15Z" fill="currentColor" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Pengumuman</span>
                    </a>
                </div>
                @endif
                <!-- end:pinjaman -->
                <!-- begin:deposit -->
                <?php
                $isSalaryDepositRoute = request()->is('salary-deposits*');
                $isSalaryDepositRouteGroup = $isSalaryDepositRoute;
                ?>
                @if(in_array('view_deposit', $permissions))
                <div class="menu-item">
                    <a class="menu-link <?= $isSalaryDepositRoute ? 'active' : '' ?>" href="/salary-deposits">
                        <span class="menu-icon">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M3.20001 5.91897L16.9 3.01895C17.4 2.91895 18 3.219 18.1 3.819L19.2 9.01895L3.20001 5.91897Z" fill="currentColor" />
                                    <path opacity="0.3" d="M13 13.9189C13 12.2189 14.3 10.9189 16 10.9189H21C21.6 10.9189 22 11.3189 22 11.9189V15.9189C22 16.5189 21.6 16.9189 21 16.9189H16C14.3 16.9189 13 15.6189 13 13.9189ZM16 12.4189C15.2 12.4189 14.5 13.1189 14.5 13.9189C14.5 14.7189 15.2 15.4189 16 15.4189C16.8 15.4189 17.5 14.7189 17.5 13.9189C17.5 13.1189 16.8 12.4189 16 12.4189Z" fill="currentColor" />
                                    <path d="M13 13.9189C13 12.2189 14.3 10.9189 16 10.9189H21V7.91895C21 6.81895 20.1 5.91895 19 5.91895H3C2.4 5.91895 2 6.31895 2 6.91895V20.9189C2 21.5189 2.4 21.9189 3 21.9189H19C20.1 21.9189 21 21.0189 21 19.9189V16.9189H16C14.3 16.9189 13 15.6189 13 13.9189Z" fill="currentColor" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Deposit</span>
                    </a>
                </div>
                @endif
                <!-- end:pinjaman -->
                <!-- begin:insurance -->
                <?php
                $isInsuranceRoute = request()->is('insurance*');
                $isPrivateInsuranceRoute = request()->is('private-insurances*');
                $isInsuranceRouteGroup = $isInsuranceRoute || $isPrivateInsuranceRoute;

                $hasInsuranceGroupPermission = auth()->user()->can('view', App\Models\PrivateInsurance::class) || auth()->user()->can('viewValue', App\Models\PrivateInsurance::class);
                ?>
                <!-- <div class="menu-item">
                    <a class="menu-link <?= $isInsuranceRoute ? 'active' : '' ?>" href="/insurances/create">
                        <span class="menu-icon">

                            <span class="svg-icon svg-icon-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M2.10001 10C3.00001 5.6 6.69998 2.3 11.2 2L8.79999 4.39999L11.1 7C9.60001 7.3 8.30001 8.19999 7.60001 9.59999L4.5 12.4L2.10001 10ZM19.3 11.5L16.4 14C15.7 15.5 14.4 16.6 12.7 16.9L15 19.5L12.6 21.9C17.1 21.6 20.8 18.2 21.7 13.9L19.3 11.5Z" fill="black" />
                                    <path d="M13.8 2.09998C18.2 2.99998 21.5 6.69998 21.8 11.2L19.4 8.79997L16.8 11C16.5 9.39998 15.5 8.09998 14 7.39998L11.4 4.39998L13.8 2.09998ZM12.3 19.4L9.69998 16.4C8.29998 15.7 7.3 14.4 7 12.8L4.39999 15.1L2 12.7C2.3 17.2 5.7 20.9 10 21.8L12.3 19.4Z" fill="black" />
                                </svg>
                            </span>

                        </span>
                        <span class="menu-title">Asuransi</span>
                    </a>
                </div> -->
                @if($hasInsuranceGroupPermission)
                <div data-kt-menu-trigger="click" class="menu-item <?= $isInsuranceRouteGroup ? 'show here' : '' ?> menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                            <span class="svg-icon svg-icon-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M2.10001 10C3.00001 5.6 6.69998 2.3 11.2 2L8.79999 4.39999L11.1 7C9.60001 7.3 8.30001 8.19999 7.60001 9.59999L4.5 12.4L2.10001 10ZM19.3 11.5L16.4 14C15.7 15.5 14.4 16.6 12.7 16.9L15 19.5L12.6 21.9C17.1 21.6 20.8 18.2 21.7 13.9L19.3 11.5Z" fill="black" />
                                    <path d="M13.8 2.09998C18.2 2.99998 21.5 6.69998 21.8 11.2L19.4 8.79997L16.8 11C16.5 9.39998 15.5 8.09998 14 7.39998L11.4 4.39998L13.8 2.09998ZM12.3 19.4L9.69998 16.4C8.29998 15.7 7.3 14.4 7 12.8L4.39999 15.1L2 12.7C2.3 17.2 5.7 20.9 10 21.8L12.3 19.4Z" fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Asuransi</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        @can('view', App\Models\PrivateInsurance::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isPrivateInsuranceRoute ? 'active' : '' ?>" href="/private-insurances">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Jenis Asuransi</span>
                            </a>
                        </div>
                        @endcan
                        @can('viewValue', App\Models\PrivateInsurance::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isInsuranceRoute ? 'active' : '' ?>" href="/insurances/create?type=bpjs_ketenagakerjaan&year={{ date('Y') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Nilai Asuransi</span>
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
                @endif
                <!-- begin:settings -->
                <?php
                $isReportRoute = request()->is('reports*');
                $isReportRouteGroup = $isReportRoute;
                ?>
                @if(in_array('view_report', $permissions))
                <div class="menu-item">
                    <a class="menu-link <?= $isReportRouteGroup ? 'active' : '' ?>" href="/reports">
                        <span class="menu-icon">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M14 3V21H10V3C10 2.4 10.4 2 11 2H13C13.6 2 14 2.4 14 3ZM7 14H5C4.4 14 4 14.4 4 15V21H8V15C8 14.4 7.6 14 7 14Z" fill="black" />
                                    <path d="M21 20H20V8C20 7.4 19.6 7 19 7H17C16.4 7 16 7.4 16 8V20H3C2.4 20 2 20.4 2 21C2 21.6 2.4 22 3 22H21C21.6 22 22 21.6 22 21C22 20.4 21.6 20 21 20Z" fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Laporan</span>
                    </a>
                </div>
                @endif
                <!-- end:settings -->
                <!-- begin:settings -->
                <?php
                $isSettingsRoute = request()->is('settings*');
                $isEventCalendarRoute = request()->is('event-calendars*');
                $isLeaveCategoriesRoute = request()->is('leave-categories*');
                $isGroupRoute = request()->is('credential-groups*');
                $isSettingAttendanceRoute = request()->is('settings/attendance*');
                $isWorkScheduleWorkingPatternRoute = request()->is('work-schedule-working-patterns');
                $isSettingRouteGroup = $isEventCalendarRoute || $isLeaveCategoriesRoute || $isGroupRoute || $isSettingAttendanceRoute || $isWorkScheduleWorkingPatternRoute;

                $hasSettingGroupPermission = auth()->user()->can('view', App\Models\EventCalendar::class) || auth()->user()->can('view', App\Models\LeaveCategory::class) || auth()->user()->can('view', App\Models\CredentialGroup::class);
                ?>
                @if($hasSettingGroupPermission)
                <div data-kt-menu-trigger="click" class="menu-item <?= $isSettingRouteGroup ? 'show here' : '' ?> menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M22.1 11.5V12.6C22.1 13.2 21.7 13.6 21.2 13.7L19.9 13.9C19.7 14.7 19.4 15.5 18.9 16.2L19.7 17.2999C20 17.6999 20 18.3999 19.6 18.7999L18.8 19.6C18.4 20 17.8 20 17.3 19.7L16.2 18.9C15.5 19.3 14.7 19.7 13.9 19.9L13.7 21.2C13.6 21.7 13.1 22.1 12.6 22.1H11.5C10.9 22.1 10.5 21.7 10.4 21.2L10.2 19.9C9.4 19.7 8.6 19.4 7.9 18.9L6.8 19.7C6.4 20 5.7 20 5.3 19.6L4.5 18.7999C4.1 18.3999 4.1 17.7999 4.4 17.2999L5.2 16.2C4.8 15.5 4.4 14.7 4.2 13.9L2.9 13.7C2.4 13.6 2 13.1 2 12.6V11.5C2 10.9 2.4 10.5 2.9 10.4L4.2 10.2C4.4 9.39995 4.7 8.60002 5.2 7.90002L4.4 6.79993C4.1 6.39993 4.1 5.69993 4.5 5.29993L5.3 4.5C5.7 4.1 6.3 4.10002 6.8 4.40002L7.9 5.19995C8.6 4.79995 9.4 4.39995 10.2 4.19995L10.4 2.90002C10.5 2.40002 11 2 11.5 2H12.6C13.2 2 13.6 2.40002 13.7 2.90002L13.9 4.19995C14.7 4.39995 15.5 4.69995 16.2 5.19995L17.3 4.40002C17.7 4.10002 18.4 4.1 18.8 4.5L19.6 5.29993C20 5.69993 20 6.29993 19.7 6.79993L18.9 7.90002C19.3 8.60002 19.7 9.39995 19.9 10.2L21.2 10.4C21.7 10.5 22.1 11 22.1 11.5ZM12.1 8.59998C10.2 8.59998 8.6 10.2 8.6 12.1C8.6 14 10.2 15.6 12.1 15.6C14 15.6 15.6 14 15.6 12.1C15.6 10.2 14 8.59998 12.1 8.59998Z" fill="black" />
                                    <path d="M17.1 12.1C17.1 14.9 14.9 17.1 12.1 17.1C9.30001 17.1 7.10001 14.9 7.10001 12.1C7.10001 9.29998 9.30001 7.09998 12.1 7.09998C14.9 7.09998 17.1 9.29998 17.1 12.1ZM12.1 10.1C11 10.1 10.1 11 10.1 12.1C10.1 13.2 11 14.1 12.1 14.1C13.2 14.1 14.1 13.2 14.1 12.1C14.1 11 13.2 10.1 12.1 10.1Z" fill="black" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Pengaturan</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        @if(in_array('view_setting_attendance', $permissions))
                        <div class="menu-item">
                            <a class="menu-link <?= $isSettingAttendanceRoute ? 'active' : '' ?>" href="/settings/attendance">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Kehadiran</span>
                            </a>
                        </div>
                        @endif
                        @can('view', App\Models\EventCalendar::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isEventCalendarRoute ? 'active' : '' ?>" href="/event-calendars">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Kalender</span>
                            </a>
                        </div>
                        @endcan
                        @can('view', App\Models\LeaveCategory::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isLeaveCategoriesRoute ? 'active' : '' ?>" href="/leave-categories">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Jenis Cuti</span>
                            </a>
                        </div>
                        @endcan
                        <div class="menu-item">
                            <a class="menu-link <?= $isWorkScheduleWorkingPatternRoute ? 'active' : '' ?>" href="/work-schedule-working-patterns">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Shift Jadwal Kerja</span>
                            </a>
                        </div>
                        @can('view', App\Models\CredentialGroup::class)
                        <div class="menu-item">
                            <a class="menu-link <?= $isGroupRoute ? 'active' : '' ?>" href="/credential-groups">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Grup Hak Akses</span>
                            </a>
                        </div>
                        @endcan

                    </div>
                </div>
                @endif
                <!-- <div class="menu-item">
                    <a class="menu-link <?= $isSettingsRoute ? 'active' : '' ?>" href="/settings/payrolls/components">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M22.1 11.5V12.6C22.1 13.2 21.7 13.6 21.2 13.7L19.9 13.9C19.7 14.7 19.4 15.5 18.9 16.2L19.7 17.2999C20 17.6999 20 18.3999 19.6 18.7999L18.8 19.6C18.4 20 17.8 20 17.3 19.7L16.2 18.9C15.5 19.3 14.7 19.7 13.9 19.9L13.7 21.2C13.6 21.7 13.1 22.1 12.6 22.1H11.5C10.9 22.1 10.5 21.7 10.4 21.2L10.2 19.9C9.4 19.7 8.6 19.4 7.9 18.9L6.8 19.7C6.4 20 5.7 20 5.3 19.6L4.5 18.7999C4.1 18.3999 4.1 17.7999 4.4 17.2999L5.2 16.2C4.8 15.5 4.4 14.7 4.2 13.9L2.9 13.7C2.4 13.6 2 13.1 2 12.6V11.5C2 10.9 2.4 10.5 2.9 10.4L4.2 10.2C4.4 9.39995 4.7 8.60002 5.2 7.90002L4.4 6.79993C4.1 6.39993 4.1 5.69993 4.5 5.29993L5.3 4.5C5.7 4.1 6.3 4.10002 6.8 4.40002L7.9 5.19995C8.6 4.79995 9.4 4.39995 10.2 4.19995L10.4 2.90002C10.5 2.40002 11 2 11.5 2H12.6C13.2 2 13.6 2.40002 13.7 2.90002L13.9 4.19995C14.7 4.39995 15.5 4.69995 16.2 5.19995L17.3 4.40002C17.7 4.10002 18.4 4.1 18.8 4.5L19.6 5.29993C20 5.69993 20 6.29993 19.7 6.79993L18.9 7.90002C19.3 8.60002 19.7 9.39995 19.9 10.2L21.2 10.4C21.7 10.5 22.1 11 22.1 11.5ZM12.1 8.59998C10.2 8.59998 8.6 10.2 8.6 12.1C8.6 14 10.2 15.6 12.1 15.6C14 15.6 15.6 14 15.6 12.1C15.6 10.2 14 8.59998 12.1 8.59998Z" fill="black" />
                                    <path d="M17.1 12.1C17.1 14.9 14.9 17.1 12.1 17.1C9.30001 17.1 7.10001 14.9 7.10001 12.1C7.10001 9.29998 9.30001 7.09998 12.1 7.09998C14.9 7.09998 17.1 9.29998 17.1 12.1ZM12.1 10.1C11 10.1 10.1 11 10.1 12.1C10.1 13.2 11 14.1 12.1 14.1C13.2 14.1 14.1 13.2 14.1 12.1C14.1 11 13.2 10.1 12.1 10.1Z" fill="black" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Pengaturan</span>
                    </a>
                </div> -->
                <!-- end:settings -->
                <!-- <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                           
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M6.28548 15.0861C7.34369 13.1814 9.35142 12 11.5304 12H12.4696C14.6486 12 16.6563 13.1814 17.7145 15.0861L19.3493 18.0287C20.0899 19.3618 19.1259 21 17.601 21H6.39903C4.87406 21 3.91012 19.3618 4.65071 18.0287L6.28548 15.0861Z" fill="black" />
                                    <rect opacity="0.3" x="8" y="3" width="8" height="8" rx="4" fill="black" />
                                </svg>
                            </span>
                           
                        </span>
                        <span class="menu-title">Pegawai</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        <div class="menu-item">
                            <a class="menu-link" href="../../demo1/dist/widgets/lists.html">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Data Pegawai</span>
                            </a>
                        </div>
                    </div>
                </div> -->
            </div>
            <!--end::Menu-->
        </div>
        <!--end::Aside Menu-->
    </div>
    <!--end::Aside menu-->
    <!--begin::Footer-->
    <!-- <div class="aside-footer flex-column-auto pt-5 pb-7 px-5" id="kt_aside_footer">
        <a href="../../demo1/dist/documentation/getting-started.html" class="btn btn-custom btn-primary w-100" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss-="click" title="200+ in-house components and 3rd-party plugins">
            <span class="btn-label">Docs &amp; Components</span>
            
            <span class="svg-icon btn-icon svg-icon-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22ZM12.5 18C12.5 17.4 12.6 17.5 12 17.5H8.5C7.9 17.5 8 17.4 8 18C8 18.6 7.9 18.5 8.5 18.5L12 18C12.6 18 12.5 18.6 12.5 18ZM16.5 13C16.5 12.4 16.6 12.5 16 12.5H8.5C7.9 12.5 8 12.4 8 13C8 13.6 7.9 13.5 8.5 13.5H15.5C16.1 13.5 16.5 13.6 16.5 13ZM12.5 8C12.5 7.4 12.6 7.5 12 7.5H8C7.4 7.5 7.5 7.4 7.5 8C7.5 8.6 7.4 8.5 8 8.5H12C12.6 8.5 12.5 8.6 12.5 8Z" fill="black" />
                    <rect x="7" y="17" width="6" height="2" rx="1" fill="black" />
                    <rect x="7" y="12" width="10" height="2" rx="1" fill="black" />
                    <rect x="7" y="7" width="6" height="2" rx="1" fill="black" />
                    <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="black" />
                </svg>
            </span>
           
        </a>
    </div> -->
    <!--end::Footer-->
</div>