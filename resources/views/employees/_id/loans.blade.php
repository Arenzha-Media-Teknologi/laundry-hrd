@extends('layouts.app')

@section('title', $employee->name . ' - Pinjaman')

@section('head')

@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <x-employee-detail-card :employee="$employee" />
    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
        <!--begin::Card header-->
        <div class="card-header">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Statistik Pinjaman</h3>
            </div>
            <div class="card-toolbar">
                <!--begin::Svg Icon | path: assets/media/icons/duotune/general/gen045.svg-->
                <span class="svg-icon svg-icon-muted svg-icon-2hx" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Statistik yang ditampilkan merupakan data seluruh pinjaman sampai tanggal berjalan"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                        <rect x="11" y="17" width="7" height="2" rx="1" transform="rotate(-90 11 17)" fill="black" />
                        <rect x="11" y="9" width="2" height="2" rx="1" transform="rotate(-90 11 9)" fill="black" />
                    </svg></span>
                <!--end::Svg Icon-->
            </div>
        </div>
        <!--begin::Card header-->
        <!--begin::Card body-->
        <div class="card-body p-9">
            <div class="row p-0 mb-5">
                <!--begin::Col-->
                <div class="col">
                    <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                        <span class="fs-4 fw-bold text-info d-block mb-3">Pinjaman Berjalan</span>
                        <span class="fs-2 fw-bolder text-gray-900">{{ number_format($statistics['total_loans'], 0, ',', '.') }}</span>
                    </div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col">
                    <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                        <span class="fs-4 fw-bold text-primary d-block mb-3">Total Pinjaman</span>
                        <span class="fs-2 fw-bolder text-gray-900 counted">Rp {{ number_format($statistics['total_loans_amount'], 0, ',', '.') }}</span>
                    </div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col">
                    <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                        <span class="fs-4 fw-bold text-success d-block mb-3">Sisa Pinjaman</span>
                        <span class="fs-2 fw-bolder text-gray-900">Rp {{ number_format($statistics['remaining_loans'], 0, ',', '.') }}</span>
                    </div>
                </div>
                <!--end::Col-->
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Profil Completion-->
    <div class="d-flex flex-wrap flex-stack mb-6">
        <!--begin::Heading-->
        <h3 class="fw-bolder my-2">Daftar Pinjaman
            <!-- <span class="fs-6 text-gray-400 fw-bold ms-1">Active</span> -->
        </h3>
        <!--end::Heading-->
        <!--begin::Actions-->
        <div class="d-flex flex-wrap my-2">
            <a href="/loans/create" class="btn btn-primary">Tambah Pinjaman</a>
        </div>
        <!--end::Actions-->
    </div>
    <div class="row g-6 g-xl-9 mb-5 mb-xl-10">
        @if(count($loans) < 1) <div class="col-md-12 text-center px-5 fs-5 fw-bold">
            <span class="text-muted">Tidak ada pinjaman</span>
    </div>
    @endif
    @foreach($loans as $loan)
    <div class="col-md-6 col-xl-4">
        <!--begin::Card-->
        <div class="card border-hover-primary">
            <!--begin::Card header-->
            <div class="card-header border-0 pt-9">
                <!--begin::Card Title-->
                <div class="card-title m-0">
                    <!--begin::Avatar-->
                    <!-- <div class="symbol symbol-50px w-50px bg-light">
                            <img src="{{asset('assets/media/svg/brand-logos/plurk.svg')}}" alt="image" class="p-3">
                        </div> -->
                    <!--begin::Svg Icon | path: assets/media/icons/duotune/coding/cod005.svg-->
                    <!--end::Avatar-->
                    <!--begin::Svg Icon | path: assets/media/icons/duotune/abstract/abs013.svg-->
                    @if($loan->completion >= 100)
                    <span class="badge badge-light-success fw-bolder me-auto px-4 py-3">Lunas</span>
                    @else
                    <span class="badge badge-light-warning fw-bolder me-auto px-4 py-3">Belum Lunas</span>
                    @endif
                </div>
                <!--end::Car Title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <div class="d-flex">
                        <div class="me-0">
                            <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="bi bi-three-dots fs-3"></i>
                            </button>
                            <!--begin::Menu 3-->
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px py-3" data-kt-menu="true">
                                <!--begin::Heading-->
                                <!-- <div class="menu-item px-3">
                                        <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Detail</div>
                                    </div> -->
                                <!--end::Heading-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="/loans/{{ $loan->id }}/detail" class="menu-link px-3">Detail</a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="/loans/{{ $loan->id }}/edit" class="menu-link px-3">Ubah</a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link text-danger px-3 btn-delete" data-id="{{ $loan->id }}">Hapus</a>
                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::Menu 3-->
                        </div>
                    </div>
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end:: Card header-->
            <!--begin:: Card body-->
            <div class="card-body p-9">
                <!--begin::Name-->
                <div class="fs-3 fw-bolder text-dark">{{ $loan->name->name }}</div>
                <!--end::Name-->
                <!--begin::Description-->
                <p class="text-gray-400 fw-bold fs-5 mt-1 mb-7">{{ $loan->description }}</p>
                <!--end::Description-->
                <!--begin::Info-->
                <div class="d-flex flex-wrap mb-5">
                    <!--begin::Due-->
                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-7 mb-3">
                        <div class="fs-6 text-gray-800 fw-bolder">{{ \Carbon\Carbon::parse($loan->effective_date)->isoFormat('ll') }}</div>
                        <div class="fw-bold text-gray-400">Tanggal</div>
                    </div>
                    <!--end::Due-->
                    <!--begin::Budget-->
                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
                        <div class="fs-6 text-gray-800 fw-bolder">Rp {{ number_format($loan->amount, 0, ',', '.') }}</div>
                        <div class="fw-bold text-gray-400">Jumlah</div>
                    </div>
                    <!--end::Budget-->
                </div>
                <!--end::Info-->
                <!--begin::Progress-->
                <div class="h-4px w-100 bg-light mb-5" data-bs-toggle="tooltip" title="" data-bs-original-title="Total pembayaran pinjaman adalah <?= $loan->completion ?>% dari total pinjaman">
                    <div class="<?= $loan->completion >= 100 ? 'bg-success' : 'bg-warning' ?> rounded h-4px" role="progressbar" style="width: <?= $loan->completion ?>%" aria-valuenow="<?= $loan->completion ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-end">
                    <span class="fw-bold">{{ number_format($loan->total_paid, 0, ',', '.') }}</span>
                    <span>/</span>
                    <span class="text-muted">{{ number_format($loan->amount, 0, ',', '.') }}</span>
                </div>
                <!--end::Progress-->
            </div>
            <!--end:: Card body-->
        </div>
        <!--end::Card-->
    </div>
    @endforeach
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

        function sendDeleteRequest(id) {
            const self = this;
            return axios.delete('/loans/' + id)
                .then(function(response) {
                    let message = response?.data?.message;
                    if (!message) {
                        message = 'Data berhasil disimpan'
                    }
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
        };


        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const id = $(this).attr('data-id');
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Komponen gaji untuk pinjaman akan ikut terhapus",
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
                    return sendDeleteRequest(id);
                },
                allowOutsideClick: () => !Swal.isLoading(),
                backdrop: true,
            })
        })
    })
</script>
<script src="{{ asset('assets/js/addons/employeeActivation.js') }}"></script>
<script>
    Vue.prototype.moment = moment

    // const statistics = <?php echo Illuminate\Support\Js::from($statistics) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                // statistics,
                loading: false,
            }
        },
        methods: {

        }
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