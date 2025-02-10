@extends('layouts.app')

@section('title', 'Deposit')

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
        <div class="card card-flush py-4 border-top border-primary border-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Detail Deposit</h2>
                </div>
                <div class="card-toolbar">
                    @if($deposit->redeemed == 1)
                    <div>
                        <span class="badge badge-success">Dikembalikan (Tgl: {{ $deposit->redeemed_date }})</span>
                    </div>
                    @else
                    <div>
                        <button class="btn btn-success" @click="openRedeemConfirmation({{ $deposit->id }})">
                            <i class="bi bi-wallet2"></i>
                            <span>Kembalikan Deposit</span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <!--begin::Input group-->

                <div class="row ">
                    <div class="col-md-6">
                        <div class="row align-items-center mb-3">
                            <div class="col-sm-4">
                                <label for="" class="required form-label">Tanggal Deposit</label>
                            </div>
                            <div class="col-sm-6">
                                {{ $deposit->date }}
                            </div>
                        </div>
                        <div class="row align-items-center mb-3">
                            <div class="col-sm-4">
                                <label for="" class="required form-label">Pegawai</label>
                            </div>
                            <div class="col-sm-8">
                                {{ $deposit->employee->name ?? '' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="" class="required form-label">Keterangan</label>
                            </div>
                            <div class="col-sm-8">
                                {{ $deposit->description }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row align-items-center justify-content-end pt-3">
                            <div class="col-sm-4 text-end">
                                <label for="" class="required form-label">Jumlah</label>
                            </div>
                            <div class="col-sm-5 text-end">
                                <span class="fs-1 fw-bold">Rp @{{ currencyFormat(totalItemAmount) }}</span>
                            </div>
                        </div>
                        <!-- <div class="row align-items-center justify-content-end mb-3">
                            <div class="col-sm-4">
                                <label for="" class="required form-label">Cicilan</label>
                            </div>
                            <div class="col-sm-5">
                                <div class="input-group input-group-sm">
                                    <input type="number" v-model="model.installment" class="form-control text-end" placeholder="0" />
                                    <span class="input-group-text" id="basic-addon2">Kali</span>
                                </div>
                            </div>
                        </div> -->
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
        <div class="card card-flush py-4 border-top border-light-primary border-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Cicilan Deposit</h2>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">

                <div class="table-responsive">
                    <!-- <table class="table gs-7 gy-7 gx-7 align-middle"> -->
                    <table class="table align-middle">
                        <thead class="fw-bold fs-6 text-gray-800 bg-light-primary">
                            <tr>
                                <td class="ps-2">Cicilan Ke</td>
                                <td class="text-end w-200px">Jumlah</td>
                                <td class="text-end pe-3">Sudah Dipotong</td>
                            </tr>
                        </thead>
                        <tbody class="fw-bold text-gray-800">
                            <!-- <tr is="loan-item" v-for="(item, index) in items" :key="index" :item="item" :index="index" :items="items"></tr> -->
                            <tr v-for="(item, index) in items">
                                <td class="ps-2">@{{ index + 1 }}</td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <!-- <input type="text" v-model="item.amount" class="form-control text-end payment-amount-input bg-light" readonly> -->
                                        <my-currency-input v-model="item.amount"></my-currency-input>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end align-items-center">
                                        <div class="form-check form-check-custom form-check-success">
                                            <input class="form-check-input" type="checkbox" v-model="item.paid" disabled />
                                        </div>
                                        <div v-if="item.paid" class="ms-3">
                                            <input type="date" v-model="item.paidDate" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </td>
                            </tr>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment.min.js"></script>
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
    const closeModal = (selector) => {
        const modalElement = document.querySelector(selector);
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();
    }

    const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;
    let items = <?php echo Illuminate\Support\Js::from($deposit->items) ?>;
    items = items.map(item => ({
        amount: item.amount,
        paid: Number(item.paid),
        paidDate: item.paid_date,
    }))

    Vue.component('my-currency-input', {
        props: ["value"],
        template: `
       
            <input type="text" class="form-control form-control-sm text-end bg-light" v-model="displayValue" @blur="isInputActive = false" @focus="isInputActive = true" readonly/>
            
        `,
        data: function() {
            return {
                isInputActive: false
            }
        },
        computed: {
            displayValue: {
                get: function() {
                    if (this.isInputActive) {
                        // Cursor is inside the input field. unformat display value for user
                        return this.value.toString()
                    } else {
                        // User is not modifying now. Format display value for user interface
                        // return "Rp " + this.value.toString().replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1\.")
                        return this.value.toString().replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1\.")
                    }
                },
                set: function(modifiedValue) {
                    // Recalculate value after ignoring "$" and "," in user input
                    let newValue = parseFloat(modifiedValue.replace(/[^\d\.]/g, ""))
                    // Ensure that it is not NaN
                    if (isNaN(newValue)) {
                        newValue = 0
                    }
                    // Note: we cannot set this.value as it is a "prop". It needs to be passed to parent component
                    // $emit the event so that parent component gets it
                    this.$emit('input', newValue)
                }
            }
        }
    });

    Vue.component('loan-item', {
        props: ['item', 'index', 'items'],
        template: `
        <tr>
            <td>@{{ index + 1 }}</td>
            <td>@{{ item.paymentDate }}</td>
            <td><input type="text" v-model="item.paymentAmount" class="form-control"></td>
            <td>@{{ item.remaining }}</td>
        </tr>
        `,
        watch: {
            'item': {
                handler(newValue) {
                    console.log(items);

                },
                deep: true,
            },
        },
    })

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                employees,
                model: {
                    amount: 0,
                    installment: 0,
                    effectiveDate: '',
                    employeeId: '',
                    loanNameId: '',
                    description: '',
                },
                items,
                selectedUnassignedEmployeesIds: [],
                submitLoading: false,
                cleave: {
                    thousandFormat: {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand',
                        numeralDecimalMark: ',',
                        delimiter: '.'
                    }
                }
            }
        },
        computed: {
            totalItemAmount() {
                return this.items.map(item => Number(item.amount)).reduce((acc, cur) => acc + cur, 0);
            }
        },
        methods: {
            openRedeemConfirmation(id) {
                const self = this;
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Deposit akan dikembalikan",
                    icon: 'warning',
                    reverseButtons: true,
                    showCancelButton: true,
                    confirmButtonText: 'Kembalikan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: "btn btn-success",
                        cancelButton: "btn btn-light"
                    },
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return self.redeemDeposit(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            redeemDeposit(id) {
                const self = this;
                return axios.post('/salary-deposits/' + id + '/redeem')
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        // toastr.success(message);

                        // setTimeout(() => {
                        //     document.location.reload();
                        // }, 500);

                        // $newJournal = new Journal();
                        // $newJournal - > number = $journalNumber;
                        // $newJournal - > date = $request - > date;
                        // $newJournal - > description = $request - > description;
                        // $newJournal - > total = $request - > total;
                        // $newJournal - > source = $request - > source;
                        // $newJournal - > partner_id = $request - > partner_id;
                        // $newJournal - > tenant_id = $request - > tenant_id;
                        // $newJournal - > is_partner_or_tenant = $request - > is_partner_or_tenant;
                        // $newJournal - > journalable_id = $request - > journalable_id;

                        // $newJournal - > save();

                        // $transactionList = $request - > transactions;

                        // foreach($transactionList as $transaction) {
                        //     $newTransaction = new AccountTransaction();
                        //     $newTransaction - > date = $request - > date;
                        //     $newTransaction - > amount_type = $transaction['type'];
                        //     $newTransaction - > amount = $transaction['amount'];
                        //     $newTransaction - > journal_id = $newJournal - > id;
                        //     $newTransaction - > description = $transaction['description'] ?? '-';
                        //     $newTransaction - > account_id = $transaction['account_id'];
                        //     $newTransaction - > save();
                        // }
                        const deposit = response?.data?.data || null;

                        const description = 'Pengembalian Deposit ' + (deposit?.employee?.name || 'NAMA_PEGAWAI') + ' ' + (deposit?.employee?.description || '');

                        const amount = deposit?.redeemed_amount || 0;

                        const journalPayload = {
                            date: '{{ date("Y-m-d") }}',
                            description: description,
                            total: amount,
                            source: 'MAGENTA_HRD_DEPOSIT',
                            partner_id: null,
                            tenant_id: null,
                            is_partner_or_tenant: null,
                            journalable_id: deposit?.id || null,
                            transactions: [{
                                    type: 'DEBIT',
                                    amount: amount,
                                    description: description,
                                    account_id: 326,
                                },
                                {
                                    type: 'CREDIT',
                                    amount: amount,
                                    description: description,
                                    account_id: 5,
                                },
                            ],
                        }

                        return axios.post('{{ env("AERPLUS_URL") }}/api/v1/journals', journalPayload)
                    })
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        toastr.success(message);

                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    })
                    .catch(function(error) {
                        let message = error?.response?.data?.message;
                        if (!message) {
                            message = 'Something wrong...'
                        }
                        toastr.error(message);
                    });
            },
            async onSubmit() {
                let self = this;
                try {
                    self.submitLoading = true;
                    const items = self.items;
                    const {
                        effectiveDate,
                        amount,
                        installment,
                        employeeId,
                        loanNameId,
                        description,
                    } = self.model;

                    const response = await axios.post('/salary-deposits', {
                        effective_date: effectiveDate,
                        amount: self.totalItemAmount,
                        installment: items.length,
                        employee_id: employeeId,
                        description,
                        items
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.submitLoading = false;
                }
            },
            addItem() {
                this.items.push({
                    amount: 50000,
                    paid: false,
                    paidDate: '',
                });
            },
            deleteItem(index) {
                this.items.splice(index, 1);
            },
            currencyFormat(number) {
                return new Intl.NumberFormat('De-de').format(number);
            }
        },
        watch: {},
        directives: {
            cleave: {
                inserted: (el, binding) => {
                    el.cleave = new Cleave(el, binding.value || {})
                },
                update: (el) => {
                    const event = new Event('input', {
                        bubbles: true
                    });
                    setTimeout(function() {
                        el.value = el.cleave.properties.result
                        el.dispatchEvent(event)
                    }, 100);
                }
            }
        }
    })
</script>
<script>
    $(function() {
        $('#employee-select').on('change', function() {
            // console.log('changed');
            const value = $(this).val();
            app.$data.model.employeeId = value;
        });
    })
</script>
@endsection