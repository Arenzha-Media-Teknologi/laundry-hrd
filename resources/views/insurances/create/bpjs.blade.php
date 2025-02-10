@extends('layouts.app')

@section('title', 'Asuransi')

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
    <div class="row mb-8">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="m-0 mb-5 text-info">Jenis Asuransi</h3>
                    <p class="m-0 text-gray-700 fw-bold fs-4">
                        @if($bpjs_id == 'ketenagakerjaan')
                        BPJS Ketenagakerjaan
                        @elseif($bpjs_id == 'mandiri')
                        BPJS Mandiri
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
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
                    <input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Pegawai" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <!--begin::Filter-->
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
                            </svg>
                        </span>
                        Filter
                    </button>
                    @if($bpjs_id == 'ketenagakerjaan')
                    <button type="button" :data-kt-indicator="loading ? 'on' : null" class="btn btn-primary" @click="onSaveBpjsValues" :disabled="loading">
                        <span class="indicator-label">
                            <i class="bi bi-save fs-6"></i>
                            Simpan Nilai Asuransi
                        </span>
                        <span class="indicator-progress">Mengirim data...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                    @endif
                    <!--begin::Menu 1-->
                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true" id="kt-toolbar-filter">
                        <!--begin::Header-->
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Filter</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Separator-->
                        <div class="separator border-gray-200"></div>
                        <!--end::Separator-->
                        <!--begin::Content-->
                        <div class="px-7 py-5">
                            <!--begin::Input group-->
                            <div class="mb-10">
                                <!--begin::Label-->
                                <label class="form-label fs-5 fw-bold mb-3">Jenis Asuransi:</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Pilih jenis asuransi" data-allow-clear="true" id="filterInsuranceType">
                                    <option value="bpjs_ketenagakerjaan" <?= request()->query('type') == 'bpjs_ketenagakerjaan' ? 'selected' : '' ?>>BPJS Ketenagakerjaan</option>
                                    <option value="bpjs_mandiri" <?= request()->query('type') == 'bpjs_mandiri' ? 'selected' : '' ?>>BPJS Mandiri</option>
                                    @foreach($private_insurances as $private_insurance)
                                    <option value="private_{{ $private_insurance->id }}" <?= request()->query('type') == 'private_' . $private_insurance->id ? 'selected' : '' ?>>{{ $private_insurance->name }}</option>
                                    @endforeach
                                </select>
                                <!--end::Input-->
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-5 fw-bold mb-3">Tahun:</label>
                                <select class="form-select form-select-solid" id="filterInsuranceYear">
                                    <?php
                                    $limitYear = (int) date('Y');
                                    for ($i = 2020; $i <= $limitYear; $i++) {
                                        $nYear = $i;
                                    ?>
                                        <option value="<?= $nYear ?>"><?= $nYear ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Actions-->
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" id="btnApplyFilter">Terapkan</button>
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Menu 1-->
                    <!--end::Filter-->
                    <!--begin::Export-->
                    <!-- <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_customers_export_modal">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black" />
                                <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black" />
                                <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4" />
                            </svg>
                        </span>
                        Export
                    </button> -->
                    <!--end::Export-->
                </div>
                <!--end::Toolbar-->
                <!--begin::Group actions-->
                <div class="d-flex justify-content-end align-items-center d-none" data-kt-customer-table-toolbar="selected">
                    <div class="fw-bolder me-5">
                        <span class="me-2" data-kt-customer-table-select="selected_count"></span>Selected
                    </div>
                    <button type="button" class="btn btn-danger" data-kt-customer-table-select="delete_selected">Delete Selected</button>
                </div>
                <!--end::Group actions-->
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <!--begin::Table-->
            @if($bpjs_id == 'ketenagakerjaan')
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6">
                    <!--begin::Table head-->
                    <thead class="bg-light-primary">
                        <!--begin::Table row-->
                        <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0 align-middle">
                            <!-- <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_customers_table .form-check-input" value="1" />
                            </div>
                        </th> -->
                            <!-- 5 -->
                            <?php $nextYearValueClass = "" ?>
                            <th class="min-w-125px ps-2" rowspan="2">No</th>
                            <th class="min-w-125px" rowspan="2">ID</th>
                            <th class="min-w-125px" rowspan="2">Nama Pemegang Polis</th>
                            <th class="min-w-125px" rowspan="2">Polis #</th>
                            <th class="min-w-125px text-center" rowspan="2">Tahun Mulai Polis</th>
                            <th colspan="4" class="text-center">Pembayaran Polis Tahun {{ $current_year }}</th>
                            <th colspan="4" class="text-center {{ $nextYearValueClass }} pe-2">Akumulasi Nilai Polis Tahun {{ $current_year }}</th>
                        </tr>
                        <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                            <!-- 8 -->
                            <th class="min-w-150px text-end">JHT</th>
                            <th class="min-w-150px text-end">JKK</th>
                            <th class="min-w-150px text-end">JKM</th>
                            <th class="min-w-150px text-end">JP</th>
                            <th class="min-w-150px text-end {{ $nextYearValueClass }}">JHT</th>
                            <th class="min-w-150px text-end {{ $nextYearValueClass }}">JKK</th>
                            <th class="min-w-150px text-end {{ $nextYearValueClass }}">JKM</th>
                            <th class="min-w-150px text-end {{ $nextYearValueClass }} pe-2">JP</th>
                        </tr>
                        <!--end::Table row-->
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="fw-bold text-gray-600">
                        <tr v-for="(employee, index) in employees">
                            <td class="ps-2">@{{ index + 1 }}</td>
                            <td class="text-gray-800">@{{ employee.number }}</td>
                            <td class="text-gray-800">@{{ employee.name }}</td>
                            <td>@{{ employee?.bpjs?.ketenagakerjaan_number || '' }}</td>
                            <td>@{{ employee?.bpjs?.ketenagakerjaan_start_year || '' }}</td>
                            <!-- <td class="text-end">@{{ employee?.previous_year_value?.jht || 0 }}</td>
                            <td class="text-end">@{{ employee?.previous_year_value?.jkk || 0 }}</td>
                            <td class="text-end">@{{ employee?.previous_year_value?.jkm || 0 }}</td>
                            <td class="text-end">@{{ employee?.previous_year_value?.jp || 0 }}</td> -->
                            <td class="text-end {{ $nextYearValueClass }}">
                                <input type="number" v-model="employee.current_year_value.jht_payment" class="form-control form-control-sm w-100">
                            </td>
                            <td class="text-end {{ $nextYearValueClass }}">
                                <input type="number" v-model="employee.current_year_value.jkk_payment" class="form-control form-control-sm w-100">
                            </td>
                            <td class="text-end {{ $nextYearValueClass }}">
                                <input type="number" v-model="employee.current_year_value.jkm_payment" class="form-control form-control-sm w-100">
                            </td>
                            <td class="text-end {{ $nextYearValueClass }}">
                                <input type="number" v-model="employee.current_year_value.jp_payment" class="form-control form-control-sm w-100">
                            </td>
                            <td class="text-end {{ $nextYearValueClass }}">
                                <input type="number" v-model="employee.current_year_value.jht" class="form-control form-control-sm w-100">
                            </td>
                            <td class="text-end {{ $nextYearValueClass }}">
                                <input type="number" v-model="employee.current_year_value.jkk" class="form-control form-control-sm w-100">
                            </td>
                            <td class="text-end {{ $nextYearValueClass }}">
                                <input type="number" v-model="employee.current_year_value.jkm" class="form-control form-control-sm w-100">
                            </td>
                            <td class="text-end {{ $nextYearValueClass }} pe-2">
                                <input type="number" v-model="employee.current_year_value.jp" class="form-control form-control-sm w-100">
                            </td>
                        </tr>
                    </tbody>
                    <!--end::Table body-->
                    <tfoot>
                        <tr class="bg-light">
                            <td colspan="5" class="fw-bold ps-3">TOTAL</td>
                            <td class="text-end">@{{ currencyFormat(insuranceTotal.payment.jht) }}</td>
                            <td class="text-end">@{{ currencyFormat(insuranceTotal.payment.jkk) }}</td>
                            <td class="text-end">@{{ currencyFormat(insuranceTotal.payment.jkm) }}</td>
                            <td class="text-end">@{{ currencyFormat(insuranceTotal.payment.jp) }}</td>
                            <td class="text-end">@{{ currencyFormat(insuranceTotal.current.jht) }}</td>
                            <td class="text-end">@{{ currencyFormat(insuranceTotal.current.jkk) }}</td>
                            <td class="text-end">@{{ currencyFormat(insuranceTotal.current.jkm) }}</td>
                            <td class="text-end">@{{ currencyFormat(insuranceTotal.current.jp) }}</td>
                        </tr>
                    </tfoot>
                </table>
                <!--end::Table-->
            </div>
            @elseif($bpjs_id == 'mandiri')
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6" id="kt_customers_table">
                    <!--begin::Table head-->
                    <thead class="bg-light-primary">
                        <!--begin::Table row-->
                        <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0 align-middle">
                            <!-- 5 -->
                            <th class="min-w-125px ps-2">No</th>
                            <th class="min-w-125px">ID</th>
                            <th class="min-w-125px">Nama Pemegang Polis</th>
                            <th class="min-w-125px pe-2">Polis #</th>
                        </tr>
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="fw-bold text-gray-700">
                        <tr v-for="(employee, index) in employees">
                            <td class="ps-2">@{{ index + 1 }}</td>
                            <td>@{{ employee.number }}</td>
                            <td>@{{ employee.name }}</td>
                            <td class="pe-2">@{{ employee?.bpjs?.mandiri_number || '' }}</td>
                        </tr>
                    </tbody>
                    <!--end::Table body-->
                </table>
                <!--end::Table-->
            </div>
            @endif
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
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

        $('#btnApplyFilter').on('click', function(e) {
            $insuranceType = $('#filterInsuranceType').val();
            $insuranceYear = $('#filterInsuranceYear').val();
            document.location.href = `/insurances/create?type=${$insuranceType}&year=${$insuranceYear}`
        })
    })
