@extends('layouts.app')

@section('title', 'Jadwal Kerja')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('pagestyle')
<style>
    .dataTables_empty {
        display: none;
    }
</style>
@endsection

<?php
$groupPermissions = auth()->user()->group->permissions ?? "[]";
$permissions = json_decode($groupPermissions);
// dd($permissions);
?>

@section('content')
<div id="kt_content_container" class="container-xxl">
    <div class="row flex-wrap flex-stack mb-5">
        <!--begin::Heading-->
        <div class="col-md-6 d-flex align-items-center">
            <h2 class="fs-2 fw-bold my-2">Jadwal Kerja
                <!-- <span class="fs-6 text-gray-400 ms-1">by Month</span> -->
            </h2>
            <div class="ps-3">
                <select class="form-select" style="width: 150px;" id="select-year">
                    <?php $selectedYear = request()->query('year') ?? date('Y'); ?>
                    @for($year = 2023; $year <= date('Y'); $year++) <option value="{{ $year }}" <?= $selectedYear == $year ? 'selected' : '';  ?>>
                        {{ $year }}</option>
                        @endfor
                </select>
            </div>
        </div>
        <!--end::Heading-->
        <!--begin::Controls-->
        <div class="col-md-6 d-flex flex-wrap my-1 justify-content-end">
            <!--begin::Select wrapper-->
            <div class="m-0">
                <!--begin::Select-->
                @if(in_array('add_work_schedule', $permissions))
                <a href="/work-schedules/create" class="btn btn-primary">Buat Jadwal Baru</a>
                @endif
                <!--end::Select-->
            </div>
            <!--end::Select wrapper-->
        </div>
        <!--end::Controls-->
    </div>
    <div class="row g-6 g-xl-9">
        <?php $isEmpty = false; ?>
        @foreach($months as $index => $month)
        @if(array_key_exists($index, $schedules))
        <div class="col-lg-6 col-xxl-6">
            <!--begin::Budget-->
            <div class="card h-100">
                <div class="card-body p-9">
                    <div class="fs-2 fw-bolder">{{ $month }}</div>
                    <div class="fs-4 fw-bold text-gray-400 mb-7">Periode di bulan {{ strtolower($month) }}</div>
                    @foreach($schedules[$index] as $schedule)
                    <?php
                    $startDate = $schedule->start_date;
                    $endDate = $schedule->end_date;
                    ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold">Periode &nbsp;<a href="/work-schedules/{{ $schedule->id }}/detail" class="text-primary">{{ \Carbon\Carbon::parse($startDate)->isoFormat('ll') }} - {{ \Carbon\Carbon::parse($endDate)->isoFormat('ll') }}</a></div>
                        </div>
                        <div class="d-flex">
                            <!-- <a href="/daily-salaries/export/aerplus-report?start_date={{ $startDate }}&end_date={{ $endDate }}" target="_blank" class="btn btn-light-primary btn-sm">
                                <i class="bi bi-download"></i>
                                <span>Rekap Detail</span>
                            </a> -->
                            <div class="btn-group me-2">
                                <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-download"></i>
                                    <span>Export</span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/work-schedules/export/by-employee?id={{ $schedule->id }}" target="_blank">By Pegawai</a></li>
                                    <li><a class="dropdown-item" href="/work-schedules/export/by-office?id={{ $schedule->id }}" target="_blank">By Outlet</a></li>
                                </ul>
                            </div>
                            @if(in_array('delete_work_schedule', $permissions))
                            <div>
                                <button class="btn btn-icon btn-light-danger btn-sm" @click="openDeleteConfirmation({{ $schedule->id }})"><i class="bi bi-trash"> </i></button>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                    @endforeach
                </div>
            </div>
            <!--end::Budget-->
        </div>
        @endif
        @endforeach
        @if(count($schedules) == 0)
        <div class="text-center">
            <img src="{{ asset('assets/media/illustrations/sigma-1/5.png') }}" alt="" width="300">
            <p class="text-muted fw-bold fs-3 mt-3">Tidak ada data</p>
        </div>
        @endif
    </div>

</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection

@section('pagescript')
<script>
    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {}
        },
        methods: {
            // COMPANY METHODS
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
                return axios.delete('/work-schedules/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        toastr.success(message);

                        setTimeout(() => {
                            document.location.reload();
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
        },
    })

    $(function() {
        $('#select-year').on('change', function(e) {
            document.location.href = "/payrolls/daily-aerplus?year=" + $(this).val();
        })
    })
</script>
@endsection