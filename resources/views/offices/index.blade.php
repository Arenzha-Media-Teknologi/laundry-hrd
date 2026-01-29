@extends('layouts.app')

@section('title', 'Kantor')

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
            <h1 class="mb-0">Daftar Kantor</h1>
            <p class="text-muted mb-0">Daftar semua kantor</p>
        </div>
        <div class="d-flex">
            <div class="d-flex justify-content-end me-2" data-kt-customer-table-toolbar="base">
                <!--begin::Add customer-->
                <a href="/offices/move" class="btn btn-secondary"><i class="bi bi-arrow-left-right"></i> Pindah Kantor</a>
                <!--end::Add customer-->
            </div>
            @can('create', App\Models\Office::class)
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <!--begin::Add customer-->
                <a href="/offices/create" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Kantor</a>
                <!--end::Add customer-->
            </div>
            @endcan
        </div>
    </div>
    <div class="separator my-5" style="border-bottom-width: 3px;"></div>
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                    <span class="svg-icon svg-icon-1 position-absolute ms-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <input type="text" data-kt-customer-table-filter="search" class="form-control w-300px ps-15" placeholder="Cari Kantor" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <!--begin::Table-->
            <table class=" use-datatable table align-middle table-row-dashed fs-6" id="office_table">
                <!--begin::Table head-->
                <thead class="bg-light-primary">
                    <!--begin::Table row-->
                    <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                        <!-- <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_customers_table .form-check-input" value="1" />
                            </div>
                        </th> -->
                        <th class="ps-2">Nama</th>
                        <th>Divisi</th>
                        <th>Perusahaan</th>
                        <th>Pegawai</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Jam Buka</th>
                        <th class="text-end min-w-70px pe-2">Actions</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="fw-bold text-gray-600">
                    <tr v-for="(office, index) in offices" :key="office.id">
                        <!--begin::Name=-->
                        <td class="ps-2">
                            <span class="text-gray-800" v-cloak>@{{ office?.name }}</span>
                        </td>
                        <!--end::Name=-->
                        <!--begin::Name=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ office?.division?.name || "-" }}</span>
                        </td>
                        <!--end::Name=-->
                        <!--begin::Email=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ office?.division?.company?.name || "-" }}</span>
                        </td>
                        <!--end::Email-->
                        <!--begin::Employees=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ office?.employees_count || 0 }}</span>
                        </td>
                        <!--end::Employees-->
                        <!--begin::Phone=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ office?.phone }}</span>
                        </td>
                        <!--end::Phone-->
                        <!--begin::address=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ office?.address }}</span>
                        </td>
                        <!--end::address=-->
                        <!--begin::Opening Time=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ office?.opening_time || "-" }}</span>
                        </td>
                        <!--end::Opening Time-->
                        <!--begin::Action=-->
                        <td class="text-end pe-2">
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-sm btn-icon btn-light-primary ms-2" data-bs-toggle="modal" data-bs-target="#employees_modal" @click="openEmployeesModal(office.id)">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M17 7C17 6.4 17.4 6 18 6C18.6 6 19 6.4 19 7C19 7.6 18.6 8 18 8C17.4 8 17 7.6 17 7Z" fill="black" />
                                            <path opacity="0.3" d="M14 18V16H10V18C10 18.6 10.4 19 11 19H13C13.6 19 14 18.6 14 18Z" fill="black" />
                                            <path opacity="0.3" d="M6 12C5.4 12 5 12.4 5 13V17C5 17.6 5.4 18 6 18C6.6 18 7 17.6 7 17V13C7 12.4 6.6 12 6 12Z" fill="black" />
                                            <path opacity="0.3" d="M18 12C17.4 12 17 12.4 17 13V17C17 17.6 17.4 18 18 18C18.6 18 19 17.6 19 17V13C19 12.4 18.6 12 18 12Z" fill="black" />
                                            <path d="M12 10C13.1 10 14 10.9 14 12C14 13.1 13.1 14 12 14C10.9 14 10 13.1 10 12C10 10.9 10.9 10 12 10ZM12 20C7.6 20 4 16.4 4 12C4 7.6 7.6 4 12 4C16.4 4 20 7.6 20 12C20 16.4 16.4 20 12 20ZM12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2Z" fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </button>
                                @can('update', App\Models\Office::class)
                                <!--begin::Share link-->
                                <a :href="`/offices/${office.id}/edit`" class="btn btn-sm btn-icon btn-light-info ms-2">
                                    <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </a>
                                @endcan
                                @can('delete', App\Models\Office::class)
                                <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2" @click="openDeleteConfirmation(office.id)">
                                    <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black" />
                                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black" />
                                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </button>
                                @endcan
                            </div>
                        </td>
                        <!--end::Action=-->
                    </tr>
                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
    <!--begin::Modal - Employees-->
    <div class="modal fade" tabindex="-1" id="employees_modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Daftar Pegawai</h5>
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
                    <div v-cloak v-if="selectedOffice">
                        <div class="mb-5">
                            <h6 class="text-gray-800 fw-bold mb-2">@{{ selectedOffice?.name }}</h6>
                            <div class="text-gray-600">
                                <div>Divisi: @{{ selectedOffice?.division?.name || '-' }}</div>
                                <div>Perusahaan: @{{ selectedOffice?.division?.company?.name || '-' }}</div>
                            </div>
                        </div>
                        <div class="separator mb-5"></div>
                        <div v-if="loadingEmployees" class="text-center py-10">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div v-else>
                            <div v-if="employees.length === 0" class="text-center py-10 text-gray-500">
                                Tidak ada pegawai di kantor ini
                            </div>
                            <div v-else>
                                <div class="table-responsive" style="max-height: 400px;">
                                    <table class="table table-striped table-hover">
                                        <thead class="bg-light-primary">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Jabatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(employee, index) in employees" :key="employee.id">
                                                <td>@{{ index + 1 }}</td>
                                                <td>@{{ employee.name }}</td>
                                                <td>@{{ employee.active_career?.job_title?.name || '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Employees-->
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

        datatable = $('.use-datatable').DataTable({
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
    })
</script>
<script>
    const offices = <?php echo Illuminate\Support\Js::from($offices) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                offices,
                selectedOffice: null,
                employees: [],
                loadingEmployees: false,
            }
        },
        methods: {
            // EMPLOYEES METHODS
            openEmployeesModal(officeId) {
                const self = this;
                self.loadingEmployees = true;
                self.selectedOffice = null;
                self.employees = [];

                // Find office from the list
                const office = this.offices.find(o => o.id === officeId);
                if (office) {
                    self.selectedOffice = office;
                }

                // Fetch employees
                axios.get(`/offices/${officeId}/employees`)
                    .then(function(response) {
                        self.selectedOffice = response.data.office;
                        self.employees = response.data.employees;
                        self.loadingEmployees = false;
                    })
                    .catch(function(error) {
                        console.error(error);
                        let message = error?.response?.data?.message || 'Terjadi kesalahan saat mengambil data pegawai';
                        toastr.error(message);
                        self.loadingEmployees = false;
                    });
            },
            // COMPANY METHODS
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
                return axios.delete('/offices/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        self.deleteOffice(id);
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
            deleteOffice(id) {
                this.offices = this.offices.filter(office => office.id !== id);
            },
        },
    })
</script>
@endsection