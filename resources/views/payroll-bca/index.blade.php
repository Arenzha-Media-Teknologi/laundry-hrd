@extends('layouts.app')

@section('title', 'Payroll BCA')

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
    <div class="d-flex mb-5 justify-content-between align-items-center">
        <h1 class="mb-0">Payroll BCA</h1>
        <div>
            <a href="/payroll-bca-email-log" class="btn btn-secondary"><i class="bi bi-ui-checks"></i> Log Email</a>
            <a href="/payroll-bca-email-log/send-email" class="btn btn-primary"><i class="bi bi-send"></i> Kirim Email</a>
        </div>
        <!-- <div>
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/2560px-Bank_Central_Asia.svg.png" alt="" width="200">
        </div> -->
    </div>
    <div class="row gy-5 g-xl-10">
        <!--begin::Col-->
        <div class="col-sm-6 col-xl-4 mb-xl-10">
            <!--begin::Card widget 2-->
            <div class="card h-lg-100" style="position: relative; overflow: hidden;">
                <!--begin::Body-->
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/2560px-Bank_Central_Asia.svg.png" style="position: absolute; opacity: .1; filter: grayscale(100); z-index: 1; bottom: -10%; right: -5%" width="300">
                <div class="card-body d-flex justify-content-between align-items-start flex-column" style="position: relative; z-index: 2;">
                    <!--begin::Icon-->
                    <div class="m-0">
                        <span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M22 8H8L12 4H19C19.6 4 20.2 4.39999 20.5 4.89999L22 8ZM3.5 19.1C3.8 19.7 4.4 20 5 20H12L16 16H2L3.5 19.1ZM19.1 20.5C19.7 20.2 20 19.6 20 19V12L16 8V22L19.1 20.5ZM4.9 3.5C4.3 3.8 4 4.4 4 5V12L8 16V2L4.9 3.5Z" fill="currentColor" />
                                <path d="M22 8L20 12L16 8H22ZM8 16L4 12L2 16H8ZM16 16L12 20L16 22V16ZM8 8L12 4L8 2V8Z" fill="currentColor" />
                            </svg>
                        </span>

                    </div>
                    <!--end::Icon-->

                    <!--begin::Section-->
                    <div class="d-flex flex-column my-7">
                        <!--begin::Number-->
                        <span class="fw-semibold fs-2x text-gray-800 ls-n2">Gaji Bulanan</span>
                        <!--end::Number-->

                        <!--begin::Follower-->
                        <div class="mt-3">
                            <span class="fw-semibold fs-6 text-gray-500">
                                Payroll untuk gaji bulanan </span>

                        </div>
                        <!--end::Follower-->
                    </div>
                    <!--end::Section-->
                    <a href="/payroll-bca/monthly" class="btn btn-primary" style="background-color: royalblue;">
                        <span class="me-2">Buka</span>
                        <i class="bi bi-chevron-right pe-0"></i>
                    </a>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card widget 2-->


        </div>
        <!--end::Col-->
        <!--begin::Col-->
        <div class="col-sm-6 col-xl-4 mb-xl-10">
            <!--begin::Card widget 2-->
            <div class="card h-lg-100" style="position: relative; overflow: hidden;">
                <!--begin::Body-->
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/2560px-Bank_Central_Asia.svg.png" style="position: absolute; opacity: .1; filter: grayscale(100); z-index: 1; bottom: -10%; right: -5%" width="300">
                <div class="card-body d-flex justify-content-between align-items-start flex-column" style="position: relative; z-index: 2;">
                    <!--begin::Icon-->
                    <div class="m-0">
                        <span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 21.6C16.6 20.4 9.1 20.3 6.3 21.2C5.7 21.4 5.1 21.2 4.7 20.8L2 18C4.2 15.8 10.8 15.1 15.8 15.8C16.2 18.3 17 20.5 18 21.6ZM18.8 2.8C18.4 2.4 17.8 2.20001 17.2 2.40001C14.4 3.30001 6.9 3.2 5.5 2C6.8 3.3 7.4 5.5 7.7 7.7C9 7.9 10.3 8 11.7 8C15.8 8 19.8 7.2 21.5 5.5L18.8 2.8Z" fill="currentColor" />
                                <path opacity="0.3" d="M21.2 17.3C21.4 17.9 21.2 18.5 20.8 18.9L18 21.6C15.8 19.4 15.1 12.8 15.8 7.8C18.3 7.4 20.4 6.70001 21.5 5.60001C20.4 7.00001 20.2 14.5 21.2 17.3ZM8 11.7C8 9 7.7 4.2 5.5 2L2.8 4.8C2.4 5.2 2.2 5.80001 2.4 6.40001C2.7 7.40001 3.00001 9.2 3.10001 11.7C3.10001 15.5 2.40001 17.6 2.10001 18C3.20001 16.9 5.3 16.2 7.8 15.8C8 14.2 8 12.7 8 11.7Z" fill="currentColor" />
                            </svg>
                        </span>

                    </div>
                    <!--end::Icon-->

                    <!--begin::Section-->
                    <div class="d-flex flex-column my-7">
                        <!--begin::Number-->
                        <span class="fw-semibold fs-2x text-gray-800 ls-n2">Gaji Mingguan Magenta</span>
                        <!--end::Number-->

                        <!--begin::Follower-->
                        <div class="mt-3">
                            <span class="fw-semibold fs-6 text-gray-500">
                                Payroll untuk gaji mingguan Magenta </span>

                        </div>
                        <!--end::Follower-->
                    </div>
                    <!--end::Section-->
                    <a href="/payroll-bca/daily-magenta" class="btn btn-primary" style="background-color: royalblue;">
                        <span class="me-2">Buka</span>
                        <i class="bi bi-chevron-right pe-0"></i>
                    </a>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card widget 2-->
        </div>
        <!--end::Col-->
        <!--begin::Col-->
        <div class="col-sm-6 col-xl-4 mb-xl-10">
            <!--begin::Card widget 2-->
            <div class="card h-lg-100" style="position: relative; overflow: hidden;">
                <!--begin::Body-->
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/2560px-Bank_Central_Asia.svg.png" style="position: absolute; opacity: .1; filter: grayscale(100); z-index: 1; bottom: -10%; right: -5%" width="300">
                <div class="card-body d-flex justify-content-between align-items-start flex-column" style="position: relative; z-index: 2;">
                    <!--begin::Icon-->
                    <div class="m-0">
                        <span class="svg-icon svg-icon-muted svg-icon-2hx"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 12C22 12.9 21.9 13.8 21.7 14.6L5 4.89999C6.8 3.09999 9.3 2 12 2C17.5 2 22 6.5 22 12Z" fill="currentColor" />
                                <path opacity="0.3" d="M3.7 17.5C2.6 15.9 2 14 2 12C2 9.20003 3.1 6.70002 5 4.90002L9.3 7.40002V14.2L3.7 17.5ZM17.2 12L5 19.1C6.8 20.9 9.3 22 12 22C16.6 22 20.5 18.8 21.7 14.6L17.2 12Z" fill="currentColor" />
                            </svg>
                        </span>

                    </div>
                    <!--end::Icon-->

                    <!--begin::Section-->
                    <div class="d-flex flex-column my-7">
                        <!--begin::Number-->
                        <span class="fw-semibold fs-2x text-gray-800 ls-n2">Gaji Mingguan AerPlus</span>
                        <!--end::Number-->

                        <!--begin::Follower-->
                        <div class="mt-3">
                            <span class="fw-semibold fs-6 text-gray-500">
                                Payroll untuk gaji mingguan AerPlus </span>

                        </div>
                        <!--end::Follower-->
                    </div>
                    <!--end::Section-->
                    <a href="/payroll-bca/daily-aerplus" class="btn btn-primary" style="background-color: royalblue;">
                        <span class="me-2">Buka</span>
                        <i class="bi bi-chevron-right pe-0"></i>
                    </a>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card widget 2-->
        </div>
        <!--end::Col-->
    </div>
    <div class="separator"></div>
    <div></div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection

@section('pagescript')

@endsection