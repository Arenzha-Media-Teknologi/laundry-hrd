@extends('layouts.app')

@section('title', 'Gaji Bulanan')

@section('prehead')
<!-- <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" /> -->
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
    <!-- <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_4">Perusahaan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_5">Link 2</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_6">Link 3</a>
        </li>
    </ul> -->
    <?php $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] ?>
    <div class="card mb-5 mb-xxl-10" style="background: linear-gradient(to right, #2f80ed, #56ccf2); background-image: url('<?= asset('assets/media/patterns/hexagonal.jpg') ?>'); background-position: center; background-repeat: no-repeat; background-size: cover;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-white mb-5 fs-1">Payroll BCA</p>
                    <h1 class="text-white mb-3 fs-2x">Gaji Harian Aerplus</h1>
                    <p class="m-0 fs-3 text-white mb-4">{{ \Carbon\Carbon::parse($start_date)->isoFormat('LL') }} - {{ \Carbon\Carbon::parse($end_date)->isoFormat('LL') }}</p>
                </div>
                <div class="text-end">
                    <p class="text-white mb-3 fs-4 fw-bolder">Total Gaji</p>
                    <p class="text-white fs-2x m-0">Rp @{{ currencyFormat(fixSalaries.totalTakeHomePay) }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-5 mb-xxl-10" id="cardRunPayroll">
        <!--begin::Header-->
        <div class="card-header" style="background-image: url('<?= asset('assets/media/patterns/hexagonal.jpg') ?>'); background-position: center; background-repeat: no-repeat; background-size: cover;">
            <div class="card-title">
                <h3 class="text-white">Filter</h3>
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body">
            <!-- <h3>Filter</h3> -->
            <div class="alert alert-secondary" role="alert">
                <span class="bi bi-question-circle me-2"></span>
                <span>Pilih periode kemudian klik tombol <strong>Generate</strong> untuk men-<em>generate</em> file</span>
            </div>
            <div class="row align-items-end justify-content-end mb-6">
                <!-- <div class="col-md-6 col-lg-4 my-3">
                    <div class="mb-0">
                        <label class="form-label">Tahun</label>
                        <select v-model="model.year" class="form-select form-select-solid">
                            @for($i = 2021; $i <= date("Y"); $i++) <option value="{{ $i }}" <?= $i == (int) date("Y") ? 'selected' : '' ?>>{{ $i }}</option>
                                @endfor
                        </select>
                    </div>
                </div> -->
                <div class="col-md-6 col-lg-4 my-3">
                    <div class="mb-0">
                        <label class="form-label">Periode</label>
                        <select class="form-select form-select-solid" id="select-period">
                            <option value="">Pilih Periode</option>
                            @foreach($periods as $index => $period)
                            <option value="{{ $period->start_date . '_' . $period->end_date }}">{{ \Carbon\Carbon::parse($period->start_date)->isoFormat('LL') }} - {{ \Carbon\Carbon::parse($period->end_date)->isoFormat('LL') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="my-3 text-end">
                <!-- <button class="btn btn-primary">Generate</button> -->
                <button type="button" class="btn btn-primary btn-sm" @click="generate" style="background-color: royalblue;">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Generate</span>
                </button>
            </div>
            <!-- <h3>Laporan</h3>
            <div>
                <a href="/salaries/export/report/monthly?start_date_period={{ $start_date }}&end_date_period={{ $end_date }}&staffonly={{ request()->query('staffonly') !== null ? request()->query('staffonly') : 'false' }}" target="_blank" class="btn btn-primary">Download Laporan</a>
            </div> -->
        </div>
        <!--end::Body-->
    </div>
    <div class="card">
        <!--begin::Card body-->
        <div class="card-body">
            <div class="alert alert-secondary" role="alert">
                <div class="d-flex align-items-center">
                    <span class="bi bi-exclamation-circle me-3 fs-4"></span>
                    <span>Nilai gaji yang tercantum sesuai dengan nilai gaji pada <a href="/payrolls/monthly"><strong>Gaji Dibuat</strong></a></span>
                </div>
            </div>
            <div class="d-flex flex-column flex-grow-1 gap-7 my-4 my-8" data-kt-scroll="true">
                <div class="d-flex align-items-center position-relative">
                    <!-- <div class="d-flex flex-center flex-shrink-0 w-25px h-25px bg-light-success rounded-circle border border-success">
                        <div class="w-8px h-8px bg-success rounded-circle"></div>
                    </div> -->


                    <div class="card border border-dashed d-flex flex-column p-6 gap-5 flex-grow-1 mb-2">
                        <div class="d-flex flex-stack">
                            <div>
                                <img src="<?= asset('assets/media/files/xls.png') ?>" width="100">
                            </div>

                            <!-- <span class="rounded text-success fw-semibold fs-8 bg-light-success py-1 px-2 border border-success-clarity">
                                Completed </span> -->

                        </div>

                        <div class="d-flex flex-column">
                            <span class="fs-3 fw-bold text-gray-800">
                                FILE TEMPLATE MULTI PAYROLL</span>
                            <span class="fs-base text-gray-500">
                                File ini dapat digunakan pada <em>software</em> File Converter Multi Transaksi BCA</span>
                        </div>
                        @if(count($error_rows) > 0)
                        <div class="separator"></div>
                        <div>
                            <h6 class="text-danger">Data Tidak Lengkap</h6>
                            <p class="text-muted">Lengkapi dan perbaiki data yang ditandai untuk melanjutkan proses</p>
                        </div>
                        <div style="height: 400px; overflow: scroll; position: relative;">
                            <div class="table-responsive" style="position: relative;">
                                <table class="table table-striped fs-7" style="position: sticky; top:0">
                                    <thead class="table-dark align-middle fw-bold">
                                        <tr>
                                            <td class="ps-2">No</td>
                                            <td>Transaction ID</td>
                                            <td>Transfer Type</td>
                                            <td>Beneficiary ID</td>
                                            <td>Credited Account</td>
                                            <td>Receiver Name</td>
                                            <td class="text-end">Amount</td>
                                            <td>NIP</td>
                                            <td>Remark</td>
                                            <td>Benefeciary email address</td>
                                            <td>Receiver Swift Code</td>
                                            <td>Receiver Cust Type</td>
                                            <td class="pe-2">Receiver Cust Residence</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $usedRemarkNames = [];
                                        $secondName = [];
                                        ?>
                                        @foreach($error_rows as $employee)
                                        <?php
                                        $tryedNames = [];
                                        $joinedName = preg_replace('/\PL/u', '', str_replace(" ", "", $employee->credited_account ?? ""));
                                        $remark = "";
                                        // ["muly", "muha"]
                                        if (strlen($joinedName) > 4) {
                                            $willRemoveCharIndex = 3;
                                            $tryIteration = 1;
                                            do {
                                                $remark = substr($joinedName, 0, 4);
                                                $joinedName = substr_replace($joinedName, "", $willRemoveCharIndex, 1);
                                                array_push($tryedNames, $willRemoveCharIndex . ' - ' . $joinedName);

                                                if ($willRemoveCharIndex > 10) {
                                                    break;
                                                }

                                                $tryIteration++;
                                                // 3 + 1 = 4
                                                // ----
                                                // 4 + 1 = 5
                                            } while (
                                                // false
                                                in_array($remark, $usedRemarkNames)
                                                // true
                                                // ----
                                            );
                                        } else {
                                            $remark = substr($joinedName, 0, 4);
                                        }
                                        array_push($usedRemarkNames, $remark);

                                        $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
                                        $remark = "HON" . strtoupper($remark) . ($months[(\Carbon\Carbon::parse($end_date)->month) - 1]) . \Carbon\Carbon::parse($end_date)->format('y');

                                        $amount = $employee->amount > 0 ? $employee->amount : 0;
                                        ?>
                                        <tr>
                                            <td></td>
                                            <td class="{{ empty($employee->transaction_id) ? 'bg-danger' : '' }}">{{ $employee->transaction_id }}{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT); }}</td>
                                            <td class="{{ empty($employee->transfer_type) ? 'bg-danger' : '' }}">{{ $employee->transfer_type }}</td>
                                            <td></td>
                                            <td class="{{ empty($employee->bank_account_number) ? 'bg-danger' : '' }}">{{ $employee->bank_account_number }}</td>
                                            <td class="{{ empty($employee->credited_account) ? 'bg-danger' : '' }}">{{ strtoupper($employee->credited_account) }}</td>
                                            <td class="text-end {{ empty($amount) ? 'bg-danger' : '' }}">{{ number_format($amount, 2) }}</td>
                                            <td></td>
                                            <td>{{ $remark }}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                        <div>
                            <a href="/payroll-bca/daily-aerplus/multi-payroll-template?start_date_period={{ $start_date }}&end_date_period={{ $end_date }}&company_id={{ $company_id }}" class="btn btn-outline btn-sm btn-outline-success btn-active-light-success" target="_blank">
                                <i class="bi bi-download text-success"></i>
                                <span>Download</span>
                            </a>
                        </div>
                    </div>

                </div>
                <div class="d-flex align-items-center position-relative">
                    <!-- <div class="d-flex flex-center flex-shrink-0 w-25px h-25px bg-light-success rounded-circle border border-success">
                        <div class="w-8px h-8px bg-success rounded-circle"></div>
                    </div> -->
                    <div class="card border border-dashed d-flex flex-column p-6 gap-5 flex-grow-1 mb-2">
                        <div class="d-flex flex-stack">
                            <div>
                                <img src="<?= asset('assets/media/files/txt.png') ?>" width="100">
                            </div>
                        </div>

                        <div class="d-flex flex-column">
                            <span class="fs-3 fw-bold text-gray-800">
                                FILE TRANSAKSI</span>
                            <span class="fs-base text-gray-500">
                                File ini dapat digunakan pada website klikBCA Bisnis</span>
                        </div>
                        <div class="separator"></div>
                        <div>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>Detail Perusahaan</h5>
                                <div>
                                    <a href="/companies" class="btn btn-secondary btn-sm">
                                        <i class="bi bi-pencil"></i>
                                        <span>Ubah</span>
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="fs-7 fw-bold text-gray-600">
                                            <th>Corporate ID</th>
                                            <th>Company Code</th>
                                            <th>Business Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="fs-6 fw-bold">
                                            <td>{{ $company->corporate_id }}</td>
                                            <td>{{ $company->company_code }}</td>
                                            <td>{{ $company->businessType->name ?? "" }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="separator mb-5"></div>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="fs-7 fw-bold text-gray-600">
                                            <th>Header ID</th>
                                            <th>Effective Date</th>
                                            <th>Effective Time</th>
                                            <th>Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="fs-6 fw-bold">
                                            <td><input type="text" v-model="model.transactionTxt.headerId" class="form-control form-control-solid form-control-sm"></td>
                                            <td><input type="date" v-model="model.transactionTxt.effectiveDate" class="form-control form-control-solid form-control-sm"></td>
                                            <td>
                                                <select v-model="model.transactionTxt.effectiveTime" class="form-select form-select-solid form-select-sm">
                                                    @for($effectiveTime = 1; $effectiveTime < 24; $effectiveTime++) <option value="{{ $effectiveTime }}">{{ $effectiveTime }}</option>
                                                        @endfor
                                                </select>
                                            </td>
                                            <td><input type="text" v-model="model.transactionTxt.remark" class="form-control form-control-solid form-control-sm"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if(count($error_rows) > 0)
                        <div class="alert alert-warning" role="alert">
                            <div class="d-flex align-items-center">
                                <span class="bi bi-exclamation-circle me-3 fs-4"></span>
                                <span>Data belum lengkap dan perlu diperbaiki. File yang diunduh mungkin akan menyebabkan <em>error</em></span>
                            </div>
                        </div>
                        @endif
                        <div>
                            <a :href="downloadTransactionTxtUrl" class="btn btn-outline btn-sm btn-outline-success btn-active-light-success" target="_blank">
                                <i class="bi bi-download text-success"></i>
                                <span>Download</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!--end::Card body-->
    </div>
</div>
@endsection

@section('script')
<!-- <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script> -->
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
    const closeCompanyAddModal = () => {
        const addCompanyModal = document.querySelector('#kt_modal_add_company');
        const modal = bootstrap.Modal.getInstance(addCompanyModal);
        modal.hide();
    }

    const salaries = [];
    const fixSalaries = [];
    const summarySalaries = [];
    const periods = <?php echo Illuminate\Support\Js::from($periods) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                model: {
                    fixSalaries: {
                        checkedAll: false,
                        checkedEmployeesIds: [],
                    },
                    checkedAll: false,
                    checkedEmployeesIds: [],
                    transactionTxt: {
                        headerId: "",
                        effectiveDate: "{{ date('Y-m-d') }}",
                        effectiveTime: "",
                        remark: "",
                    },
                    year: '{{ date("Y") }}',
                    period: '{{ $start_date }}_{{$end_date}}',
                },
                fixSalaries: {
                    salaries: fixSalaries,
                    totalEmployees: 0,
                    totalTakeHomePay: Number("{{ $take_home_pay }}"),
                },
                summarySalaries: {
                    salaries: summarySalaries,
                },
                periods,
            }
        },
        computed: {
            downloadTransactionTxtUrl() {
                const {
                    headerId,
                    effectiveDate,
                    effectiveTime,
                    remark
                } = this.model.transactionTxt;
                return `/payroll-bca/daily-aerplus/export-transaction-txt?start_date_period={{ $start_date }}&end_date_period={{ $end_date }}&company_id={{ $company_id }}&header_id=${headerId}&effective_date=${effectiveDate}&effective_time=${effectiveTime}&remark=${remark}`;
            },
            periodsByYear() {
                const self = this;
                return this.periods.filter(period => {
                    return period.year == self.model.year;
                })
            },
        },
        methods: {
            currencyFormat(number) {
                const numbered = Number(number);
                return new Intl.NumberFormat('De-de').format(numbered);
            },
            async generate() {
                const {
                    period
                } = this.model;

                const [startDate, endDate] = period.split('_');

                document.location.href = `/payroll-bca/daily-aerplus?start_date=${startDate}&end_date=${endDate}`;
            },

        },
        watch: {}
    })
</script>
<script>
    $(function() {
        $('#select-period').select2();

        if (app.$data.model.period) {
            $('#select-period').val(app.$data.model.period).trigger('change');
        }

        $('#select-period').on('change', function(e) {
            app.$data.model.period = $(this).val();
        });
    });
</script>
@endsection