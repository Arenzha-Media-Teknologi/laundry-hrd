@extends('layouts.app')

@section('title', 'Pembukaan Outlet')

@section('prehead')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="
https://cdn.jsdelivr.net/npm/lightbox2@2.11.5/dist/css/lightbox.min.css
" rel="stylesheet">
@endsection

@section('pagestyle')
<style>
    .dataTables_empty {
        display: none;
    }

    .filter-badge {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }

    .filter-badge i {
        cursor: pointer;
        font-size: 0.875rem;
    }

    .filter-badge i:hover {
        opacity: 0.7;
    }

    .outlet-opening-thumbnail {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .outlet-opening-thumbnail:hover {
        transform: scale(1.1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
</style>
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0">Daftar Pembukaan Outlet</h1>
            <p class="text-muted mb-0">Daftar semua pembukaan outlet</p>
        </div>
        <div class="d-flex">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <!--begin::Add customer-->
                <a href="/outlet-openings/create" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Pembukaan Outlet</a>
                <!--end::Add customer-->
            </div>
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
                    <input type="text" data-kt-customer-table-filter="search" class="form-control w-300px ps-15" placeholder="Cari Pembukaan Outlet" />
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
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V16.1819C14.5 14.9582 14.8208 13.7559 15.4303 12.6949L20.1393 4.49814C20.5223 3.83148 20.0411 3 19.0759 3Z" fill="currentColor" />
                            </svg>
                        </span>
                        Filter
                    </button>
                    <!--begin::Menu 1-->
                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true" id="kt-toolbar-filter">
                        <!--begin::Header-->
                        <div class="px-7 py-5">
                            <div class="fs-5 fw-bold text-dark">Filter Pembukaan Outlet</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Separator-->
                        <div class="separator border-gray-200"></div>
                        <!--end::Separator-->
                        <!--begin::Content-->
                        <div class="px-7 py-5" data-kt-customer-table-filter="form">
                            <!--begin::Input group-->
                            <div class="mb-10">
                                <label class="form-label fs-6 fw-bold text-dark">Outlet</label>
                                <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Pilih Outlet" data-allow-clear="true" data-kt-customer-table-filter="office" id="filter_office_id">
                                    <option value="">Semua Outlet</option>
                                    @foreach($offices as $office)
                                    <option value="{{ $office->id }}">{{ $office->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10">
                                <label class="form-label fs-6 fw-bold text-dark">Tanggal Awal</label>
                                <input type="date" class="form-control form-control-solid" placeholder="Pilih Tanggal Awal" data-kt-customer-table-filter="start_date" id="filter_start_date" />
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10">
                                <label class="form-label fs-6 fw-bold text-dark">Tanggal Akhir</label>
                                <input type="date" class="form-control form-control-solid" placeholder="Pilih Tanggal Akhir" data-kt-customer-table-filter="end_date" id="filter_end_date" />
                            </div>
                            <!--end::Input group-->
                            <!--begin::Actions-->
                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-light btn-active-light-primary fw-bold me-2 px-6" data-kt-menu-dismiss="true" data-kt-customer-table-filter="reset">Reset</button>
                                <button type="submit" class="btn btn-primary fw-bold px-6" data-kt-menu-dismiss="true" data-kt-customer-table-filter="filter">Terapkan</button>
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Menu 1-->
                    <!--end::Filter-->
                </div>
                <!--end::Toolbar-->
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <!--begin::Active Filters-->
            <div id="active_filters" class="d-flex flex-wrap align-items-center gap-2 mb-5" style="display: none;">
                <span class="text-muted fw-bold me-2">Filter Aktif:</span>
            </div>
            <!--end::Active Filters-->
            <!--begin::Table-->
            <table class=" use-datatable table align-middle table-row-dashed fs-7" id="outlet_opening_table">
                <!--begin::Table head-->
                <thead class="bg-light-primary">
                    <!--begin::Table row-->
                    <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                        <th class="ps-2">Tanggal</th>
                        <th>Jam</th>
                        <th>Nomor</th>
                        <th>Outlet</th>
                        <th>Penanggung Jawab</th>
                        <th>Gambar</th>
                        <th>Status Waktu</th>
                        <th>Status Persetujuan</th>
                        <th class="text-end min-w-70px pe-2">Actions</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="fw-bold text-gray-600">

                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="
https://cdn.jsdelivr.net/npm/lightbox2@2.11.5/dist/js/lightbox.min.js
"></script>
@endsection

@section('pagescript')
<script>
    let outletOpeningDataTable = null;
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

        const outletOpeningDataTable = $('#outlet_opening_table').DataTable({
            // autoWidth: false,
            order: false,
            ajax: {
                url: '/outlet-openings/datatable',
                data: function(d) {
                    d.start_date = $('#filter_start_date').val();
                    d.end_date = $('#filter_end_date').val();
                    d.office_id = $('#filter_office_id').val();
                }
            },
            processing: true,
            serverSide: true,
            // columnDefs: [{
            //     width: 500,
            //     targets: 0
            // }],
            columns: [{
                    data: 'formatted_date',
                    name: 'date',
                    class: "ps-2",
                    // width: 500,
                },
                {
                    data: 'time',
                    name: 'time',
                    class: "text-center"
                },
                {
                    data: 'number',
                    name: 'number',
                    class: "text-center"
                },
                {
                    data: 'office_name',
                    name: 'office.name',
                },
                {
                    data: 'person_in_charge',
                    name: 'creator.name',
                },
                {
                    data: 'image_preview',
                    name: 'image',
                    orderable: false,
                    searchable: false,
                    class: "text-center"
                },
                {
                    data: 'timeliness_status',
                    name: 'timeliness_status',
                    class: "text-center"
                },
                {
                    data: 'approval_status',
                    name: 'approval_status',
                    class: "text-center"
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

                // Initialize lightbox for dynamically loaded images
                // Use setTimeout to ensure DOM is fully updated before refreshing lightbox
                setTimeout(function() {
                    if (typeof lightbox !== 'undefined') {
                        // Refresh lightbox to detect new dynamically loaded elements
                        lightbox.refresh();
                    }
                }, 200);
            }
        });

        const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            outletOpeningDataTable.search(e.target.value).draw();
        });

        // Format date to Indonesian format
        const formatDate = (dateString) => {
            if (!dateString) return '';
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        };

        // Update active filters display
        const updateActiveFilters = () => {
            const activeFiltersDiv = $('#active_filters');
            activeFiltersDiv.find('.filter-badge').remove();

            const officeId = $('#filter_office_id').val();
            const startDate = $('#filter_start_date').val();
            const endDate = $('#filter_end_date').val();

            let hasActiveFilter = false;

            // Office filter
            if (officeId) {
                const officeName = $('#filter_office_id option:selected').text();
                activeFiltersDiv.append(`
                    <span class="badge badge-light-primary filter-badge" data-filter-type="office">
                        Outlet: ${officeName}
                        <i class="bi bi-x-circle ms-2 cursor-pointer" onclick="removeFilter('office')"></i>
                    </span>
                `);
                hasActiveFilter = true;
            }

            // Start date filter
            if (startDate) {
                activeFiltersDiv.append(`
                    <span class="badge badge-light-info filter-badge" data-filter-type="start_date">
                        Tanggal Awal: ${formatDate(startDate)}
                        <i class="bi bi-x-circle ms-2 cursor-pointer" onclick="removeFilter('start_date')"></i>
                    </span>
                `);
                hasActiveFilter = true;
            }

            // End date filter
            if (endDate) {
                activeFiltersDiv.append(`
                    <span class="badge badge-light-success filter-badge" data-filter-type="end_date">
                        Tanggal Akhir: ${formatDate(endDate)}
                        <i class="bi bi-x-circle ms-2 cursor-pointer" onclick="removeFilter('end_date')"></i>
                    </span>
                `);
                hasActiveFilter = true;
            }

            // Show/hide active filters container
            if (hasActiveFilter) {
                activeFiltersDiv.css('display', 'flex');
            } else {
                activeFiltersDiv.css('display', 'none');
            }
        };

        // Remove specific filter
        window.removeFilter = function(filterType) {
            if (filterType === 'office') {
                $('#filter_office_id').val('').trigger('change');
            } else if (filterType === 'start_date') {
                $('#filter_start_date').val('');
            } else if (filterType === 'end_date') {
                $('#filter_end_date').val('');
            }
            updateActiveFilters();
            outletOpeningDataTable.ajax.reload();
        };

        // Filter handlers
        const handleFilter = () => {
            updateActiveFilters();
            outletOpeningDataTable.ajax.reload();
        };

        const handleReset = () => {
            $('#filter_office_id').val('').trigger('change');
            $('#filter_start_date').val('');
            $('#filter_end_date').val('');
            updateActiveFilters();
            outletOpeningDataTable.ajax.reload();
        };

        // Submit filter
        document.querySelector('[data-kt-customer-table-filter="filter"]')?.addEventListener('click', function(e) {
            e.preventDefault();
            handleFilter();
        });

        // Reset filter
        document.querySelector('[data-kt-customer-table-filter="reset"]')?.addEventListener('click', function(e) {
            e.preventDefault();
            handleReset();
        });

        // Initialize active filters on page load
        updateActiveFilters();

        // Initialize lightbox after DataTable is created
        // Lightbox2 automatically supports event delegation, but we need to refresh after dynamic content loads
        if (typeof lightbox !== 'undefined') {
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'fadeDuration': 200,
                'imageFadeDuration': 200,
                'albumLabel': 'Gambar %1 dari %2'
            });
        }


        $('#outlet_opening_table').on('click', 'td .btn-delete', function(e) {
            openDeleteConfirmation($(this).attr('data-id'), 'pembukaan outlet', $(this).attr('data-status'));
        })

        $('#outlet_opening_table').on('click', 'td .btn-reject', function(e) {
            openRejectConfirmation($(this).attr('data-id'), 'pembukaan outlet');
        })

        $('#outlet_opening_table').on('click', 'td .btn-approve', function(e) {
            openApproveConfirmation($(this).attr('data-id'), 'pembukaan outlet');
        })
    })

    function openDeleteConfirmation(id, type, status) {
        let html = '<span>Data akan dihapus</span>';
        if (status == 'approved') {
            html = `<div class="alert alert-warning" role="alert">
                <strong>Peringatan:</strong> Laporan pembukaan outlet ini sudah disetujui. Menghapus data ini akan membatalkan denda keterlambatan pembukaan outlet.
                </div>`;
        }

        Swal.fire({
            title: 'Apakah anda yakin?',
            html,
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
                return sendDeleteRequest(id, type);
            },
            allowOutsideClick: () => !Swal.isLoading(),
            backdrop: true,
        })
    }

    function sendDeleteRequest(id, type) {
        let url = null;
        if (type == 'pembukaan outlet') {
            url = `/outlet-openings/${id}`;
        }

        if (url) {
            return axios.delete(url)
                .then(function(response) {
                    let message = response?.data?.message;
                    if (!message) {
                        message = 'Data berhasil dihapus'
                    }
                    toastr.success(message);
                    document.location.reload();
                })
                .catch(function(error) {
                    console.error(error)
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                });
        }
        return null;
    }

    function openApproveConfirmation(id, type) {
        Swal.fire({
            title: 'Setujui Laporan?',
            text: "Laporan pembukaan outlet akan disetujui",
            icon: 'question',
            reverseButtons: true,
            showCancelButton: true,
            confirmButtonText: 'Setujui',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-light"
            },
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return sendApproveRequest(id, type);
            },
            allowOutsideClick: () => !Swal.isLoading(),
            backdrop: true,
        })
    }

    function openRejectConfirmation(id, type) {
        Swal.fire({
            title: 'Tolak Laporan?',
            text: "Laporan pembukaan outlet akan ditolak",
            icon: 'warning',
            reverseButtons: true,
            showCancelButton: true,
            confirmButtonText: 'Tolak',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-light"
            },
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return sendRejectRequest(id, type);
            },
            allowOutsideClick: () => !Swal.isLoading(),
            backdrop: true,
        })
    }

    function sendApproveRequest(id, type) {
        let url = null;
        if (type == 'pembukaan outlet') {
            url = `/outlet-openings/${id}/approve`;
        }

        if (url) {
            return axios.post(url)
                .then(function(response) {
                    let message = response?.data?.message;
                    if (!message) {
                        message = 'Laporan berhasil disetujui'
                    }
                    toastr.success(message);
                    document.location.reload();
                })
                .catch(function(error) {
                    console.error(error)
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                });
        }
        return null;
    }

    function sendRejectRequest(id, type) {
        let url = null;
        if (type == 'pembukaan outlet') {
            url = `/outlet-openings/${id}/reject`;
        }

        if (url) {
            return axios.post(url)
                .then(function(response) {
                    let message = response?.data?.message;
                    if (!message) {
                        message = 'Laporan berhasil ditolak'
                    }
                    toastr.success(message);
                    document.location.reload();
                })
                .catch(function(error) {
                    console.error(error)
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                });
        }
        return null;
    }
</script>
<script>
    const outletOpenings = [];

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                outletOpenings,
            }
        },
        methods: {
            // OUTLET OPENING METHODS - Delete functionality handled by global functions
        },
    })
</script>
@endsection