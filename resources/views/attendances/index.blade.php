@extends('layouts.app')

@section('title', 'Kehadiran')

@section('prehead')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
  <div class="card mb-5 mb-xl-10">
    <!--begin::Card header-->
    <div class="card-header">
      <!--begin::Card title-->
      <div class="card-title m-0">
        <h3 class="fw-bolder m-0">Ringkasan Absensi</h3>
      </div>
      <!--end::Card title-->
      <div class="card-toolbar">
        <div class="input-group">
          <input v-model="date" type="date" class="form-control" @change="applyFilter">
          <span class="input-group-text" id="basic-addon2">
            <!-- <i class="fas fa-calendar fs-4"></i> -->
            <span class="svg-icon svg-icon-muted"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                <path opacity="0.3" d="M19 3.40002C18.4 3.40002 18 3.80002 18 4.40002V8.40002H14V4.40002C14 3.80002 13.6 3.40002 13 3.40002C12.4 3.40002 12 3.80002 12 4.40002V8.40002H8V4.40002C8 3.80002 7.6 3.40002 7 3.40002C6.4 3.40002 6 3.80002 6 4.40002V8.40002H2V4.40002C2 3.80002 1.6 3.40002 1 3.40002C0.4 3.40002 0 3.80002 0 4.40002V19.4C0 20 0.4 20.4 1 20.4H19C19.6 20.4 20 20 20 19.4V4.40002C20 3.80002 19.6 3.40002 19 3.40002ZM18 10.4V13.4H14V10.4H18ZM12 10.4V13.4H8V10.4H12ZM12 15.4V18.4H8V15.4H12ZM6 10.4V13.4H2V10.4H6ZM2 15.4H6V18.4H2V15.4ZM14 18.4V15.4H18V18.4H14Z" fill="black" />
                <path d="M19 0.400024H1C0.4 0.400024 0 0.800024 0 1.40002V4.40002C0 5.00002 0.4 5.40002 1 5.40002H19C19.6 5.40002 20 5.00002 20 4.40002V1.40002C20 0.800024 19.6 0.400024 19 0.400024Z" fill="black" />
              </svg></span>
          </span>
        </div>

      </div>
    </div>
    <!--begin::Card header-->
    <!--begin::Card body-->
    <div class="card-body p-9">
      <!-- begin:Row -->
      <div class="row justify-content-between align-items-center">
        <!--begin::Col-->
        <div class="col-md-5">
          <div id="attendance_pie_chart"></div>
        </div>
        <!--end::Col-->
        <!--begin::Col-->
        <div class="col-md-6">
          <h3 class="card-title align-items-start flex-column mb-10">
            <div class="card-label fw-bolder text-dark">Statistik Absensi</div>
            <div class="text-gray-400 pt-2 fw-bold fs-6" v-cloak>Tanggal @{{ moment(date).format("Do MMMM YYYY") }}</div>
          </h3>
          <!--begin::Completion Item-->
          <div class="d-flex justify-content-between align-items-center border-bottom border-bottom mb-4 p-1">
            <div>
              <span class="fw-bolder fs-5 text-gray-700"><span class="bullet bullet-vertical bg-success me-5"></span> Hadir</span>
            </div>
            <div v-cloak>
              <span class="fw-bolder fs-5">@{{ statistics.hadir }}</span>
            </div>
          </div>
          <!--end::Completion Item-->
          <!--begin::Completion Item-->
          <div class="d-flex justify-content-between align-items-center border-bottom mb-4 p-1">
            <div>
              <span class="fw-bolder fs-5 text-gray-700"><span class="bullet bullet-vertical bg-warning me-5"></span> Sakit</span>
            </div>
            <div v-cloak>
              <span class="fw-bolder fs-5">@{{ statistics.sakit }}</span>
            </div>
          </div>
          <!--end::Completion Item-->
          <!--begin::Completion Item-->
          <div class="d-flex justify-content-between align-items-center border-bottom mb-4 p-1">
            <div>
              <span class="fw-bolder fs-5 text-gray-700"><span class="bullet bullet-vertical bg-primary me-5"></span> Izin</span>
            </div>
            <div v-cloak>
              <span class="fw-bolder fs-5">@{{ statistics.izin }}</span>
            </div>
          </div>
          <!--end::Completion Item-->
          <!--begin::Completion Item-->
          <div class="d-flex justify-content-between align-items-center border-bottom mb-4 p-1">
            <div>
              <span class="fw-bolder fs-5 text-gray-700"><span class="bullet bullet-vertical bg-info me-5"></span> Cuti</span>
            </div>
            <div v-cloak>
              <span class="fw-bolder fs-5">@{{ statistics.cuti }}</span>
            </div>
          </div>
          <!--end::Completion Item-->
          <!--begin::Completion Item-->
          <div class="d-flex justify-content-between align-items-center border-bottom mb-4 border-gray-300 p-1">
            <div>
              <span class="fw-bolder fs-5 text-gray-700"><span class="bullet bullet-vertical me-5"></span> Tanpa Keterangan</span>
            </div>
            <div v-cloak>
              <span class="fw-bolder fs-5">@{{ statistics.na }}</span>
            </div>
          </div>
          <!--end::Completion Item-->
        </div>
        <!--end::Col-->
      </div>
      <!-- end:Row -->
    </div>
    <!--end::Card body-->
  </div>
  <!--begin::Card-->
  <div class="card mb-5 mb-xl-10">
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
        @can('create', App\Models\Attendance::class)
        <!--begin::Toolbar-->
        <a href="/attendances/action/upload" class="btn btn-light-primary me-3">
          <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
          <span class="svg-icon svg-icon-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black" />
              <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black" />
              <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4" />
            </svg>
          </span>
          <!--end::Svg Icon-->Upload
        </a>
        @endcan
        <button class="btn btn-light-primary" data-bs-toggle="collapse" href="#collapseFilter" role="button" aria-expanded="false" aria-controls="collapseFilter">
          <span class="svg-icon svg-icon-2">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor" />
            </svg>
          </span>
          Filter
        </button>
        <!--<button class="btn btn-light-primary" id="btn-filter-2">-->
        <!--    <span class="svg-icon svg-icon-2">-->
        <!--        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">-->
        <!--            <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor" />-->
        <!--        </svg>-->
        <!--    </span>-->
        <!--    Filter 2-->
        <!--</button>-->
      </div>
      <!--end::Card toolbar-->

    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body pt-0">
      <div class="collapse justify-content-center" id="collapseFilter">
        <div class="row py-3 gy-3">
          <div class="col-md-3">
            <select v-model="model.filter.companyId" name="company_id" class="form-select form-select-sm">
              <option value="">Semua Perusahaan</option>
              @foreach($companies as $company)
              <option value="{{ $company->id }}">{{ $company->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <select v-model="model.filter.divisionId" name="division_id" class="form-select form-select-sm">
              <option value="">Semua Divisi</option>
              <option v-for="division in filteredDivisions" :value="division.id">@{{ division.name }}</option>
            </select>
          </div>
          <div class="col-md-3">
            <select v-model="model.filter.officeId" name="office_id" class="form-select form-select-sm">
              <option value="">Semua Kantor</option>
              <option v-for="office in filteredOffices" :value="office.id">@{{ office.name }}</option>
            </select>
          </div>
          <div class="col-md-3">
            <select v-model="model.filter.status" name="office_id" class="form-select form-select-sm">
              <option value="">Semua status</option>
              <option value="1">Aktif</option>
              <option value="0">Tidak Aktif</option>
            </select>
          </div>
          <div class="col-md-3">
            <button type="button" class="btn btn-secondary btn-sm w-100" @click="applyFilter">Filter</button>
          </div>
        </div>
      </div>
      <?php
      $queryDate = request()->query('date');
      $queryCompanyId = request()->query('company_id');
      $queryDivisionId = request()->query('division_id');
      $queryOfficeId = request()->query('office_id');
      $queryStatus = request()->query('status');
      ?>
      @if(isset($queryDate) || isset($queryCompanyId) || isset($queryDivisionId) || isset($queryOfficeId) || isset($queryStatus))
      <div class="mb-5">
        <div class="d-flex align-items-center">
          <div class="me-3">
            <span class="badge badge-secondary">{{ \Carbon\Carbon::parse($date)->isoFormat('LL') }}</span>
          </div>
          <div class="me-3">
            <span class="badge badge-secondary">{{ $filtered_company_name }}</span>
          </div>
          <div class="me-3">
            <span class="badge badge-secondary">{{ $filtered_division_name }}</span>
          </div>
          <div class="me-3">
            <span class="badge badge-secondary">{{ $filtered_office_name }}</span>
          </div>
          <div class="me-3">
            <span class="badge badge-secondary">{{ $filtered_status_name }}</span>
          </div>
          <div>
            <a href="/attendances" class="badge badge-danger text-white align-middle"><i class="bi bi-x text-white"></i> Clear</a>
          </div>
        </div>
      </div>
      @endif
      <div class="table-responsive">
        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-7 gy-5" id="attendance_table">
          <!--begin::Table head-->
          <thead class="bg-light-primary">
            <!--begin::Table row-->
            <tr class="text-center text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
              <th class="text-start min-w-150px ps-2">Pegawai</th>
              <th>Status</th>
              <th>Jam Masuk</th>
              <th>Jam Keluar</th>
              <th>Lembur (Menit)</th>
              <th>Keterlambatan (Menit)</th>
              <!-- <th>Foto</th> -->
              <th class="text-end min-w-70px pe-2">Action</th>
            </tr>
            <!--end::Table row-->
          </thead>
          <!--end::Table head-->
          <!--begin::Table body-->
          <tbody class="fw-bold text-gray-600">
            <tr v-for="(employee, index) in employees" class="text-center">
              <!--begin::Name=-->
              <td class="text-start ps-2" style="max-width: 200px;">
                <div v-cloak>
                  <div>
                    <a :href="'/employees/' + employee.id + '/attendances'" class="text-gray-800 text-hover-primary">@{{ employee.name }}</a>
                  </div>
                  <span class="text-muted d-block fs-7">@{{ employee?.office?.division?.company?.initial || 'NA' }}-@{{ employee?.office?.division?.initial || 'NA' }}-@{{ employee?.number }} | @{{ employee?.office?.division?.company?.name ?? 'PERUSAHAAN' }} - @{{ employee?.office?.division?.name ?? 'DIVISI' }} - @{{ employee?.office?.name ?? 'KANTOR' }}</span>
                  <span class="text-muted d-block fs-7">@{{ employee?.active_career?.job_title?.name || '' }} - @{{ employee?.active_career?.job_title?.designation?.name || '' }}</span>
                </div>
              </td>
              <!--end::Name=-->
              <!--begin::Email=-->
              <template v-if="employee.attendances.length > 0">
                <td>
                  <span class="badge text-uppercase" :class="badgeColor(employee.attendances[0].status)">
                    <span>@{{ employee.attendances[0].status || '' }}</span>
                    <span v-cloak v-if="employee.attendances[0]?.leave_application?.category">(@{{ employee.attendances[0]?.leave_application?.category?.name || '' }})</span>
                  </span>
                </td>
                <!--end::Email=-->
                <!--begin::Company=-->
                <td class="text-center">
                  <div class="mb-2 fw-bolder text-gray-800">@{{ employee.attendances[0].clock_in_time || '' }}</div>
                  <div class="d-flex align-items-center justify-content-center mb-2">
                    <a v-if="employee.attendances[0].clock_in_latitude && employee.attendances[0].clock_in_longitude" :href="`http://www.google.com/maps/place/${employee.attendances[0].clock_in_latitude},${employee.attendances[0].clock_in_longitude}`" target="_blank" class="btn btn-sm btn-icon btn-light-warning ms-2">
                      <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                      <i class="bi bi-geo-alt-fill"></i>
                      <!--end::Svg Icon-->
                    </a>
                    <a v-if="employee.attendances[0].clock_in_attachment" :href="employee.attendances[0].clock_in_attachment" target="_blank" class="btn btn-sm btn-icon btn-light-warning ms-2">
                      <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                      <i class="bi bi-card-image"></i>
                      <!--end::Svg Icon-->
                    </a>
                    <button v-if="employee.attendances[0].clock_in_note" data-bs-toggle="tooltip" data-bs-placement="top" :data-bs-title="employee.attendances[0].clock_in_note" target="_blank" class="btn btn-sm btn-icon btn-light-warning ms-2">
                      <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                      <i class="bi bi-sticky-fill"></i>
                      <!--end::Svg Icon-->
                    </button>
                  </div>
                  <div v-cloak v-if="employee.attendances[0].clock_in_latitude != null">
                    <div v-cloak v-if="employee.attendances[0].clock_in_is_inside_office_radius == 1">
                      <span class="badge badge-light-success">
                        <i class="bi bi-check-circle text-success fs-8"></i>
                        <span>Di dalam Area</span>
                      </span>
                    </div>
                    <div v-cloak v-else>
                      <span class="badge badge-light-danger">
                        <i class="bi bi-x-circle text-danger fs-8"></i>
                        <span>Di luar Area</span>
                      </span>
                    </div>
                  </div>

                </td>
                <!--end::Company=-->
                <!--begin::Payment method=-->
                <td class="text-center">
                  <div class="mb-2 fw-bolder text-gray-800">@{{ employee.attendances[0].clock_out_time || '' }}</div>
                  <div class="d-flex align-items-center justify-content-center mb-2">
                    <a v-if="employee.attendances[0].clock_out_latitude && employee.attendances[0].clock_out_longitude" :href="`http://www.google.com/maps/place/${employee.attendances[0].clock_out_latitude},${employee.attendances[0].clock_out_longitude}`" target="_blank" class="btn btn-sm btn-icon btn-light-primary ms-2">
                      <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                      <i class="bi bi-geo-alt-fill"></i>
                      <!--end::Svg Icon-->
                    </a>
                    <a v-if="employee.attendances[0].clock_out_attachment" :href="employee.attendances[0].clock_out_attachment" target="_blank" class="btn btn-sm btn-icon btn-light-primary ms-2">
                      <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                      <i class="bi bi-card-image"></i>
                      <!--end::Svg Icon-->
                    </a>
                    <button v-if="employee.attendances[0].clock_out_note" data-bs-toggle="tooltip" data-bs-placement="top" :data-bs-title="employee.attendances[0].clock_out_note" target="_blank" class="btn btn-sm btn-icon btn-light-primary ms-2">
                      <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                      <i class="bi bi-sticky-fill"></i>
                      <!--end::Svg Icon-->
                    </button>
                  </div>
                  <div v-cloak v-if="employee.attendances[0].clock_out_latitude != null">
                    <div v-cloak v-if="employee.attendances[0].clock_out_is_inside_office_radius == 1">
                      <span class="badge badge-light-success">
                        <i class="bi bi-check-circle text-success fs-8"></i>
                        <span>Di dalam Area</span>
                      </span>
                    </div>
                    <div v-cloak v-else>
                      <span class="badge badge-light-danger">
                        <i class="bi bi-x-circle text-danger fs-8"></i>
                        <span>Di luar Area</span>
                      </span>
                    </div>
                  </div>
                </td>
                <!--end::Payment method=-->
                <!--begin::Date=-->
                <td class="text-success">@{{ employee.attendances[0].overtime || 0 }}</td>
                <!--end::Date=-->
                <!--begin::Date=-->
                <td class="text-danger">
                  <span v-if="employee.attendances[0].time_late > 0">@{{ employee.attendances[0].time_late || 0 }}</span>
                  <span v-else>0</span>
                </td>
                <!--end::Date=-->
                <!-- <td>
                                    <div class="d-flex">
                                        <a v-if="employee.attendances[0].clock_in_attachment" :href="employee.attendances[0].clock_in_attachment" target="_blank" class="d-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-image" viewBox="0 0 16 16">
                                                <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                                                <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54A.505.505 0 0 1 1 12.5v-9a.5.5 0 0 1 .5-.5h13z" />
                                            </svg>
                                            <span class="d-block">Clock In</span>
                                        </a>
                                        <a v-if="employee.attendances[0].clock_out_attachment" :href="employee.attendances[0].clock_out_attachment" target="_blank" class="d-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-image" viewBox="0 0 16 16">
                                                <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                                                <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54A.505.505 0 0 1 1 12.5v-9a.5.5 0 0 1 .5-.5h13z" />
                                            </svg>
                                            <span class="d-block">Clock Out</span>
                                        </a>
                                    </div>
                                </td> -->
                <!--begin::Action=-->
                <td class="text-end pe-2">
                  @can('update', App\Models\Attendance::class)
                  <button type="button" class="btn btn-sm btn-icon btn-light-info ms-2 btn-edit" data-bs-toggle="modal" data-bs-target="#modalEditAttendance" @click="onOpenEditModal(employee.attendances[0].id, employee.id)">
                    <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                    <span class="svg-icon svg-icon-5 m-0">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                      </svg>
                    </span>
                    <!--end::Svg Icon-->
                  </button>
                  @endif
                  @can('delete', App\Models\Attendance::class)
                  <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2" @click="openDeleteConfirmation(employee.attendances[0].id)">
                    <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                    <span class="svg-icon svg-icon-5 m-0">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor" />
                        <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor" />
                        <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor" />
                      </svg>
                    </span>
                    <!--end::Svg Icon-->
                  </button>
                  @endcan
                  <a :href="`/activities/detail?date=${employee.attendances[0].date}&employee_id=${employee.id}`" target="_blank" class="btn btn-sm btn-icon btn-light-primary ms-2">
                    <span class="svg-icon svg-icon-5 m-0">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.39961 20.5073C7.29961 20.5073 6.39961 19.6073 6.39961 18.5073C6.39961 17.4073 7.29961 16.5073 8.39961 16.5073H9.89961C11.7996 16.5073 13.3996 14.9073 13.3996 13.0073C13.3996 11.1073 11.7996 9.50732 9.89961 9.50732H8.09961L6.59961 11.2073C6.49961 11.3073 6.29961 11.4073 6.09961 11.5073C6.19961 11.5073 6.19961 11.5073 6.29961 11.5073H9.79961C10.5996 11.5073 11.2996 12.2073 11.2996 13.0073C11.2996 13.8073 10.5996 14.5073 9.79961 14.5073H8.39961C6.19961 14.5073 4.39961 16.3073 4.39961 18.5073C4.39961 20.7073 6.19961 22.5073 8.39961 22.5073H15.3996V20.5073H8.39961Z" fill="currentColor" />
                        <path opacity="0.3" d="M8.89961 8.7073L6.69961 11.2073C6.29961 11.6073 5.59961 11.6073 5.19961 11.2073L2.99961 8.7073C2.19961 7.8073 1.7996 6.50732 2.0996 5.10732C2.3996 3.60732 3.5996 2.40732 5.0996 2.10732C7.6996 1.50732 9.99961 3.50734 9.99961 6.00734C9.89961 7.00734 9.49961 8.0073 8.89961 8.7073Z" fill="currentColor" />
                        <path d="M5.89961 7.50732C6.72804 7.50732 7.39961 6.83575 7.39961 6.00732C7.39961 5.1789 6.72804 4.50732 5.89961 4.50732C5.07119 4.50732 4.39961 5.1789 4.39961 6.00732C4.39961 6.83575 5.07119 7.50732 5.89961 7.50732Z" fill="currentColor" />
                        <path opacity="0.3" d="M17.3996 22.5073H15.3996V13.5073C15.3996 12.9073 15.7996 12.5073 16.3996 12.5073C16.9996 12.5073 17.3996 12.9073 17.3996 13.5073V22.5073Z" fill="currentColor" />
                        <path d="M21.3996 18.5073H15.3996V13.5073H21.3996C22.1996 13.5073 22.5996 14.4073 22.0996 15.0073L21.2996 16.0073L22.0996 17.0073C22.6996 17.6073 22.1996 18.5073 21.3996 18.5073Z" fill="currentColor" />
                      </svg>
                    </span>
                    <!--end::Svg Icon-->
                  </a>
                </td>
                <!--end::Action=-->
              </template>
              <template v-else>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <!-- <td></td> -->
                <td class="text-end pe-2">
                  @can('create', App\Models\Attendance::class)
                  <button type="button" class="btn btn-sm btn-icon btn-light-info ms-2 btn-edit" data-bs-toggle="modal" data-bs-target="#modalAddAttendance" @click="onOpenAddModal(employee.id)">
                    <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                    <span class="svg-icon svg-icon-5 m-0">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                      </svg>
                    </span>
                    <!--end::Svg Icon-->
                  </button>
                  @endcan
                </td>
              </template>
            </tr>
          </tbody>
          <!--end::Table body-->
        </table>
        <!--end::Table-->
      </div>
    </div>
    <!--end::Card body-->
  </div>
  <!--end::Card-->
  <!-- begin::modal -->
  <div class="modal fade" tabindex="-1" id="modalEditAttendance">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ubah Kehadiran</h5>
          <!--begin::Close-->
          <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <span class="svg-icon svg-icon-2x">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
              </svg>
            </span>
          </div>
          <!--end::Close-->
        </div>

        <div class="modal-body py-10 px-lg-17">
          <!--begin::Scroll-->
          <div class="scroll-y me-n7 pe-7" id="kt_modal_add_division_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_division_header" data-kt-scroll-wrappers="#kt_modal_add_division_scroll" data-kt-scroll-offset="300px">
            <div v-if="model.edit.employee !== null" class="mb-5">
              <h3>@{{ model?.edit?.employee?.name }}</h3>
              <span class="text-muted fs-5">@{{ model?.edit?.employee?.number }}</span>
            </div>
            <!-- <div class="fv-row mb-7">
                            <label class="required fs-6 fw-bold mb-2">Status</label>
                            <select v-model="model.edit.status" class="form-select">
                                <option value="">Pilih Status</option>
                                <option value="hadir">Hadir</option>
                            </select>
                        </div> -->
            <!--end::Input group-->
            <!--begin::Input group-->
            <!-- <div class="fv-row mb-7">
                            <label class="required fs-6 fw-bold mb-2">Pola Kerja</label>
                            <select v-model="model.edit.workingPatternId" class="form-select">
                                <option value="">Pilih Pola Kerja</option>
                                @foreach($working_patterns as $working_pattern)
                                <option value="{{ $working_pattern->id }}">
                                    {{ $working_pattern->name }}
                                    @if(count($working_pattern->items) > 0)
                                    ({{ $working_pattern->items[0]->clock_in }}
                                    &nbsp;-&nbsp;
                                    {{ $working_pattern->items[0]->clock_out }})
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div> -->
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="d-flex flex-column flex-md-row gap-5 mb-7">
              <div class="fv-row flex-row-fluid fv-plugins-icon-container">
                <!--begin::Label-->
                <label class="form-label">Jam Masuk</label>
                <!--end::Label-->
                <!--begin::Input-->
                <input type="time" v-model="model.edit.clockInAt" class="form-control" value="">
                <!--end::Input-->
              </div>
              <div class="fv-row flex-row-fluid">
                <!--begin::Label-->
                <label class="form-label">Jam Keluar</label>
                <!--end::Label-->
                <!--begin::Input-->
                <input type="time" v-model="model.edit.clockOutAt" class="form-control">
                <!--end::Input-->
              </div>
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row mb-7">
              <div class="fv-row col-md-6 fv-plugins-icon-container">
                <!--begin::Label-->
                <label class="form-label">Jumlah Lembur (Menit)</label>
                <!--end::Label-->
                <!--begin::Input-->
                <input type="number" v-model="model.edit.overtime" class="form-control" min="0" placeholder="Masukkan jumlah lembur">
                <!--end::Input-->
              </div>
              <div class="fv-row col-md-6">
                <!--begin::Label-->
                <label class="form-label">Jumlah Keterlambatan (Menit)</label>
                <!--end::Label-->
                <!--begin::Input-->
                <input type="number" v-model="model.edit.timeLate" class="form-control" min="0" placeholder="Masukkan jumlah keterlambatan">
                <!--end::Input-->
              </div>
            </div>
            <!--end::Input group-->
          </div>
          <!--end::Scroll-->
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
          <button type="button" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-primary" :disabled="editButtonDisabled" @click="updateAttendance">
            <span class="indicator-label">Simpan</span>
            <span class="indicator-progress">Mengirim data...
              <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- end::modal -->
  <!-- begin::modal -->
  <div class="modal fade" tabindex="-1" id="modalAddAttendance">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ubah Kehadiran</h5>
          <!--begin::Close-->
          <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <span class="svg-icon svg-icon-2x">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
              </svg>
            </span>
          </div>
          <!--end::Close-->
        </div>

        <div class="modal-body py-10 px-lg-17">
          <!--begin::Scroll-->
          <div class="scroll-y me-n7 pe-7" id="kt_modal_add_division_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_division_header" data-kt-scroll-wrappers="#kt_modal_add_division_scroll" data-kt-scroll-offset="300px">
            <div v-if="model.add.employee !== null" class="mb-5">
              <h3>@{{ model?.add?.employee?.name }}</h3>
              <span class="text-muted fs-5">@{{ model?.add?.employee?.number }}</span>
            </div>
            <!--begin::Input group-->
            <!-- <div class="fv-row mb-7">
                            <label class="required fs-6 fw-bold mb-2">Status</label>
                            <select v-model="model.add.status" class="form-select">
                                <option value="">Pilih Status</option>
                                <option value="hadir">Hadir</option>
                            </select>
                        </div> -->
            <!--end::Input group-->
            <!--begin::Input group-->
            <!-- <div class="fv-row mb-7">
                            <label class="required fs-6 fw-bold mb-2">Pola Kerja</label>
                            <select v-model="model.add.workingPatternId" class="form-select">
                                <option value="">Pilih Pola Kerja</option>
                                @foreach($working_patterns as $working_pattern)
                                <option value="{{ $working_pattern->id }}">
                                    {{ $working_pattern->name }}
                                    @if(count($working_pattern->items) > 0)
                                    ({{ $working_pattern->items[0]->clock_in }}
                                    &nbsp;-&nbsp;
                                    {{ $working_pattern->items[0]->clock_out }})
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div> -->
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="d-flex flex-column flex-md-row gap-5 mb-7">
              <div class="fv-row flex-row-fluid fv-plugins-icon-container">
                <!--begin::Label-->
                <label class="form-label">Jam Masuk</label>
                <!--end::Label-->
                <!--begin::Input-->
                <input type="time" v-model="model.add.clockInAt" class="form-control" value="">
                <!--end::Input-->
              </div>
              <div class="fv-row flex-row-fluid">
                <!--begin::Label-->
                <label class="form-label">Jam Keluar</label>
                <!--end::Label-->
                <!--begin::Input-->
                <input type="time" v-model="model.add.clockOutAt" class="form-control">
                <!--end::Input-->
              </div>
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row mb-7">
              <div class="fv-row col-md-6 fv-plugins-icon-container">
                <!--begin::Label-->
                <label class="form-label">Jumlah Lembur (Menit)</label>
                <!--end::Label-->
                <!--begin::Input-->
                <input type="number" v-model="model.add.overtime" class="form-control" min="0" placeholder="Masukkan jumlah lembur">
                <!--end::Input-->
              </div>
              <div class="fv-row col-md-6">
                <!--begin::Label-->
                <label class="form-label">Jumlah Keterlambatan (Menit)</label>
                <!--end::Label-->
                <!--begin::Input-->
                <input type="number" v-model="model.add.timeLate" class="form-control" min="0" placeholder="Masukkan jumlah keterlambatan">
                <!--end::Input-->
              </div>
            </div>
            <!--end::Input group-->
          </div>
          <!--end::Scroll-->
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
          <button type="button" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-primary" :disabled="addButtonDisabled" @click="storeAttendance">
            <span class="indicator-label">Simpan</span>
            <span class="indicator-progress">Mengirim data...
              <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- end::modal -->
  <div id="kt_drawer_example_dismiss" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true" data-kt-drawer-toggle="#btn-filter-2" data-kt-drawer-close="#kt_drawer_example_basic_close" data-kt-drawer-width="500px">

    <!--begin::Card-->
    <div class="card rounded-0 w-100">
      <!--begin::Card header-->
      <div class="card-header pe-5">
        <!--begin::Title-->
        <div class="card-title">
          <!--begin::User-->
          <div class="d-flex justify-content-center flex-column me-3">
            <span class="fs-4 fw-bold text-gray-900 text-hover-primary me-1 lh-1">Filter</span>
          </div>
          <!--end::User-->
        </div>
        <!--end::Title-->

        <!--begin::Card toolbar-->
        <div class="card-toolbar">
          <!--begin::Close-->
          <div class="btn btn-sm btn-icon btn-active-light-primary" id="kt_drawer_example_dismiss_close">
            <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
          </div>
          <!--end::Close-->
        </div>
        <!--end::Card toolbar-->
      </div>
      <!--end::Card header-->

      <!--begin::Card body-->
      <div class="card-body hover-scroll-overlay-y">
        <div>
          <select v-model="model.filter.companyId" name="company_id" class="form-select form-select-sm">
            <option value="">Semua Perusahaan</option>
            @foreach($companies as $company)
            <option value="{{ $company->id }}">{{ $company->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <select v-model="model.filter.divisionId" name="division_id" class="form-select form-select-sm">
            <option value="">Semua Divisi</option>
            <option v-for="division in filteredDivisions" :value="division.id">@{{ division.name }}</option>
          </select>
        </div>
        <div>
          <select v-model="model.filter.officeId" name="office_id" class="form-select form-select-sm">
            <option value="">Semua Kantor</option>
            <option v-for="office in filteredOffices" :value="office.id">@{{ office.name }}</option>
          </select>
        </div>
        <div>
          <select v-model="model.filter.status" name="office_id" class="form-select form-select-sm">
            <option value="">Semua status</option>
            <option value="1">Aktif</option>
            <option value="0">Tidak Aktif</option>
          </select>
        </div>
        <div class="col-md-3">
          <button type="button" class="btn btn-secondary btn-sm w-100" @click="applyFilter">Filter</button>
        </div>
      </div>
      <!--end::Card body-->

      <!--begin::Card footer-->
      <div class="card-footer">
        <!--begin::Dismiss button-->
        <button class="btn btn-light-danger" data-kt-drawer-dismiss="true">Dismiss drawer</button>
        <!--end::Dismiss button-->
      </div>
      <!--end::Card footer-->
    </div>
    <!--end::Card-->
  </div>
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


    datatable = $('#attendance_table').DataTable({});

    // const handleSearchDatatable = () => {
    const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
    filterSearch.addEventListener('keyup', function(e) {
      datatable.search(e.target.value).draw();
    });
    // }

    const deleteTableRow = (el) => {
      const row = $(el).parents('tr');
      datatable
        .row(row)
        .remove()
        .draw();
    }

    $('#kt_customers_table').on('click', '.btn-delete', function() {
      const id = $(this).attr('data-id');
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
          return axios.delete('/companies/' + id)
            .then(function(response) {
              // console.log(response.data);
              let message = response?.data?.message;
              if (!message) {
                message = 'Data berhasil disimpan'
              }
              toastr.success(message);
              deleteTableRow(self);
            })
            .catch(function(error) {
              console.log(error)
              // console.log(error.data);
              let message = error?.response?.data?.message;
              if (!message) {
                message = 'Something wrong...'
              }
              toastr.error(message);
              // Swal.fire({
              //     icon: 'error',
              //     title: 'Oops',
              //     text: 'Something wrong',
              // })
            });
        },
        allowOutsideClick: () => !Swal.isLoading(),
        backdrop: true,
      })
    })
    $('#kt_customers_table').on('click', '.btn-edit', function() {
      alert('btn edit clocked');
    })

  })
