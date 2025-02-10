@extends('layouts.app')

@section('title', 'Gaji Harian Magenta')

@section('prehead')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
  <div class="card">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
      <!--begin::Card title-->
      <div class="card-title">
        <h3 class="text-gray-800">Data Gaji Harian</h3>
      </div>
      <!--begin::Card title-->
      <!--begin::Card toolbar-->
      <div class="card-toolbar">
        <div class="me-3">
          @if($paid)
          <span class="badge badge-success"><i class="bi bi-check-lg text-white"></i> Dibayar</span>
          @else
          <button class="btn btn-primary" @click="makePayment"><i class="bi bi-cash-coin"></i> Pembayaran</button>
          @endif
        </div>
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
      <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body pt-0">
      <!--begin::Table-->
      <!-- <h3 class="my-10 bg-light-primary p-5 rounded text-primary fs-6"><span class="text-gray-600">Periode</span> {{ \Carbon\Carbon::parse($start_date)->isoFormat('ll') }} - {{ \Carbon\Carbon::parse($end_date)->isoFormat('ll') }}</h3> -->
      <div class="my-6">
        <div class="separator separator-dashed"></div>
        <p class="text-center m-0 text-gray-700 py-4 fs-3">Periode {{ \Carbon\Carbon::parse($start_date)->isoFormat('ll') }} - {{ \Carbon\Carbon::parse($end_date)->isoFormat('ll') }}</p>
        <div class="separator separator-dashed"></div>
      </div>
      <!-- <div class="collapse" :class="model.checkedSalariesIds.length > 0 ? 'show' : ''" id="bulkActionCollapse">
                <div class="card card-bordered bg-light mb-5">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="ps-5">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" style="opacity: 1;" type="checkbox" checked disabled />
                                    <label class="form-check-label text-dark fs-4" style="opacity: 1;">
                                        @{{ model.checkedSalariesIds.length }} Data terpilih
                                    </label>
                                </div>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-danger ms-2" @click="openBulkDeleteConfirmation">
                                    <i class="bi bi-trash align-middle"></i>
                                    Hapus
                                </button>
                                <button class="btn btn-success">
                                    <i class="bi bi-printer align-middle"></i>
                                    Cetak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
      <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
          <p class="fs-3 mb-0">Total Take Home Pay</p>
          <p class="fs-2x fw-bolder mb-0">Rp {{ number_format($total_thp, 0, ',', '.') }}</p>
        </div>
        <div class="text-end">
          <button class="btn btn-danger ms-2" @click="openBulkDeleteConfirmation">
            <i class="bi bi-trash align-middle"></i>
            Hapus Semua Gaji
          </button>
        </div>
      </div>
      <!-- <div>
                <div class="card card-bordered bg-light mb-5">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="ps-5">
                            </div>
                            <div class="text-end">
                                <button class="btn btn-danger ms-2" @click="openBulkDeleteConfirmation">
                                    <i class="bi bi-trash align-middle"></i>
                                    Hapus Semua
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
      <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
        <!--begin::Table head-->
        <thead>
          <!--begin::Table row-->
          <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
            <!-- <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_customers_table .form-check-input" value="1" />
                            </div>
                        </th> -->
            <!-- <th>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" @change="checkAll($event)" id="checkAll" />
                                <label class="form-check-label" for="checkAll"></label>
                            </div>
                        </th> -->
            <!-- <th>NO</th> -->
            <th class="min-w-125px">Nama</th>
            <th>Job Title</th>
            <th class="text-end">Pendapatan (Rp)</th>
            <th class="text-end">Potongan (Rp)</th>
            <th class="text-end">Take Home Pay (Rp)</th>
            <!-- <th class="text-center">Status Pembayaran</th> -->
            <!-- <th>Tanggal Dibuat</th> -->
            <th class="text-end min-w-70px">Actions</th>
          </tr>
          <!--end::Table row-->
        </thead>
        <!--end::Table head-->
        <!--begin::Table body-->
        <tbody class="fw-bold text-gray-600">
          @foreach($salaries as $salary)
          <tr>
            <!--begin::Checkbox-->
            <!-- <td>
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" />
                            </div>
                        </td> -->
            <!--end::Checkbox-->
            <!--begin::Name=-->
            <!-- <td>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" v-model="model.checkedSalariesIds" value="{{ $salary->id }}" id="flexCheckDefault{{ $salary->id }}" />
                                <label class="form-check-label" for="flexCheckDefault{{ $salary->id }}"></label>
                            </div>
                        </td> -->
            <!-- <td>{{ $loop->iteration }}</td> -->
            <td>
              <span class="text-gray-800">
                @if(isset($salary->employee))
                <div>
                  <a href="#" data-bs-toggle="modal" data-bs-target="#detail_modal" @click="openDetailModal(salary?.employee?.id)" class="text-gray-800 text-hover-primary fs-5 fw-bolder mb-1">{{ $salary?->employee?->name }}</a>
                </div>
                <span class="text-muted">{{ $salary?->employee?->number }}</span>
                @endif
              </span>
            </td>
            <!--end::Name=-->
            <!--begin::Email=-->
            <td>
              @if(isset($salary->employee->activeCareer->jobTitle->name))
              <span>{{ $salary?->employee?->activeCareer?->jobTitle?->name }}</span>
              @endif
            </td>
            <!--end::Email=-->
            <td class="text-end text-gray-800">
              {{ number_format($salary?->total_incomes, 0, ',', '.') }}
            </td>
            <td class="text-end text-gray-800">
              {{ number_format($salary?->total_deductions, 0, ',', '.') }}
            </td>
            <td class="text-end text-gray-800">
              {{ number_format($salary?->take_home_pay, 0, ',', '.') }}
            </td>
            <!-- <td class="text-center text-gray-800">
                            @if($salary->paid)
                            <span class="badge badge-success">Dibayar</span>
                            @endif
                        </td> -->
            <!--begin::Company=-->
            <!-- <td>
                            <span>{{ \Carbon\Carbon::parse($salary->created_at)->isoFormat('LL') }}</span>
                        </td> -->
            <!--end::Company=-->
            <!--begin::Action=-->
            <td class="text-end">
              <div class="d-flex justify-content-end">
                <!--begin::Share link-->
                <a href="/daily-salaries/{{ $salary?->id }}/print" target="_blank" class="btn btn-sm btn-light-success me-2" title="Cetak">
                  <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                  <!-- <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22ZM12.5 18C12.5 17.4 12.6 17.5 12 17.5H8.5C7.9 17.5 8 17.4 8 18C8 18.6 7.9 18.5 8.5 18.5L12 18C12.6 18 12.5 18.6 12.5 18ZM16.5 13C16.5 12.4 16.6 12.5 16 12.5H8.5C7.9 12.5 8 12.4 8 13C8 13.6 7.9 13.5 8.5 13.5H15.5C16.1 13.5 16.5 13.6 16.5 13ZM12.5 8C12.5 7.4 12.6 7.5 12 7.5H8C7.4 7.5 7.5 7.4 7.5 8C7.5 8.6 7.4 8.5 8 8.5H12C12.6 8.5 12.5 8.6 12.5 8Z" fill="black" />
                                            <rect x="7" y="17" width="6" height="2" rx="1" fill="black" />
                                            <rect x="7" y="12" width="10" height="2" rx="1" fill="black" />
                                            <rect x="7" y="7" width="6" height="2" rx="1" fill="black" />
                                            <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="black" />
                                        </svg>
                                    </span> -->
                  <!--end::Svg Icon-->
                  <i class="bi bi-printer align-middle"></i>
                  <!-- Cetak -->
                </a>
                <!-- <a href="/daily-salaries/{{ $salary->id }}/edit" target="_blank" class="btn btn-sm btn-light-primary" title="Cetak">
                                    <i class="bi bi-pencil align-middle"></i>
                                </a> -->
                <!-- <a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Lainnya
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                        </svg>
                                    </span>
                                </a>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#detail_modal" @click="openDetailModal({{ $salary->id }})">Detail</a>
                                    </div>
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 text-danger" @click.prevent="openSingleDeleteConfirmation({{ $salary->id }})">Hapus</a>
                                    </div>
                                </div> -->
                <!--end::Menu-->
              </div>
            </td>
            <!--end::Action=-->
          </tr>
          @endforeach
        </tbody>
        <!--end::Table body-->
      </table>
      <!--end::Table-->
    </div>
    <!--end::Card body-->
  </div>
  <!--end::Card-->
  <!--begin::Modals-->
  <div class="modal fade" tabindex="-1" id="detail_modal">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content shadow-none">
        <div class="modal-header">
          <h5 class="modal-title">Detail Gaji</h5>

          <!--begin::Close-->
          <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <span class="svg-icon svg-icon-2x">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
              </svg>
            </span>
          </div>
          <!--end::Close-->
        </div>

        <div class="modal-body">
          <div v-if="selectedDetailSalary">
            <div class="d-flex flex-center flex-column">
              <!--begin::Name-->
              <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bolder mb-0">@{{ selectedDetailSalary?.employee?.name }}</a>
              <!--end::Name-->
              <!--begin::Position-->
              <div class="fw-bold text-gray-400 mb-6">@{{ selectedDetailSalary?.employee?.active_career?.job_title?.name }}</div>
              <!--end::Position-->
            </div>
            <div class="table-responsive" style="max-height: 400px;">
              <table class="table table-striped">
                <thead>
                  <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Kalender</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Masuk</th>
                    <th class="text-center">Pulang</th>
                    <th class="text-center">Lembur</th>
                    <th class="text-center">Keterlambatan</th>
                    <th class="text-end">Uang Harian</th>
                    <th class="text-end">Uang Lembur</th>
                    <th class="text-end">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(income, index) in selectedDetailSalary.incomes" class="fw-bold fs-6 text-gray-700">
                    <td class="text-gray-600 ps-2">@{{ income.date }}</td>
                    <td class="text-gray-700">
                      <span v-if="income.attendance.working_pattern_day == 'holiday' || income?.event_calendars?.length" class="text-danger">@{{ income.day_name }}</span>
                      <span v-else>@{{ income.day_name }}</span>
                    </td>
                    <td>
                      <div v-for="event in income?.event_calendars" class="mb-3">
                        <span class="badge badge-light-danger">@{{ event.name }}</span>
                      </div>
                    </td>
                    <td class="text-center text-uppercase">
                      <span class="text-uppercase" :class="badgeColor(income.attendance.status)">@{{ income.attendance.status }}</span>
                    </td>
                    <td class="text-center text-gray-600">@{{ income.attendance.clock_in_time }}</td>
                    <td class="text-center text-gray-600">@{{ income.attendance.clock_in_time }}</td>
                    <td class="text-center text-gray-600">@{{ income.attendance.overtime }}</td>
                    <td class="text-center text-gray-600">@{{ income.attendance.time_late }}</td>
                    <td class="text-end text-gray-700">@{{ currencyFormat(income.daily_wage) }}</td>
                    <td class="text-end text-gray-700">@{{ currencyFormat(income.overtime_pay) }}</td>
                    <td class="text-end text-gray-700 pe-2">@{{ currencyFormat(income.total) }}</td>
                  </tr>
                </tbody>
                <!-- <tfoot>
                                    <tr>
                                        <td colspan="9"></td>
                                        <td class="text-end fw-bold">@{{ currencyFormat(selectedDetailSalary?.total_incomes) }}</td>
                                    </tr>
                                </tfoot> -->
              </table>
            </div>
            <div class="text-end py-3">
              <span class="fw-bold fs-3">Rp @{{ currencyFormat(selectedDetailSalary?.total_incomes) }}</span>
            </div>
            <div class="separator separator-dashed mb-5"></div>
            <div v-if="selectedDetailSalary?.deductions.length > 0">
              <h4>Potongan</h4>
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                      <th>Nama</th>
                      <th class="text-end">Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="deduction in selectedDetailSalary?.deductions" class="fw-bold fs-6 text-gray-700">
                      <td>@{{ deduction.name }}</td>
                      <td class="text-end">@{{ currencyFormat(deduction.value) }}</td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td></td>
                      <td class="text-end fw-bold fs-3">Rp @{{ currencyFormat(selectedDetailSalary?.total_deductions) }}</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="row justify-content-end mt-10">
              <div class="col-md-4">
                <div class="d-flex justify-content-between mb-5">
                  <h5 class="mb-0 text-gray-700">Pendapatan</h5>
                  <h5 class="mb-0 text-gray-800">Rp @{{ currencyFormat(selectedDetailSalary?.total_incomes) }}</h5>
                </div>
                <div class="d-flex justify-content-between">
                  <h5 class="mb-0 text-gray-700">Potongan</h5>
                  <h5 class="mb-0 text-gray-800">(Rp @{{ currencyFormat(selectedDetailSalary?.total_deductions) }})</h5>
                </div>

                <div class="separator separator-dashed my-3"></div>
                <div class="d-flex justify-content-between my-3 bg-light-success p-5 border-success border-bottom-dashed border-top-dashed">
                  <h3 class="mb-0 text-gray-800">TAKE HOME PAY</h3>
                  <h3 class="mb-0 text-gray-800">Rp @{{ currencyFormat(selectedDetailSalary?.take_home_pay) }}</h3>
                </div>
              </div>
            </div>
          </div>
          <div v-else>
            <h3 class="text-center text-gray-800">Pilih Pegawai</h3>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
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
  moment.locale('id');
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
      // order: [
      //     [1, 'ASC']
      // ],
      columnDefs: [{
          targets: 0,
          // searchable: false,
          orderable: false,
        },
        {
          targets: 4,
          searchable: false,
          orderable: false,
          className: "text-end",
        },
      ]
    });

    // const handleSearchDatatable = () => {
    const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
    filterSearch.addEventListener('keyup', function(e) {
      datatable.search(e.target.value).draw();
    });
    // }
  })
