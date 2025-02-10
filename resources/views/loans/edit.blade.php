@extends('layouts.app')

@section('title', 'Pinjaman')

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
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Pinjaman</h2>
                </div>
                <div class="card-toolbar">

                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <!--begin::Input group-->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row g-3 mb-10">
                            <div class="col-md-6">
                                <label for="" class="required form-label">Tanggal Berlaku</label>
                                <input type="date" v-model="model.effectiveDate" class="form-control" placeholder="" id="" />
                            </div>
                            <div class="col-md-6">
                                <label for="" class="required form-label">Nama Pinjaman</label>
                                <select v-model="model.loanNameId" class="form-select">
                                    <option value="">Pilih Nama Pinjaman</option>
                                    @foreach($loan_names as $loan_name)
                                    <option value="{{ $loan_name->id }}">{{ $loan_name->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-10">
                            <label for="" class="required form-label">Pegawai</label>
                            <select v-model="model.employeeId" class="form-select" id="employee-select" data-control="select2" data-placeholder="Pilih Pegawai">
                                <option></option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3 mb-10">
                            <div class="col-md-6">
                                <label for="" class="required form-label">Jumlah</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">Rp</span>
                                    <input type="text" v-model="model.amount" class="form-control text-end" placeholder="0" />
                                </div>

                            </div>
                            <div class="col-md-6">
                                <label for="" class="required form-label">Cicilan</label>
                                <div class="input-group">
                                    <input type="number" v-model="model.installment" class="form-control text-end" placeholder="0" />
                                    <span class="input-group-text" id="basic-addon2">Bulan</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="" class="required form-label">Keterangan</label>
                            <textarea v-model="model.description" class="form-control" placeholder=""></textarea>
                        </div>
                    </div>
                </div>
                <!--end::Input group-->
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
                    <table class="table gs-7 gy-7 gx-7 align-middle">
                        <thead class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                            <tr>
                                <td>Cicilan Ke</td>
                                <td class="text-center">Tanggal Pembayaran</td>
                                <td class="text-end">Jumlah</td>
                                <td class="text-end">Sisa Pinjaman</td>
                            </tr>
                        </thead>
                        <tbody class="fw-bold text-gray-800">
                            <!-- <tr is="loan-item" v-for="(item, index) in items" :key="index" :item="item" :index="index" :items="items"></tr> -->
                            <tr v-for="(item, index) in items">
                                <td>@{{ index + 1 }}</td>
                                <td class="text-center">@{{ item.paymentDate }}</td>
                                <td class="text-end">
                                    <div v-if="item.editable">
                                        <input type="text" :value="item.paymentAmount" class="form-control text-end payment-amount-input" @change="onChangePaymentAmount($event, item, index)">
                                    </div>
                                    <div v-else>
                                        <span><span v-if="(item.salary_item_count && item.salary_item_count > 0) || item.paid == 1" class="badge badge-light-success me-3">Dibayar</span> @{{ currencyFormat(item.paymentAmount) }}</span>
                                    </div>
                                </td>
                                <td class="text-end">@{{ currencyFormat(item.remaining) }}</td>
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
        <div class="d-flex justify-content-end">
            <!--begin::Button-->
            <button type="button" class="btn btn-primary" :data-kt-indicator="submitLoading ? 'on' : null" :disabled="submitLoading" @click="onSubmit">
                <span class="indicator-label">Simpan</span>
                <span class="indicator-progress">Please wait...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
            <!--end::Button-->
        </div>
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
    const loanItems = <?php echo Illuminate\Support\Js::from($loan_items) ?>;

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
                    amount: '{{ $loan->amount }}',
                    installment: '',
                    effectiveDate: '{{ $loan->effective_date }}',
                    employeeId: '{{ $loan->employee_id }}',
                    loanNameId: '{{ $loan->loan_name_id }}',
                    description: '{{ $loan->description }}',
                    lastIndex: '{{ $last_index }}',
                },
                items: loanItems,
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
            changeDataModel() {
                const {
                    installment,
                    amount,
                    effectiveDate
                } = this.model;
                return {
                    installment,
                    amount,
                    effectiveDate,
                }
            },
            selectedEmployee() {
                const self = this;
                const [employee] = this.employees.filter(employee => employee.id == self.model.employeeId)
                return employee;
            },
            journalPayload() {
                const description = 'Kasbon ' + (this.selectedEmployee?.name || 'NAMA_PEGAWAI') + ' ' + this.model.description;
                return {
                    // number: '',
                    date: this.model.effectiveDate,
                    description: description,
                    total: this.model.amount,
                    source: 'MAGENTA_HRD_LOAN',
                    partner_id: null,
                    tenant_id: null,
                    is_partner_or_tenant: null,
                    transactions: [{
                            type: 'DEBIT',
                            amount: this.model.amount,
                            description: description,
                            account_id: 83,
                        },
                        {
                            type: 'CREDIT',
                            amount: this.model.amount,
                            description: description,
                            account_id: 5,
                        },
                    ],
                }
            }
            // items() {
            //     const newItems = [];
            //     const {
            //         amount,
            //         effectiveDate,
            //         installment
            //     } = this.model;

            //     if (amount && effectiveDate && installment) {
            //         if (!isNaN(installment) && !isNaN(amount)) {
            //             let paymentAmount = Math.floor(amount / installment);
            //             let remaining = amount;
            //             let totalToMonth = 0;
            //             for (let i = 0; i < installment; i++) {
            //                 let newPaymentAmount = paymentAmount;
            //                 if (i == installment - 1) {
            //                     newPaymentAmount = remaining;
            //                 }
            //                 totalToMonth += newPaymentAmount;
            //                 remaining = amount - totalToMonth;

            //                 const item = {
            //                     paymentDate: '2022-01-12',
            //                     paymentAmount: newPaymentAmount,
            //                     remaining,
            //                 }

            //                 newItems.push(item);
            //             }
            //         }
            //     }

            //     return newItems;
            // },
        },
        methods: {
            onChangePaymentAmount(event, item, index) {
                const self = this;
                const value = event.target.value;

                item.paymentAmount = value;
                const loanAmount = this.model.amount;
                const installment = this.model.installment;
                const effectiveDate = this.model.effectiveDate;
                // const items = this.items;
                let newItems = [];
                const previousRemaining = index > 0 ? self.items[index - 1].remaining : loanAmount;
                if (value > previousRemaining) {
                    item.paymentAmount = previousRemaining;
                    newItems = self.items.slice(0, index + 1);
                    // self.items = newItems;
                    // item.remaining = 0;
                } else {
                    if (value > item.remaining) {
                        // item.paymentAmount = item.remaining;
                        newItems = self.items.slice(0, index + 1);
                        // Solution:
                        //     1. Get hasil value - remaining;
                        // 2. Looping hasil sisa sampai habis
                        const difference = previousRemaining - value;
                        // console.log(previousRemaining, value, difference);
                        // const basicPaymentAmount = Math.round(loanAmount / installment);

                        newItems.push({
                            paymentDate: '',
                            paymentAmount: difference,
                            remaining: 0,
                            editable: true,
                        })
                    } else {
                        totalPaymentAmount = self.items.reduce((acc, cur) => acc + Number(cur.paymentAmount), 0);
                        const unpaidLoan = loanAmount - totalPaymentAmount;
                        const basicPaymentAmount = Math.round(loanAmount / installment);
                        let items = [];
                        if (unpaidLoan > basicPaymentAmount) {
                            const unpaidLoanRatio = Math.floor(unpaidLoan / basicPaymentAmount);

                            for (let i = 0; i < unpaidLoanRatio; i++) {
                                items.push({
                                    paymentDate: '',
                                    paymentAmount: basicPaymentAmount,
                                    remaining: 0,
                                    editable: true,
                                })
                            }

                            if (unpaidLoan % basicPaymentAmount !== 0) {
                                items.push({
                                    paymentDate: '',
                                    paymentAmount: unpaidLoan % basicPaymentAmount,
                                    remaining: 0,
                                    editable: true,
                                })
                            }
                        } else {
                            items.push({
                                paymentDate: '',
                                paymentAmount: unpaidLoan,
                                remaining: 0,
                                editable: true,
                            })
                        }

                        newItems = [...self.items, ...items];
                    }
                }

                // console.log(newItems);
                let loanAmountRemaining = loanAmount;

                const effectiveDateObject = new Date(effectiveDate);
                const effectiveDay = effectiveDateObject.getDate();
                const effectiveMonth = effectiveDateObject.getMonth() + 1;
                const effectiveYear = effectiveDateObject.getFullYear();
                const effectiveStringMonth = effectiveMonth < 10 ? '0' + effectiveMonth : effectiveMonth;

                const firstPaymentDate = effectiveDay < 26 ? `${effectiveYear}-${effectiveStringMonth}-25` : moment(`${effectiveYear}-${effectiveStringMonth}-25`).add(1, 'M').toString();


                const finalItems = newItems.map(function(item, index) {
                    const paymentDate = moment(firstPaymentDate).add(index, 'M').format('YYYY-MM-DD').toString();
                    loanAmountRemaining -= Number(item.paymentAmount);
                    return {
                        paymentDate,
                        paymentAmount: item.paymentAmount,
                        remaining: loanAmountRemaining,
                        editable: typeof item.editable !== "undefined" ? item.editable : true,
                        salary_item_count: item.salary_item_count,
                    }
                })

                self.items = finalItems;

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
                        lastIndex,
                    } = self.model;

                    const response = await axios.post('/loans/{{ $loan->id }}', {
                        effective_date: effectiveDate,
                        amount,
                        installment: items.length,
                        employee_id: employeeId,
                        loan_name_id: loanNameId,
                        description,
                        items,
                        last_index: lastIndex,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const AERPLUS_DIVISION_ID = 12;

                        if (self.selectedEmployee?.office?.division_id == AERPLUS_DIVISION_ID) {
                            const data = response?.data?.data;
                            toastr.info(message + '. Membuat jurnal...');

                            const journalResponse = await axios.post('http://aerplus-central-clone.test/api/v1/journals', {
                                ...self.journalPayload,
                                journalable_id: data?.id
                            });

                            if (journalResponse) {
                                message = response?.data?.message;
                                if (!message) {
                                    message = 'Data berhasil disimpan'
                                }

                                toastr.success(message);
                            }
                        } else {
                            toastr.success(message);
                        }
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
            showFinalData() {
                console.log(this.items);
            },
            currencyFormat(number) {
                return new Intl.NumberFormat('De-de').format(number);
            }
        },
        watch: {
            changeDataModel: {
                handler(newValue) {
                    const self = this
                    console.log('watched');

                    const newItems = [];
                    const {
                        amount,
                        effectiveDate,
                        installment
                    } = this.model;

                    if (amount && effectiveDate && installment) {
                        if (!isNaN(installment) && !isNaN(amount)) {
                            let paymentAmount = Math.round(amount / installment);
                            let remaining = amount;
                            let totalToMonth = 0;

                            const effectiveDateObject = new Date(effectiveDate);
                            const effectiveDay = effectiveDateObject.getDate();
                            const effectiveMonth = effectiveDateObject.getMonth() + 1;
                            const effectiveYear = effectiveDateObject.getFullYear();
                            const effectiveStringMonth = effectiveMonth < 10 ? '0' + effectiveMonth : effectiveMonth;

                            const firstPaymentDate = effectiveDay < 26 ? `${effectiveYear}-${effectiveStringMonth}-25` : moment(`${effectiveYear}-${effectiveStringMonth}-25`).add(1, 'M').toString();

                            for (let i = 0; i < installment; i++) {
                                let newPaymentAmount = paymentAmount;
                                if (i == installment - 1) {
                                    newPaymentAmount = remaining;
                                }
                                totalToMonth += newPaymentAmount;
                                remaining = amount - totalToMonth;

                                const paymentDate = moment(firstPaymentDate).add(i, 'M').format('YYYY-MM-DD').toString();

                                const item = {
                                    paymentDate: paymentDate,
                                    paymentAmount: newPaymentAmount,
                                    remaining,
                                    editable: true,
                                }

                                newItems.push(item);
                            }
                        }
                    }

                    this.items = newItems;
                },
                deep: true,
            },
            // items: {
            //     handler(newValue) {
            //         console.log(newValue)
            //     },
            //     deep: true,
            // }
        },
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