@extends('layouts.app')

@section('title', 'Pinjaman')

@section('prehead')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('pagestyle')
<style>
    .dataTables_empty {
        display: none;
    }

    #table-bulk-hold tr {
        border-bottom-width: 1px !important;
    }

    #table-bulk-hold tr,
    #table-bulk-hold td {
        border-color: rgb(239, 242, 245) !important;
    }
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
            <h1 class="mb-0">Daftar Pinjaman</h1>
            <p class="text-muted mb-0">Semua pinjaman pegawai</p>
        </div>
        @can('create', App\Models\Loan::class)
        <div class="d-flex">
            <div class="me-3">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Aksi
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkHoldLoanModal">Bulk Hold Pinjaman</a></li>
                    </ul>
                </div>
            </div>
            <div>
                <a href="/loans/create" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Pinjaman</a>
            </div>
        </div>
        @endcan
    </div>
    <div class="separator my-5" style="border-bottom-width: 3px;"></div>
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary" style="min-height: 5px;"></div>
                <div class="card-body p-6">
                    <p class="fs-5 fw-bold text-gray-700">Total Pinjaman</p>
                    <div class="fs-1 fw-bold">Rp {{ number_format($statistic['total_loans'], 0, ',', '.') }}</div>
                    <div class="fs-7 text-gray-700">Dari total <strong>{{ number_format($statistic['total_loans_count'], 0, ',', '.') }}</strong> pinjaman</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary" style="min-height: 5px;"></div>
                <div class="card-body p-6">
                    <p class="fs-5 fw-bold text-gray-700">Pinjaman Lunas</p>
                    <div class="fs-1 fw-bold">Rp {{ number_format($statistic['total_paid_loans'], 0, ',', '.') }}</div>
                    <div class="fs-7 text-gray-700">Dari total <strong>{{ number_format($statistic['total_paid_loans_count'], 0, ',', '.') }}</strong> cicilan</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary" style="min-height: 5px;"></div>
                <div class="card-body p-6">
                    <p class="fs-5 fw-bold text-gray-700">Pinjaman Belum Lunas</p>
                    <div class="fs-1 fw-bold">Rp {{ number_format($statistic['total_unpaid_loans'], 0, ',', '.') }}</div>
                    <div class="fs-7 text-gray-700">Dari total <strong>{{ number_format($statistic['total_unpaid_loans_count'], 0, ',', '.') }}</strong> cicilan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <!--begin::Card body-->
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="d-flex align-items-center position-relative mb-5">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <input type="text" data-kt-customer-table-filter="search" class="form-control form-control-sm w-250px ps-15" placeholder="Cari Pinjaman" />
                    </div>
                </div>
                <div>
                    <button class="btn btn-light-primary btn-sm" disabled>
                        <i class="bi bi-funnel-fill"></i>
                        <span class="align-middle">Filter</span>
                    </button>
                </div>
            </div>
            <!--begin::Table-->
            <table class="use-datatable table align-middle table-row-dashed fs-7" id="loan_table">
                <!--begin::Table head-->
                <thead class="bg-light-primary">
                    <!--begin::Table row-->
                    <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                        <th class="ps-2">Pegawai</th>
                        <th>Tanggal</th>
                        <th class="text-end">Awal Cicilan</th>
                        <th class="text-end">Akhir Cicilan</th>
                        <th class="text-end">Jumlah (Rp)</th>
                        <th class="text-center">Cicilan (Bulan)</th>
                        <th class="text-end">Status</th>
                        <th class="text-end min-w-70px pe-2">Actions</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="fw-bold text-gray-700 fs-7">

                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
    <!-- Modal -->
    <div class="modal fade" id="bulkHoldLoanModal" tabindex="-1" aria-labelledby="bulkHoldLoanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="bulkHoldLoanModalLabel">Bulk Hold Pinjaman</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-secondary">
                        Pilih bulan cicilan pinjaman yang akan di-<em>hold</em> kemudian tekan tombol <strong>proses</strong> untuk memilih pinjaman
                    </div>
                    <div>
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <label for="" class="form-label">Bulan Cicilan</label>
                                <input type="month" v-model="model.yearMonth" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary btn-sm" :data-kt-indicator="getLoansByMonthLoading ? 'on' : null" :disabled="getLoansByMonthLoading" @click="getLoansByMonth">
                                    <span class="indicator-label">Proses</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                                <!-- <button class="btn btn-primary btn-sm">Proses</button> -->
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 position-relative">
                        <div v-cloak v-if="getLoansByMonthLoading" class="d-flex justify-content-center align-items-center" style="position: absolute; top:0; left: 0; bottom: 0; right: 0;"><span class="badge badge-secondary">Loading..</span></div>
                        <table class="table table-bordered" id="table-bulk-hold">
                            <thead>
                                <tr>
                                    <th style="width: 50px;"></th>
                                    <th>Pegawai</th>
                                    <th>Tanggal Pinjaman</th>
                                    <th class="text-end pe-2">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-cloak v-for="(loan, index) in bulkHoldLoans">
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" v-model="model.selectedHoldLoans" :value="loan.id" :id="'checkLoan' + loan.id" />
                                                <label class="form-check-label" :for="'checkLoan' + loan.id">
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>@{{ loan?.employee?.name }}</td>
                                    <td>@{{ loan?.effective_date }}</td>
                                    <td class="text-end pe-2">Rp @{{ Number(loan?.amount).toLocaleString('De-de') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" :disabled="model.selectedHoldLoans.length < 1" @click="onSaveBulkHoldLoan">Hold <span v-cloak>@{{ model.selectedHoldLoans.length }}</span> Pinjaman</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection

@section('pagescript')
<script>
    let loanDatatable = null;
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

        loanDatatable = $('#loan_table').DataTable({
            // autoWidth: false,
            order: false,
            ajax: '/loans/datatable/loans',
            processing: true,
            serverSide: true,
            // columnDefs: [{
            //     width: 500,
            //     targets: 0
            // }],
            columns: [{
                    data: 'employee',
                    name: 'employee.name',
                    class: "ps-2",
                    // width: 500,
                },
                {
                    data: 'formatted_date',
                    name: 'effective_date',
                    class: "text-center"
                },
                {
                    data: 'first_installment',
                    class: "text-center",
                    searchable: false,
                },
                {
                    data: 'last_installment',
                    class: "text-center",
                    searchable: false,
                },
                {
                    data: 'formatted_amount',
                    name: 'amount',
                    class: "text-end"
                },
                {
                    data: 'installment',
                    name: 'installment',
                    class: 'text-center',
                    // searchable: false,
                    // name: 'note',
                },
                {
                    data: 'status',
                    // name: 'approval_status',
                    class: "text-center",
                    searchable: false,
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    class: "text-end"
                },
            ],
            "fnDrawCallback": function(oSettings) {
                // console.log(app)
                // app.$forceUpdate();
                // $('[data-toggle="popover"]').popover();
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
                var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl)
                })

                // var popover = new bootstrap.Popover(document.querySelector('.popover-dismiss'), {
                //     trigger: 'focus'
                // });
            }
        });

        // const handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            loanDatatable.search(e.target.value).draw();
        });

        function sendDeleteRequest(id) {
            // const self = this;
            return axios.delete('/loans/' + id)
                .then(function(response) {
                    let message = response?.data?.message;
                    if (!message) {
                        message = 'Data berhasil disimpan'
                    }
                    // self.deleteOffice(id);
                    // redrawDatatable();
                    toastr.success(message);
                    loanDatatable.ajax.reload();
                    // setTimeout(() => {
                    //     document.location.reload();
                    // }, 500);
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
        }

        $('#loan_table').on('click', '.btn-delete', function() {
            // console.log('clicked');
            const id = $(this).attr('data-id');
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
                    return sendDeleteRequest(id);
                },
                allowOutsideClick: () => !Swal.isLoading(),
                backdrop: true,
            })
        })
    })

    let app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                bulkHoldLoans: [],
                model: {
                    yearMonth: '',
                    selectedHoldLoans: [],
                },
                getLoansByMonthLoading: false,
            }
        },
        methods: {
            async getLoansByMonth() {
                this.getLoansByMonthLoading = true;
                try {
                    const yearMonth = this.model.yearMonth;
                    const response = await axios.get('/loans/resources/get-loans-by-month?year_month=' + yearMonth);
                    if (response) {
                        // console.log(response);
                        this.bulkHoldLoans = response?.data?.data || [];
                        this.model.selectedHoldLoans = (response?.data?.data || []).map(loan => loan.id);
                    }
                } catch (error) {
                    // console.log(error);
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    this.getLoansByMonthLoading = false;
                }
            },
            onSaveBulkHoldLoan() {
                const bulkHoldLoanModal = bootstrap.Modal.getInstance(document.querySelector('#bulkHoldLoanModal'));

                // console.log(bulkHoldLoanModal);
                // return;
                const self = this;
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Pinjaman akan di-hold sesuai bulan cicilan yang dipilih",
                    icon: 'warning',
                    reverseButtons: true,
                    showCancelButton: true,
                    confirmButtonText: 'Hold',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: "btn btn-primary",
                        cancelButton: "btn btn-light"
                    },
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return axios.post('/loans/action/bulk-hold', {
                                loans_ids: self.model.selectedHoldLoans,
                                year_month: self.model.yearMonth,
                            })
                            .then(function(response) {
                                console.log(response);

                                let message = response?.data?.message;
                                if (!message) {
                                    message = 'Data berhasil disimpan'
                                }
                                // self.deleteOffice(id);
                                // redrawDatatable();
                                bulkHoldLoanModal.hide();
                                toastr.success(message);
                                if (loanDatatable) {
                                    loanDatatable.ajax.reload();
                                }
                                // setTimeout(() => {
                                //     document.location.reload();
                                // }, 500);
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
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            }

        }
    });
</script>
<script>

</script>
@endsection