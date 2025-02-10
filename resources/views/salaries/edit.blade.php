@extends('layouts.app')

@section('title', 'Gaji Bulanan')

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
    <div class="card mb-5 mb-xxl-10">
        <div class="card-body p-lg-20">
            <!--begin::Layout-->
            <div class="text-end mb-10">
                <button type="button" :data-kt-indicator="loading ? 'on' : null" class="btn btn-success" @click="save">
                    <span class="indicator-label"><i class="bi bi-check fs-1"></i> Simpan Perubahan</span>
                    <span class="indicator-progress">Mengirim data...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
            <div class="d-flex flex-column flex-xl-row">
                <!--begin::Content-->
                <div class="flex-lg-row-fluid me-xl-18 mb-10 mb-xl-0">
                    <!--begin::Invoice 2 content-->
                    <div class="mt-n1">
                        <!--begin::Top-->
                        <div class="d-flex justify-content-between align-items-center pb-10">
                            <!--begin::Logo-->
                            <div class="fw-bolder fs-3 text-gray-800">Slip Gaji September 2022</div>
                            <!--end::Logo-->
                            <!--begin::Action-->
                            <a href="/salaries/{{ $salary->id }}/print" target="_blank" class="btn btn-sm btn-secondary"><i class="bi bi-printer"></i> Cetak</a>
                            <!--end::Action-->
                        </div>
                        <!--end::Top-->
                        <!--begin::Wrapper-->
                        <div class="m-0">
                            <!--begin::Label-->

                            <!--end::Label-->
                            <!--begin::Content-->
                            <div class="flex-grow-1">
                                <!--begin::Table-->
                                <div class="table-responsive border-bottom mb-3">
                                    <table class="table mb-3">
                                        <thead>
                                            <tr class="border-bottom fs-6 fw-bolder text-muted">
                                                <th class="min-w-175px pb-2">Komponen</th>
                                                <th class="min-w-100px text-end pb-2">Amount</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td v-if="!salaryItems.length && !newSalaryItems.length" colspan="3" class="text-center">Tidak ada komponen gaji</td>
                                            </tr>
                                            <tr v-for="(salaryItem, index) in salaryItems" class="fw-bolder text-gray-700 fs-5 text-end">
                                                <td class="d-flex align-items-center pt-6">
                                                    @{{ salaryItem?.name }} <span class="badge ms-3" :class="salaryItem?.type == 'income' ? 'badge-light-success' : 'badge-light-danger'">@{{ salaryItem?.type == 'income' ? 'Pendapatan' : 'Potongan' }}</span>
                                                </td>
                                                <!-- <td v-if="salaryItem?.type == 'income'" class="text-dark fw-boldest pt-6">Rp @{{ currencyFormat(salaryItem?.amount) }}</td>
                                                <td v-else class="text-dark fw-boldest pt-6">(Rp @{{ currencyFormat(salaryItem?.amount) }})</td> -->
                                                <td v-if="salaryItem?.type == 'income'">
                                                    <div class="input-group input-group-sm mb-5">
                                                        <span class="input-group-text" id="basic-addon1">Rp</span>
                                                        <input type="text" :value="salaryItem.amount" class="form-control form-control-solid text-end" readonly>
                                                    </div>
                                                </td>
                                                <td v-else>
                                                    <div class="d-flex align-items-center">
                                                        <div class="input-group input-group-sm mb-5">
                                                            <span class="input-group-text" id="basic-addon1">Rp</span>
                                                            <input type="text" :value="salaryItem.amount" class="form-control form-control-solid text-end" readonly>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-icon btn-danger btn-sm" @click="removeSalaryItem(index)"><i class="bi bi-trash"></i></button>
                                                </td>
                                                <!-- <td>
                                                    <select v-model="salaryItem.id" class="form-select form-select-sm">
                                                        <option value="">Pilih Komponen Gaji</option>
                                                        @foreach($salary_components as $salary_component)
                                                        <option value="{{ $salary_component->id }}">{{ $salary_component->name }} ({{ $salary_component->type == 'income' ? 'Pendapatan' : 'Potongan' }})</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm mb-5">
                                                        <span class="input-group-text" id="basic-addon1">Rp</span>
                                                        <input type="text" v-model="salaryItem.amount" class="form-control">
                                                    </div>
                                                </td> -->
                                            </tr>
                                            <tr v-for="(newSalaryItem, index) in newSalaryItems" :key="index">
                                                <td>
                                                    <select v-model="newSalaryItem.salaryComponentId" class="form-select form-select-sm">
                                                        <option value="">Pilih Komponen Gaji</option>
                                                        @foreach($salary_components as $salary_component)
                                                        <option value="{{ $salary_component->id }}">{{ $salary_component->name }} ({{ $salary_component->type == 'income' ? 'Pendapatan' : 'Potongan' }})</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm mb-5">
                                                        <span class="input-group-text" id="basic-addon1">Rp</span>
                                                        <input type="text" v-model="newSalaryItem.amount" class="form-control text-end">
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-icon btn-danger btn-sm" @click="removeNewSalaryItem(index)"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-end mb-9">
                                    <!-- <div class="dropdown">
                                        <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-plus"></i> Tambah
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a class="dropdown-item fw-bold fs-7 text-success" href="#">Pendapatan</a></li>
                                            <li><a class="dropdown-item  fw-bold fs-7 text-danger" href="#">Potongan</a></li>
                                        </ul>
                                    </div> -->
                                    <button class="btn btn-light btn-sm" type="button" @click="addNewSalaryItem">
                                        <i class="bi bi-plus"></i> Tambah
                                    </button>
                                </div>
                                <!--end::Table-->
                                <!--begin::Container-->
                                <div class="d-flex justify-content-end">
                                    <!--begin::Section-->
                                    <div class="mw-300px">
                                        <!--begin::Item-->
                                        <div class="d-flex flex-stack mb-3">
                                            <!--begin::Accountname-->
                                            <div class="fw-bold pe-10 text-gray-600 fs-7">Pendapatan</div>
                                            <!--end::Accountname-->
                                            <!--begin::Label-->
                                            <div class="text-end fw-bolder fs-6 text-gray-800">Rp @{{ currencyFormat(totalIncomes) }}</div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Item-->
                                        <!--begin::Item-->
                                        <div class="d-flex flex-stack mb-3">
                                            <!--begin::Accountname-->
                                            <div class="fw-bold pe-10 text-gray-600 fs-7">Potongan</div>
                                            <!--end::Accountname-->
                                            <!--begin::Label-->
                                            <div class="text-end fw-bolder fs-6 text-gray-800">(Rp @{{ currencyFormat(totalDeductions) }})</div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Item-->
                                        <!--begin::Item-->
                                        <div class="d-flex flex-stack border-top border-bottom p-4 bg-light">
                                            <!--begin::Code-->
                                            <div class="fw-bold pe-10 text-gray-600 fs-5">Take Home Pay</div>
                                            <!--end::Code-->
                                            <!--begin::Label-->
                                            <div class="text-end fw-bolder fs-4 text-gray-800">Rp @{{ currencyFormat(takeHomePay) }}</div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Item-->
                                    </div>
                                    <!--end::Section-->
                                </div>
                                <!--end::Container-->
                            </div>
                            <!--end::Content-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Invoice 2 content-->
                </div>
                <!--end::Content-->
                <!--begin::Sidebar-->
                <div class="m-0">
                    <!--begin::Invoice 2 sidebar-->
                    <div class="d-print-none border border-dashed border-gray-300 card-rounded h-lg-100 min-w-md-350px p-9 bg-lighten">
                        <!--begin::Title-->
                        <h6 class="mb-8 fw-boldest text-gray-600 text-hover-primary">DETAIL PEGAWAI</h6>
                        <!--end::Title-->
                        <!--begin::Item-->
                        <div class="mb-6">
                            <div class="fw-bold text-gray-600 fs-7">Nomor:</div>
                            <div class="fw-bolder text-gray-800 fs-6">{{ $salary->employee->number ?? '-' }}</div>
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="mb-6">
                            <div class="fw-bold text-gray-600 fs-7">Nama:</div>
                            <div class="fw-bolder text-gray-800 fs-6">
                                {{ $salary->employee->name ?? '-' }}
                            </div>
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="mb-6">
                            <div class="fw-bold text-gray-600 fs-7">Perusahaan:</div>
                            <div class="fw-bolder text-gray-800 fs-6">
                                {{ $salary->employee->office->division->company->name ?? '-' }}
                            </div>
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="mb-6">
                            <div class="fw-bold text-gray-600 fs-7">Divisi:</div>
                            <div class="fw-bolder text-gray-800 fs-6">
                                {{ $salary->employee->office->division->name ?? '-' }}
                            </div>
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="mb-6">
                            <div class="fw-bold text-gray-600 fs-7">Job Title:</div>
                            <div class="fw-bolder text-gray-800 fs-6">
                                {{ $salary->employee->activeCareer->jobTitle->name ?? '-' }}
                            </div>
                        </div>
                        <!--end::Item-->
                    </div>
                    <!--end::Invoice 2 sidebar-->
                </div>
                <!--end::Sidebar-->
            </div>
            <!--end::Layout-->
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
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
    const salaryItems = <?php echo Illuminate\Support\Js::from($salary->items) ?>;
    const salaryComponents = <?php echo Illuminate\Support\Js::from($salary_components) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                salaryItems,
                salaryComponents,
                newSalaryItems: [],
                cleave: {
                    thousandFormat: {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand',
                        numeralDecimalMark: ',',
                        delimiter: '.'
                    },
                    minusThousandFormat: {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand',
                        numeralDecimalMark: ',',
                        delimiter: '.',
                        prefix: '-',
                    }
                },
                loading: false,
            }
        },
        computed: {
            totalIncomes() {
                const self = this;
                const salaryItemIncomes = self.salaryItems
                    .filter(salaryItem => salaryItem.type == 'income')
                    .reduce((acc, cur) => acc + Number(cur.amount), 0);

                const newSalaryItemIncomes = self.newSalaryItems
                    .filter(newSalaryItem => {
                        const {
                            salaryComponentId
                        } = newSalaryItem;

                        const [selectedSalaryComponent] = self.salaryComponents.filter(salaryComponent => salaryComponent.id == salaryComponentId);

                        if (selectedSalaryComponent) {
                            return selectedSalaryComponent.type == 'income';
                        }

                        return false;
                    })
                    .reduce((acc, cur) => acc + Number(cur.amount), 0);

                return salaryItemIncomes + newSalaryItemIncomes;
            },
            totalDeductions() {
                const self = this;
                const salaryItemIncomes = self.salaryItems
                    .filter(salaryItem => salaryItem.type == 'deduction')
                    .reduce((acc, cur) => acc + Number(cur.amount), 0);

                const newSalaryItemIncomes = self.newSalaryItems
                    .filter(newSalaryItem => {
                        const {
                            salaryComponentId
                        } = newSalaryItem;

                        const [selectedSalaryComponent] = self.salaryComponents.filter(salaryComponent => salaryComponent.id == salaryComponentId);

                        if (selectedSalaryComponent) {
                            return selectedSalaryComponent.type == 'deduction';
                        }

                        return false;
                    })
                    .reduce((acc, cur) => acc + Number(cur.amount), 0);

                return salaryItemIncomes + newSalaryItemIncomes;
            },
            takeHomePay() {
                return this.totalIncomes - this.totalDeductions;
            },
            formattedNewSalaryItems() {
                const self = this;
                const {
                    salaryComponents
                } = self;
                const newSalaryItems = self.newSalaryItems.map(newSalaryItem => {
                    const [salaryComponent] = salaryComponents.filter(salaryComponent => salaryComponent.id == newSalaryItem.salaryComponentId);

                    if (salaryComponent) {
                        newSalaryItem.component = salaryComponent;
                    } else {
                        newSalaryItem.component = null;
                    }

                    return newSalaryItem;
                });

                return newSalaryItems;
            }
        },
        methods: {
            async save() {
                let self = this;
                try {
                    const {
                        salaryItems,
                        formattedNewSalaryItems,
                    } = self;

                    self.loading = true;

                    const response = await axios.post(`/salaries/{{ $salary->id }}`, {
                        salary_items: salaryItems,
                        new_salary_items: formattedNewSalaryItems,
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
                    self.loading = false;
                }
            },
            addNewSalaryItem() {
                const self = this;
                self.newSalaryItems.push({
                    salaryComponentId: '',
                    amount: 0,
                });
            },
            removeNewSalaryItem(index) {
                const self = this;
                self.newSalaryItems.splice(index, 1);
            },
            removeSalaryItem(index) {
                const self = this;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    // cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        self.salaryItems.splice(index, 1);
                        // Swal.fire(
                        //     'Deleted!',
                        //     'Your file has been deleted.',
                        //     'success'
                        // )
                    }
                })
            },
            currencyFormat(number) {
                const numbered = Number(number);
                return new Intl.NumberFormat('De-de').format(numbered);
            },
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
@endsection