</script>
<script>
  const closeModal = (el) => {
    if (el) {
      const modalElement = document.querySelector(el);
      const modal = bootstrap.Modal.getInstance(modalElement);
      modal.hide();
    }
  }

  const textFormatter = (text) => `<span class="text-gray-800">${text}</span>`;

  const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;
  const statistics = <?php echo Illuminate\Support\Js::from($statistics) ?>;
  const divisions = <?php echo Illuminate\Support\Js::from($divisions) ?>;
  const offices = <?php echo Illuminate\Support\Js::from($offices) ?>;

  Vue.prototype.moment = moment;

  const app = new Vue({
    el: '#kt_content_container',
    data() {
      return {
        employees,
        statistics,
        divisions,
        offices,
        date: '{{ $date }}',
        model: {
          add: {
            employee: null,
            employeeId: '',
            workingPatternId: '',
            status: 'hadir',
            clockInAt: '',
            clockOutAt: '',
            overtime: 0,
            timeLate: 0,
          },
          edit: {
            id: null,
            employee: null,
            employeeId: '',
            workingPatternId: '',
            status: '',
            clockInAt: '',
            clockOutAt: '',
            overtime: 0,
            timeLate: 0,
          },
          filter: {
            companyId: '{{ $filter["company_id"] }}',
            divisionId: '{{ $filter["division_id"] }}',
            officeId: '{{ $filter["office_id"] }}',
            status: '{{ $filter["status"] }}',
          }
        },
        submitLoading: false,
      }
    },
    computed: {
      addButtonDisabled() {
        if (this.submitLoading) {
          return true;
        }

        return false;
      },
      editButtonDisabled() {
        if (this.submitLoading) {
          return true;
        }

        return false;
      },
      filteredDivisions() {
        if (this.model.filter.companyId) {
          return this.divisions.filter(division => division.company_id == this.model.filter.companyId);
        }

        this.model.filter.divisionId = "";

        return [];
      },
      filteredOffices() {
        if (this.model.filter.divisionId) {
          return this.offices.filter(office => office.division_id == this.model.filter.divisionId);
        }

        this.model.filter.officeId = "";

        return [];
      },
    },
    methods: {
      badgeColor(status) {
        switch (status) {
          case 'hadir':
            return 'badge-success';
          case 'sakit':
            return 'badge-warning';
          case 'izin':
            return 'badge-primary';
          case 'cuti':
            return 'badge-info';
          default:
            return 'badge-light';
        }
      },
      // onDateChange() {
      //     const {
      //         date
      //     } = this;
      //     if (date) {
      //         document.location.href = "/attendances?date=" + date;
      //     }
      // },
      async storeAttendance() {
        let self = this;
        try {
          const {
            employeeId,
            workingPatternId,
            status,
            clockInAt,
            clockOutAt,
            overtime,
            timeLate,
          } = self.model.add;

          const date = self.date;

          self.submitLoading = true;

          const response = await axios.post('/attendances', {
            employee_id: employeeId,
            date,
            working_pattern_id: workingPatternId,
            clock_in_at: `${date} ${clockInAt}`,
            clock_in_time: `${clockInAt}`,
            clock_out_at: `${date} ${clockOutAt}`,
            clock_out_time: `${clockOutAt}`,
            overtime,
            time_late: timeLate,
            status,
          });

          if (response) {
            console.log(response)
            let message = response?.data?.message;
            if (!message) {
              message = 'Data berhasil disimpan'
            }

            const data = response?.data?.data;
            // if (data.employees) {
            //     self.employees = data.employees;
            // }
            closeModal('#modalAddAttendance');
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
      async updateAttendance() {
        let self = this;
        try {
          const {
            id,
            employeeId,
            workingPatternId,
            status,
            clockInAt,
            clockOutAt,
            overtime,
            timeLate,
          } = self.model.edit;

          const date = self.date;

          self.submitLoading = true;

          const response = await axios.post(`/attendances/${id}`, {
            employee_id: employeeId,
            date,
            working_pattern_id: workingPatternId,
            clock_in_at: `${date} ${clockInAt}`,
            clock_in_time: `${clockInAt}`,
            clock_out_at: `${date} ${clockOutAt}`,
            clock_out_time: `${clockOutAt}`,
            overtime,
            time_late: timeLate,
            status,
          });

          if (response) {
            console.log(response)
            let message = response?.data?.message;
            if (!message) {
              message = 'Data berhasil disimpan'
            }

            const data = response?.data?.data;
            // if (data.employees) {
            //     self.employees = data.employees;
            // }
            closeModal('#modalEditAttendance');
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
      onOpenAddModal(employeeId) {
        const self = this;
        if (employeeId) {
          const [employee] = self.employees.filter(employee => employee.id == employeeId);
          if (employee) {
            self.model.add.employee = employee;
            self.model.add.employeeId = employee.id;
          }
        }
      },
      onOpenEditModal(attendanceId, employeeId) {
        console.log(attendanceId, employeeId)
        const self = this;
        if (employeeId) {
          const [employee] = self.employees.filter(employee => employee.id == employeeId);
          if (employee) {
            const [attendance] = employee.attendances.filter(attendance => attendance.id == attendanceId);
            if (attendance) {
              self.model.edit.id = attendanceId;
              self.model.edit.employee = employee;
              self.model.edit.employeeId = employee.id;
              self.model.edit.workingPatternId = attendance.working_pattern_id;
              self.model.edit.status = attendance.status;
              self.model.edit.clockInAt = attendance.clock_in_time;
              self.model.edit.clockOutAt = attendance.clock_out_time;
              self.model.edit.overtime = attendance.overtime;
              self.model.edit.timeLate = attendance.time_late;
            }
          }
        }
      },
      openDeleteConfirmation(id) {
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
            return self.sendDeleteRequest(id);
          },
          allowOutsideClick: () => !Swal.isLoading(),
          backdrop: true,
        })
      },
      sendDeleteRequest(id) {
        const self = this;
        return axios.delete('/attendances/' + id)
          .then(function(response) {
            let message = response?.data?.message;
            if (!message) {
              message = 'Data berhasil disimpan'
            }
            // self.deleteCompany(id);
            // redrawDatatable();
            toastr.success(message);
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
      applyFilter() {
        const {
          companyId,
          divisionId,
          officeId,
          status,
        } = this.model.filter;

        const date = this.date;
        const url = `/attendances?date=${date}&company_id=${companyId}&division_id=${divisionId}&office_id=${officeId}&status=${status}`;
        window.location.href = url;
      }
    },
  })

  const bgColor = {
    primary: KTUtil.getCssVariableValue('--bs-primary'),
    success: KTUtil.getCssVariableValue('--bs-success'),
    danger: KTUtil.getCssVariableValue('--bs-danger'),
    info: KTUtil.getCssVariableValue('--bs-info'),
    warning: KTUtil.getCssVariableValue('--bs-warning'),
    light: KTUtil.getCssVariableValue('--bs-dark'),
  }
  var options = {
    chart: {
      type: 'donut',
      toolbar: {
        show: false,
      },
      animations: {
        enabled: false,
      },
      width: '100%',
    },
    legend: {
      show: false
    },
    series: [statistics.hadir, statistics.sakit, statistics.izin, statistics.cuti, statistics.na],
    labels: ['Hadir', 'Sakit', 'Izin', 'Cuti', 'Tanpa Keterangan'],
    colors: [bgColor.success, bgColor.warning, bgColor.primary, bgColor.info, bgColor.light],
  }

  var chart = new ApexCharts(document.querySelector("#attendance_pie_chart"), options);

  chart.render();
</script>
@endsection