</script>
<script>
    const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                employees,
                previousYear: '{{ $previous_year }}',
                currentYear: '{{ $current_year }}',
                loading: false,
            }
        },
        computed: {
            insuranceTotal() {
                // // PREVIOUS
                // const totalPreviousJht = this.employees.map(employee => Number(employee.previous_year_value.jht)).reduce((acc, cur) => acc + cur, 0);
                // const totalPreviousJkk = this.employees.map(employee => Number(employee.previous_year_value.jkk)).reduce((acc, cur) => acc + cur, 0);
                // const totalPreviousJkm = this.employees.map(employee => Number(employee.previous_year_value.jkm)).reduce((acc, cur) => acc + cur, 0);
                // const totalPreviousJp = this.employees.map(employee => Number(employee.previous_year_value.jp)).reduce((acc, cur) => acc + cur, 0);
                // PAYMENT
                const totalJhtPayment = this.employees.map(employee => Number(employee.current_year_value.jht_payment)).reduce((acc, cur) => acc + cur, 0);
                const totalJkkPayment = this.employees.map(employee => Number(employee.current_year_value.jkk_payment)).reduce((acc, cur) => acc + cur, 0);
                const totalJkmPayment = this.employees.map(employee => Number(employee.current_year_value.jkm_payment)).reduce((acc, cur) => acc + cur, 0);
                const totalJpPayment = this.employees.map(employee => Number(employee.current_year_value.jp_payment)).reduce((acc, cur) => acc + cur, 0);
                // CURRENT
                const totalCurrentJht = this.employees.map(employee => Number(employee.current_year_value.jht)).reduce((acc, cur) => acc + cur, 0);
                const totalCurrentJkk = this.employees.map(employee => Number(employee.current_year_value.jkk)).reduce((acc, cur) => acc + cur, 0);
                const totalCurrentJkm = this.employees.map(employee => Number(employee.current_year_value.jkm)).reduce((acc, cur) => acc + cur, 0);
                const totalCurrentJp = this.employees.map(employee => Number(employee.current_year_value.jp)).reduce((acc, cur) => acc + cur, 0);

                return {
                    payment: {
                        jht: totalJhtPayment,
                        jkk: totalJkkPayment,
                        jkm: totalJkmPayment,
                        jp: totalJpPayment,
                    },
                    current: {
                        jht: totalCurrentJht,
                        jkk: totalCurrentJkk,
                        jkm: totalCurrentJkm,
                        jp: totalCurrentJp,
                    }
                }
            }
        },
        methods: {
            async onSaveBpjsValues() {
                let self = this;
                try {
                    const {
                        employees,
                        previousYear,
                        currentYear,
                    } = self;

                    self.loading = true;

                    const response = await axios.post('/bpjs-values', {
                        employees,
                        previous_year: previousYear,
                        current_year: currentYear,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        // const data = response?.data?.data?.career;
                        // const careers = response?.data?.data?.careers

                        // this.replace(careers);
                        // closeModal('#career_add_modal');
                        // this.resetForm();
                        toastr.success(message);

                        setTimeout(() => {
                            document.location.reload();
                        }, 500);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.loading = false;
                }
            },
            currencyFormat(number) {
                return new Intl.NumberFormat('De-de').format(number);
            }
        }
    })
</script>
@endsection