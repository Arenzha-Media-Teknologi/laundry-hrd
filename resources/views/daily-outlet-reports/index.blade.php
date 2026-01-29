@extends('layouts.app')

@section('title', 'Laporan Harian Outlet')

@section('prehead')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('pagestyle')
<style>
    .dataTables_empty {
        display: none;
    }

    .status-badge {
        padding: 0.5rem 0.75rem;
        border-radius: 0.475rem;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-opened {
        background-color: #d4edda;
        color: #155724;
    }

    .status-not-opened {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-on-time {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .status-late {
        background-color: #fff3cd;
        color: #856404;
    }

    .statistics-card {
        border-left: 4px solid;
    }

    .statistics-card.total {
        border-left-color: #0d6efd;
    }

    .statistics-card.opened {
        border-left-color: #198754;
    }

    .statistics-card.not-opened {
        border-left-color: #dc3545;
    }
</style>
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="mb-0">Laporan Harian Outlet</h1>
            <p class="text-muted mb-0">Laporan pembukaan outlet harian</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <input type="date" id="filter_date" class="form-control" value="{{ $date }}" style="width: 200px;">
            @if($employee)
            <input type="hidden" id="employee_id" value="{{ $employee->id }}">
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-5 g-xl-8 mb-5">
        <div class="col-xl-4">
            <div class="card statistics-card total">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-gray-500 fw-semibold fs-6 d-block">Total Outlet</span>
                            <span class="text-gray-800 fw-bold fs-2qx">{{ $statistics['total'] }}</span>
                        </div>
                        <div class="symbol symbol-50px">
                            <div class="symbol-label bg-light-primary">
                                <i class="bi bi-shop fs-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card statistics-card opened">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-gray-500 fw-semibold fs-6 d-block">Sudah Dibuka</span>
                            <span class="text-gray-800 fw-bold fs-2qx">{{ $statistics['opened'] }}</span>
                        </div>
                        <div class="symbol symbol-50px">
                            <div class="symbol-label bg-light-success">
                                <i class="bi bi-check-circle fs-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card statistics-card not-opened">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-gray-500 fw-semibold fs-6 d-block">Belum Dibuka</span>
                            <span class="text-gray-800 fw-bold fs-2qx">{{ $statistics['not_opened'] }}</span>
                        </div>
                        <div class="symbol symbol-50px">
                            <div class="symbol-label bg-light-danger">
                                <i class="bi bi-x-circle fs-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <span class="svg-icon svg-icon-1 position-absolute ms-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                        </svg>
                    </span>
                    <input type="text" id="search_input" class="form-control w-300px ps-15" placeholder="Cari Outlet" />
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="outlet_table">
                    <thead class="bg-light-primary">
                        <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                            <th class="ps-2">Nama Outlet</th>
                            <th class="text-center">Waktu Buka</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Waktu Aktual</th>
                            <th class="text-center">Ketepatan</th>
                            <th class="text-center">Pegawai Hadir</th>
                            <th class="text-center">Total Pegawai</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="fw-bold text-gray-600">
                        @forelse($outlets as $outlet)
                        <tr>
                            <td class="ps-2">
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold">{{ $outlet['name'] }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="text-gray-600">{{ $outlet['open_time'] ?? '-' }}</span>
                            </td>
                            <td class="text-center">
                                @if($outlet['is_opened'])
                                <span class="status-badge status-opened">Sudah Dibuka</span>
                                @else
                                <span class="status-badge status-not-opened">Belum Dibuka</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="text-gray-600">{{ $outlet['actual_open_time'] ?? '-' }}</span>
                            </td>
                            <td class="text-center">
                                @if($outlet['timeliness_status'])
                                @if($outlet['timeliness_status'] == 'on_time')
                                <span class="status-badge status-on-time">Tepat Waktu</span>
                                @elseif($outlet['timeliness_status'] == 'late')
                                <span class="status-badge status-late">Terlambat</span>
                                @else
                                <span class="text-gray-600">{{ ucfirst($outlet['timeliness_status']) }}</span>
                                @endif
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="text-gray-800 fw-bold">{{ $outlet['present_employees_count'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-gray-600">{{ count($outlet['employees']) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('daily-outlet-reports.show', $outlet['id']) }}?employee_id={{ $employee->id ?? '' }}&date={{ $date }}"
                                    class="btn btn-sm btn-light-primary"
                                    title="Lihat Detail">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-10">
                                <div class="text-gray-500">Tidak ada data outlet</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
    $(document).ready(function() {
        // Initialize DataTable with pagination
        const table = $('#outlet_table').DataTable({
            // order: [
            //     [0, 'asc']
            // ],
            // pageLength: 15,
            // paging: true,
            // pagingType: "full_numbers", // Show page numbers
            // info: true,
            // language: {
            //     search: "",
            //     searchPlaceholder: "Cari...",
            //     lengthMenu: "Tampilkan _MENU_ data",
            //     info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            //     infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            //     infoFiltered: "(disaring dari _TOTAL_ total data)",
            //     paginate: {
            //         first: "Pertama",
            //         last: "Terakhir",
            //         next: "Selanjutnya",
            //         previous: "Sebelumnya"
            //     }
            // }
        });

        // Search functionality
        $('#search_input').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Date filter
        $('#filter_date').on('change', function() {
            const date = $(this).val();
            const employeeId = $('#employee_id').val();
            const url = new URL(window.location.href);
            url.searchParams.set('date', date);
            url.searchParams.delete('page'); // Reset to first page when date changes
            if (employeeId) {
                url.searchParams.set('employee_id', employeeId);
            }
            window.location.href = url.toString();
        });
    });
</script>
@endsection