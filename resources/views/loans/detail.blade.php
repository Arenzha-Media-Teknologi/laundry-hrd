@extends('layouts.app')

@section('title', 'Detail Pinjaman')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@inject('carbon', 'Carbon\Carbon')

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!--begin::Row-->
    <!--begin::Aside column-->
    <div class="w-100 mb-7 me-7 me-lg-10">
        <!--begin::Order details-->
        <div class="card card-flush py-4 border-top border-primary border-top-4">
            <!--begin::Card header-->
            <div class="card-header ribbon ribbon-end">
                @if($completion >= 100)
                <div class="ribbon-label bg-success">Lunas</div>
                @else
                <div class="ribbon-label bg-warning">Belum Lunas</div>
                @endif
                <div class="card-title">
                    <h2>Detail Pinjaman</h2>
                </div>
                <div class="card-toolbar">

                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex flex-wrap mb-10">
                            <!--begin::Col-->
                            <div class="border border-dashed border-gray-300 rounded my-3 p-4 me-6">
                                <span class="fs-2x fw-bolder text-gray-800 lh-1">
                                    <span>Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
                                </span>
                                <span class="fs-6 fw-bold text-gray-400 d-block lh-1 pt-2">Total Pinjaman</span>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="border border-dashed border-gray-300 rounded my-3 p-4 me-6">
                                <span class="fs-2x fw-bolder text-gray-800 lh-1">
                                    <span>{{ count($loan_items) }}</span>
                                    <span class="fs-6 fw-bold text-gray-400 d-block lh-1 pt-2">Bulan Cicilan</span>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="border border-dashed border-gray-300 rounded my-3 p-4 me-6">
                                <span class="fs-2x fw-bolder text-gray-800 lh-1">
                                    <span data-kt-countup="true" data-kt-countup-value="1,240" data-kt-countup-prefix="$" class="counted">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                                </span>
                                <span class="fs-6 fw-bold text-gray-400 d-block lh-1 pt-2">Sisa Pinjaman</span>
                            </div>
                            <!--end::Col-->
                        </div>
                        <div class="d-flex align-items-center w-200px w-sm-500px flex-column mb-10">
                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                <span class="fw-bold fs-6 text-gray-400">Progres Pembayaran</span>
                                <span class="fw-bolder fs-6">{{ $completion }}%</span>
                            </div>
                            <div class="h-5px mx-3 w-100 bg-light mb-3">
                                <div class="<?= $completion >= 100 ? 'bg-success' : 'bg-warning' ?> rounded h-5px" role="progressbar" style="width: <?= $completion ?>%;" aria-valuenow="<?= $completion ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-bold text-muted">Tanggal</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <span class="fw-bolder fs-6 text-gray-800">{{ \Carbon\Carbon::parse($loan->effective_date)->isoFormat('ll') }}</span>
                            </div>
                            <!--end::Col-->
                        </div>
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-bold text-muted">Nama Pinjaman</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <span class="fw-bolder fs-6 text-gray-800">
                                    @if(isset($loan->name->name))
                                    {{ $loan->name->name }}
                                    @endif
                                </span>
                            </div>
                            <!--end::Col-->
                        </div>
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-bold text-muted">Pegawai</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <span class="fw-bolder fs-6 text-gray-800">
                                    @if(isset($loan->employee->number))
                                    {{ $loan->employee->number }} |
                                    @endif
                                    @if(isset($loan->employee->name))
                                    {{ $loan->employee->name }}
                                    @endif
                                </span>
                            </div>
                            <!--end::Col-->
                        </div>
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-bold text-muted">Keterangan</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <span class="fw-bolder fs-6 text-gray-800">{{ $loan->description }}</span>
                            </div>
                            <!--end::Col-->
                        </div>
                    </div>
                </div>

            </div>
            <!--end::Card header-->
        </div>
        <!--end::Order details-->
    </div>
    <!--end::Aside column-->
    <!--begin::Main column-->
    <div class="d-flex flex-column flex-lg-row-fluid gap-7 gap-lg-10">
        <!--begin::Order details-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Cicilan Pinjaman</h2>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table align-middle table-striped">
                        <thead class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200 bg-light-primary">
                            <tr>
                                <td class="ps-2">Cicilan Ke</td>
                                <td class="text-center">Tanggal Pembayaran</td>
                                <td class="text-end">Jumlah</td>
                                <td class="text-end pe-2">Sisa Pinjaman</td>
                            </tr>
                        </thead>
                        <tbody class="fw-bold text-gray-700">
                            <!-- <tr is="loan-item" v-for="(item, index) in items" :key="index" :item="item" :index="index" :items="items"></tr> -->
                            @foreach($loan_items as $index => $loanItem)
                            <tr>
                                <td class="ps-2">{{ $loanItem->installment_order }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($loanItem->payment_date)->isoFormat('ll') }}</td>
                                <td class="text-end">
                                    <span>
                                        @if($loanItem->salary_item_count > 0 || $loanItem->paid == 1)
                                        <span class="badge badge-light-success me-3">Dibayar</span>
                                        @endif
                                        {{ number_format($loanItem->basic_payment, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-end pe-2">0</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- <pre>
                    @{{ items }}
                </pre> -->
            </div>
            <!--end::Card header-->
        </div>
        <!--end::Order details-->
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
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment.min.js"></script> -->
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


        datatable = $('#insurances-datatable').DataTable({
            "ordering": false,
            "drawCallback": function() {
                console.log('redraw table...')
            },
            "language": {
                "infoEmpty": " ",
                "zeroRecords": " "
            }
        });


        // const filterSearch = document.querySelector('[data-insurance-employee-filter="search"]');
        // filterSearch.addEventListener('keyup', function(e) {
        //     datatable.search(e.target.value).draw();
        // });

    })
</script>
<script>
    moment.locale('id');

    const closeModal = (selector) => {
        const modalElement = document.querySelector(selector);
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();
    }

    const loanItems = <?php echo Illuminate\Support\Js::from($loan_items) ?>;

    Vue.prototype.moment = moment;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                items: loanItems,
            }
        },
        methods: {}
    })
</script>
@endsection