</script>
<script>
  const salaries = <?php echo Illuminate\Support\Js::from($salaries) ?>;
  const outletSpendingPayloads = <?php echo Illuminate\Support\Js::from($outlet_spending_payloads) ?>;

  Vue.prototype.moment = moment;

  const app = new Vue({
    el: '#kt_content_container',
    data() {
      return {
        model: {
          checkedAll: false,
          checkedSalariesIds: [],
        },
        salaries: salaries,
        selectedSalaryId: null,
        outletSpendingPayloads,
      }
    },
    computed: {
      selectedDetailSalary() {
        const {
          selectedSalaryId,
          salaries
        } = this;
        if (selectedSalaryId && salaries.length) {
          const [salary] = salaries.filter(salary => salary.id == selectedSalaryId);
          if (salary) {
            try {
              const parsedIncomes = JSON.parse(String.raw `${salary.incomes}`);
              const parsedDeductions = JSON.parse(String.raw `${salary.deductions}`);
              // salary.incomes = parsedIncomes;
              // salary.deductions = parsedDeductions;
              const newSalary = {
                ...salary,
                incomes: parsedIncomes,
                deductions: parsedDeductions,
              }
              return newSalary;
            } catch (error) {
              console.log(error);
              console.log(salary.incomes);
            }
          }
        }
        return null;
      },
      salariesIds() {
        return this.salaries.map(salary => salary.id.toString());
      },
    },
    methods: {
      checkAll(e) {
        const value = e.target.checked;
        // console.log(value)
        // console.log(e)
        if (value) {
          const ids = this.salaries.map(salary => salary.id.toString());
          // console.log(ids)
          this.model.checkedSalariesIds = ids;
        } else {
          this.model.checkedSalariesIds = [];
        }
      },
      currencyFormat(number) {
        return new Intl.NumberFormat('De-de').format(number);
      },
      openDetailModal(employeeId) {
        this.selectedSalaryId = employeeId;
      },
      badgeColor(status) {
        const prefix = 'badge badge-';
        switch (status) {
          case 'hadir':
            return prefix + 'success';
          case 'izin':
            return prefix + 'primary';
          case 'cuti':
            return prefix + 'info';
          case 'sakit':
            return prefix + 'warning';
          default:
            return '';
        }
      },
      openSingleDeleteConfirmation(id) {
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
            return self.sendSingleDeleteRequest(id);
          },
          allowOutsideClick: () => !Swal.isLoading(),
          backdrop: true,
        })
      },
      sendSingleDeleteRequest(id) {
        const self = this;
        return axios.delete('/daily-salaries/' + id + '/delete')
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
      openBulkDeleteConfirmation() {
        const self = this;
        Swal.fire({
          title: 'Apakah anda yakin?',
          text: (self?.salariesIds.length || '') + " Data akan dihapus dan pembayaran akan dibatalkan",
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
            return self.sendBulkDeleteRequest();
          },
          allowOutsideClick: () => !Swal.isLoading(),
          backdrop: true,
        })
      },
      sendBulkDeleteRequest() {
        const self = this;
        return axios.delete('/daily-salaries/action/bulk-delete' + '?ids=' + JSON.stringify(self?.salariesIds.map(id => Number(id))))
          .then(async function(response) {

            let paymentBatchCodes = response?.data?.data?.payment_batch_codes;

            const response2 = axios.delete(`{{ env("AERPLUS_URL") }}/api/v1/outlet-spendings/delete-from-payment-batch-code?payment_batch_code=${JSON.stringify(paymentBatchCodes)}`);

            if (response2) {
              let message = response2?.data?.message;
              if (!message) {
                message = 'Data berhasil dihapus'
              }
              toastr.success(response?.data?.message || 'Data berhasil dihapus');
            }

            setTimeout(() => {
              document.location.href = '/payrolls/daily-aerplus';
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
      makePayment() {
        const self = this;
        Swal.fire({
          title: `Pembayaran sebesar Rp {{ number_format($total_thp, 0, '.', '.') }} akan dilakukan`,
          // input: "date",
          // didOpen: () => {
          //   const today = (new Date()).toISOString();
          //   Swal.getInput().min = today.split("T")[0];
          // },
          // text: "Jumlah yang tercantum akan dicatat ke dalam jurnal pengeluaran setiap depot",
          html: `
            <p>Jumlah yang tercantum akan dicatat ke dalam jurnal pengeluaran setiap depot</p>
            <div class="mb-2 fw-bolder">Tanggal Jurnal</div>
            <div>
              <input type="date" class="form-control" id="payment_journal_date" value="{{ date('Y-m-d') }}">
            </div>
          `,
          icon: 'warning',
          reverseButtons: true,
          showCancelButton: true,
          confirmButtonText: 'Bayar',
          cancelButtonText: 'Batal',
          customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-light"
          },
          showLoaderOnConfirm: true,
          preConfirm: () => {
            const date = $('#payment_journal_date').val();
            return self.sendPayment(date);
          },
          allowOutsideClick: () => !Swal.isLoading(),
          backdrop: true,
        })
      },
      async sendPayment(date) {
        const self = this;
        try {
          // console.log(date);
          // console.log(document.getElementById('payment_journal_date'));
          // return;
          // const requests = [];

          // this.outletSpendingPayloads.forEach(payload => {
          //     const request = axios.post('http://aerplus-central-clone.test/api/v1/outlet-spendings', payload);
          //     requests.push(request);
          // });

          // const responses = await Promise.all(requests);

          // console.log(responses);
          const response = await axios.post('{{ env("AERPLUS_URL") }}/api/v1/outlet-spendings', {
            outlet_spending_payloads: self.outletSpendingPayloads,
            date: date,
          });

          // if (response) {
          //     let message = response?.data?.message;
          //     if (!message) {
          //         message = 'Data berhasil disimpan'
          //     }
          //     toastr.success(message);
          // }
          const paymentBatchCode = response?.data?.data?.payment_batch_code || null;

          const dailySalaryResponse = await axios.post('/daily-salaries/pay', {
            start_date: '{{ $start_date }}',
            end_date: '{{ $end_date }}',
            payment_batch_code: paymentBatchCode,
          })

          if (dailySalaryResponse) {
            let message = dailySalaryResponse?.data?.message;
            if (!message) {
              message = 'Pembayaran berhasil'
            }
            toastr.success(message);
            setTimeout(() => {
              document.location.reload();
            }, 500);
          }
        } catch (error) {
          console.error(error)
          // console.log(error.data);
          let message = error?.response?.data?.message;
          if (!message) {
            message = 'Something wrong...'
          }
          toastr.error(message);
        }
      }
    },
    watch: {
      'model.checkedSalariesIds': function(newValue) {
        const {
          salariesIds
        } = this;
        const diff = salariesIds.filter(id => !newValue.includes(id));

        if (diff.length > 0) {
          // this.model.checkedAll = false;
          document.getElementById('checkAll').checked = false;
        } else {
          document.getElementById('checkAll').checked = true;
        }
      },
    },
  })
</script>
@endsection