@extends('layouts.app')

@section('title', '[Maintenance] Kenaikan Gaji')

@section('prehead')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.1.0/css/fixedColumns.dataTables.min.css">
@endsection

@inject('carbon', 'Carbon\Carbon')

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!--begin::Row-->
    <!--begin::Main column-->
    <div class="d-flex flex-column flex-lg-row-fluid gap-7 gap-lg-10">
        <!--begin::Alert-->
        <div class="alert bg-light-primary d-flex flex-center flex-column py-10 px-10 px-lg-20 mb-10">

            <!--begin::Icon-->
            <span class="svg-icon svg-icon-5tx svg-icon-primary mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M22.0318 8.59998C22.0318 10.4 21.4318 12.2 20.0318 13.5C18.4318 15.1 16.3318 15.7 14.2318 15.4C13.3318 15.3 12.3318 15.6 11.7318 16.3L6.93177 21.1C5.73177 22.3 3.83179 22.2 2.73179 21C1.63179 19.8 1.83177 18 2.93177 16.9L7.53178 12.3C8.23178 11.6 8.53177 10.7 8.43177 9.80005C8.13177 7.80005 8.73176 5.6 10.3318 4C11.7318 2.6 13.5318 2 15.2318 2C16.1318 2 16.6318 3.20005 15.9318 3.80005L13.0318 6.70007C12.5318 7.20007 12.4318 7.9 12.7318 8.5C13.3318 9.7 14.2318 10.6001 15.4318 11.2001C16.0318 11.5001 16.7318 11.3 17.2318 10.9L20.1318 8C20.8318 7.2 22.0318 7.59998 22.0318 8.59998Z" fill="currentColor" />
                    <path d="M4.23179 19.7C3.83179 19.3 3.83179 18.7 4.23179 18.3L9.73179 12.8C10.1318 12.4 10.7318 12.4 11.1318 12.8C11.5318 13.2 11.5318 13.8 11.1318 14.2L5.63179 19.7C5.23179 20.1 4.53179 20.1 4.23179 19.7Z" fill="currentColor" />
                </svg>
            </span>
            <!--end::Icon-->

            <!--begin::Wrapper-->
            <div class="text-center">
                <!--begin::Title-->
                <h1 class="fw-bolder mb-5">Menu sedang dalam pengembangan</h1>
                <!--end::Title-->

                <!--begin::Separator-->
                <div class="separator separator-dashed border-primary opacity-25 mb-5"></div>
                <!--end::Separator-->

                <!--begin::Content-->
                <div class="mb-9 text-dark">
                    Menu ini sedang dalam masa pengembangan. untuk mengatur gaji karyawan bisa melalui menu <strong>Pegawai > Klik nama pegawai > Klik tab penggajian > Masukkan nominal > Simpan</strong>
                </div>
                <!--end::Content-->

                <!--begin::Buttons-->
                <div class="d-flex flex-center flex-wrap">
                    <a href="/employees" class="btn btn-primary m-2">Menu Pegawai</a>
                </div>
                <!--end::Buttons-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Alert-->
    </div>
    <!--end::Main column-->
    <!-- <div class="d-flex flex-column flex-lg-row">
    </div> -->
    <!--end::Row-->
    <!-- end::card -->
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.1.0/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
@endsection

@section('pagescript')
@endsection