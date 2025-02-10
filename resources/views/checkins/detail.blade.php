@extends('layouts.app')

@section('title', 'Detail Check In')

@section('prehead')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" integrity="sha512-ZKX+BvQihRJPA8CROKBhDNvoc2aDMOdAlcm7TUQY+35XYtrd3yh95QOOhsPDQY9QnKE0Wqag9y38OIgEvb88cA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('pagestyle')
<style>
    .dataTables_empty {
        display: none;
    }

    /* table.dataTable.table-striped>tbody>tr.odd {
        background-color: #fafafa;
    } */
</style>
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!-- <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_4">Divisi</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_5">Link 2</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_6">Link 3</a>
        </li>
    </ul> -->
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0">Detail Check In</h1>
            <p class="text-muted mb-0">Detail Check In Pegawai</p>
        </div>
        <div>

            <div class="mb-0 fs-1">
                <i class="bi bi-calendar3 fs-1 me-2"></i>
                <span class="fw-bolder text-gray-600">{{ \Carbon\Carbon::parse($date)->isoFormat('ll') }}</span>
            </div>
        </div>
    </div>
    <div class="separator my-5" style="border-bottom-width: 3px;"></div>
    @if(!empty($employee))
    <div class="card mb-10">
        <div class="card-body">
            <table id="kt_profile_overview_table" class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
                <thead class="fs-7 text-gray-500 text-uppercase">
                    <tr>
                        <th class="min-w-250px">Pegawai</th>
                        <th class="min-w-150px">Divisi</th>
                        <th class="min-w-90px">Check In Awal</th>
                        <th class="min-w-90px">Check In Akhir</th>
                    </tr>
                </thead>
                <tbody class="fs-6">
                    <tr>
                        <td>
                            <!--begin::User-->
                            <div class="d-flex align-items-center">
                                <!--begin::Wrapper-->
                                <div class="me-5 position-relative">
                                    <!--begin::Avatar-->
                                    <div class="symbol symbol-35px symbol-circle">
                                        <img alt="Pic" src="{{ $employee->photo ?? 'https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg' }}" />
                                    </div>
                                    <!--end::Avatar-->

                                </div>
                                <!--end::Wrapper-->

                                <!--begin::Info-->
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="/employees/{{ $employee->id }}/detail-v2" class="fs-6 text-gray-800 text-hover-primary">{{ $employee->name }}</a>
                                    <div class="fw-semibold text-gray-500">{{ $employee->activeCareer->jobTitle->name ?? '-' }}</div>
                                </div>
                                <!--end::Info-->
                            </div>
                            <!--end::User-->
                        </td>
                        <td>{{ $employee->office->division->name ?? '-' }}</td>
                        <?php
                        $firstCheckIn = collect($check_ins)->first();
                        $lastCheckIn = collect($check_ins)->last();
                        ?>
                        <td>
                            <div class="fw-bolder">{{ $lastCheckIn->time ?? '-' }}</div>
                            <div class="text-gray-600">{{ $lastCheckIn->note ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-bolder">{{ $firstCheckIn->time ?? '-' }}</div>
                            <div class="text-gray-600">{{ $firstCheckIn->note ?? '-' }}</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif
    <div class="card">
        <!--begin::Card head-->
        <div class="card-header card-header-stretch">
            <!--begin::Title-->
            <div class="card-title d-flex align-items-center">
                <h3 class="fw-bold m-0 text-gray-800">Riwayat Check In</h3>
            </div>
            <!--end::Title-->
        </div>
        <!--end::Card head-->

        <!--begin::Card body-->
        <div class="card-body">
            <div class="timeline timeline-border-dashed">
                @if(isset($attendance))
                <div class="timeline-item">
                    <!--begin::Timeline line-->
                    <div class="timeline-line"></div>
                    <!--end::Timeline line-->
                    <!--begin::Timeline icon-->
                    <div class="timeline-icon bg-white" style="margin-left: -8px;">
                        <!-- <i class="ki-duotone ki-message-text-2 fs-2 text-gray-500"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> -->
                        <i class="bi bi-clock fs-2 text-gray-500"></i>
                    </div>
                    <!--end::Timeline icon-->

                    <!--begin::Timeline content-->
                    <div class="timeline-content mb-10 mt-n1">
                        <!--begin::Timeline heading-->
                        <div class="pe-3 mb-5">
                            <!--begin::Title-->
                            <div class="fs-3 fw-bolder mb-2 align-middle"><span class="align-middle">{{ $attendance->clock_out_time }}</span> <span class="badge badge-danger align-middle">Clock Out</span></div>
                            <!--end::Title-->
                            <!--end::Description-->
                        </div>
                        <!--end::Timeline heading-->

                        <!--begin::Timeline details-->
                        <div class="overflow-auto pb-5">
                            <div class="card card-dashed">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <a href="{{ $attendance->clock_out_attachment }}" data-lightbox="roadtrip" data-title="{{ $attendance->clock_out_note }}">
                                                <div class="symbol symbol-100px symbol-2by3">
                                                    <img src="{{ $attendance->clock_out_attachment }}" alt="" />
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="fs-2 fw-bolder mb-2">{{ $attendance->clock_out_note }}</div>
                                            <div class="fs-6"><em>Lokasi tidak tersedia. Lihat
                                                    <a href="https://maps.google.com/?q={{ $attendance->clock_out_latitude }},{{ $attendance->clock_out_longitude }}" target="_blank">
                                                        <span>Lokasi dengan Google Maps</span>
                                                    </a>
                                                </em>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <a href="https://maps.google.com/?q={{ $attendance->clock_out_latitude }},{{ $attendance->clock_out_longitude }}" target="_blank" class="btn btn-light-primary">
                                                <i class="bi bi-geo-alt"></i>
                                                <span>Lokasi</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Timeline details-->
                    </div>
                    <!--end::Timeline content-->
                </div>
                <!--end::Timeline item-->
                @endif
                @foreach($check_ins as $check_in)
                <!--begin::Timeline item-->
                <div class="timeline-item">
                    <!--begin::Timeline line-->
                    <div class="timeline-line"></div>
                    <!--end::Timeline line-->
                    <!--begin::Timeline icon-->
                    <div class="timeline-icon bg-white" style="margin-left: -8px;">
                        <!-- <i class="ki-duotone ki-message-text-2 fs-2 text-gray-500"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> -->
                        <i class="bi bi-clock fs-2 text-gray-500"></i>
                    </div>
                    <!--end::Timeline icon-->

                    <!--begin::Timeline content-->
                    <div class="timeline-content mb-10 mt-n1">
                        <!--begin::Timeline heading-->
                        <div class="pe-3 mb-5">
                            <!--begin::Title-->
                            <div class="fs-3 fw-bolder mb-2">{{ $check_in->time }}</div>
                            <!--end::Title-->
                            <!--end::Description-->
                        </div>
                        <!--end::Timeline heading-->

                        <!--begin::Timeline details-->
                        <div class="overflow-auto pb-5">
                            <div class="card card-dashed">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <a href="{{ $check_in->attachment }}" data-lightbox="roadtrip" data-title="{{ $check_in->note }}">
                                                <div class="symbol symbol-100px symbol-2by3">
                                                    <!-- <img src="{{ $check_in->attachment }}" alt="" /> -->
                                                    <img src="{{ $check_in->attachment }}" alt="" />
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="fs-2 fw-bolder mb-2">{{ $check_in->note }}</div>
                                            <div class="fs-6">{{ $check_in->location }}</div>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <a href="https://maps.google.com/?q={{ $check_in->latitude }},{{ $check_in->longitude }}" target="_blank" class="btn btn-light-primary">
                                                <i class="bi bi-geo-alt"></i>
                                                <span>Lokasi</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Timeline details-->
                    </div>
                    <!--end::Timeline content-->
                </div>
                <!--end::Timeline item-->
                @endforeach
                @if(isset($attendance))
                <div class="timeline-item">
                    <!--begin::Timeline line-->
                    <div class="timeline-line"></div>
                    <!--end::Timeline line-->
                    <!--begin::Timeline icon-->
                    <div class="timeline-icon bg-white" style="margin-left: -8px;">
                        <!-- <i class="ki-duotone ki-message-text-2 fs-2 text-gray-500"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> -->
                        <i class="bi bi-clock fs-2 text-gray-500"></i>
                    </div>
                    <!--end::Timeline icon-->

                    <!--begin::Timeline content-->
                    <div class="timeline-content mb-10 mt-n1">
                        <!--begin::Timeline heading-->
                        <div class="pe-3 mb-5">
                            <!--begin::Title-->
                            <div class="fs-3 fw-bolder mb-2 align-middle">
                                <span class="align-middle">{{ $attendance->clock_in_time }}</span> <span class="badge badge-success align-middle">Clock In</span>
                            </div>
                            <!--end::Title-->
                            <!--end::Description-->
                        </div>
                        <!--end::Timeline heading-->

                        <!--begin::Timeline details-->
                        <div class="overflow-auto pb-5">
                            <div class="card card-dashed">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <a href="{{ $attendance->clock_in_attachment }}" data-lightbox="roadtrip" data-title="{{ $attendance->clock_in_note }}">
                                                <div class="symbol symbol-100px symbol-2by3">
                                                    <img src="{{ $attendance->clock_in_attachment }}" alt="" />
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="fs-2 fw-bolder mb-2">{{ $attendance->clock_in_note }}</div>
                                            <div class="fs-6"><em>Lokasi tidak tersedia. Lihat
                                                    <a href="https://maps.google.com/?q={{ $attendance->clock_in_latitude }},{{ $attendance->clock_in_longitude }}" target="_blank">
                                                        <span>Lokasi dengan Google Maps</span>
                                                    </a>
                                                </em>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <a href="https://maps.google.com/?q={{ $attendance->clock_in_latitude }},{{ $attendance->clock_in_longitude }}" target="_blank" class="btn btn-light-primary">
                                                <i class="bi bi-geo-alt"></i>
                                                <span>Lokasi</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Timeline details-->
                    </div>
                    <!--end::Timeline content-->
                </div>
                <!--end::Timeline item-->
                @endif
            </div>
            <!--end::Timeline-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Timeline-->
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
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


        datatable = $('#kt_customers_table').DataTable({
            "columnDefs": [{
                "targets": 3,
                // "searchable": false
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
@endsection