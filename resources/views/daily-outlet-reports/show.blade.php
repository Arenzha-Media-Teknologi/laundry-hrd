@extends('layouts.app')

@section('title', 'Detail Laporan Harian Outlet')

@section('prehead')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('pagestyle')
<style>
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

    .info-card {
        border-left: 4px solid #0d6efd;
    }
</style>
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="mb-0">Detail Laporan Harian Outlet</h1>
            <p class="text-muted mb-0">{{ $outlet['name'] }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="mb-0">
                <i class="bi bi-calendar3 fs-4 me-2"></i>
                <span class="fw-bolder text-gray-600">{{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY') }}</span>
            </div>
            <a href="{{ route('daily-outlet-reports.index') }}?employee_id={{ $employee->id ?? '' }}&date={{ $date }}" class="btn btn-light">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Outlet Information Card -->
    <div class="card info-card mb-5">
        <div class="card-header">
            <div class="card-title">
                <h2>Informasi Outlet</h2>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-5">
                <div class="col-md-6">
                    <div class="d-flex flex-column mb-5">
                        <span class="text-gray-500 fw-semibold fs-6 mb-2">Nama Outlet</span>
                        <span class="text-gray-800 fw-bold fs-4">{{ $outlet['name'] }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column mb-5">
                        <span class="text-gray-500 fw-semibold fs-6 mb-2">Waktu Buka</span>
                        <span class="text-gray-800 fw-bold fs-4">{{ $outlet['open_time'] ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column mb-5">
                        <span class="text-gray-500 fw-semibold fs-6 mb-2">Status</span>
                        @if($outlet['is_opened'])
                        <span class="status-badge status-opened">Sudah Dibuka</span>
                        @else
                        <span class="status-badge status-not-opened">Belum Dibuka</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column mb-5">
                        <span class="text-gray-500 fw-semibold fs-6 mb-2">Waktu Aktual Buka</span>
                        <span class="text-gray-800 fw-bold fs-4">{{ $outlet['actual_open_time'] ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column mb-5">
                        <span class="text-gray-500 fw-semibold fs-6 mb-2">Ketepatan Waktu</span>
                        @if($outlet['timeliness_status'])
                        @if($outlet['timeliness_status'] == 'on_time')
                        <span class="status-badge status-on-time">Tepat Waktu</span>
                        @elseif($outlet['timeliness_status'] == 'late')
                        <span class="status-badge status-late">Terlambat</span>
                        @else
                        <span class="text-gray-800 fw-bold">{{ ucfirst(str_replace('_', ' ', $outlet['timeliness_status'])) }}</span>
                        @endif
                        @else
                        <span class="text-gray-600">-</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column mb-5">
                        <span class="text-gray-500 fw-semibold fs-6 mb-2">Pegawai Hadir</span>
                        <span class="text-gray-800 fw-bold fs-4">{{ $outlet['present_employees_count'] }} / {{ count($outlet['employees']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Structure Card -->
    <div class="card mb-5">
        <div class="card-header">
            <div class="card-title">
                <h2>Struktur Manajemen</h2>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-5">
                <div class="col-md-4">
                    <div class="d-flex flex-column">
                        <span class="text-gray-500 fw-semibold fs-6 mb-3">Area Manager</span>
                        @if(count($outlet['area_managers']) > 0)
                        @foreach($outlet['area_managers'] as $manager)
                        <div class="d-flex align-items-center mb-2">
                            <div class="symbol symbol-40px me-3">
                                <div class="symbol-label bg-light-primary">
                                    <i class="bi bi-person-badge fs-2x text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-gray-800 fw-bold">{{ $manager['name'] }}</span>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <span class="text-gray-500">Tidak ada data</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex flex-column">
                        <span class="text-gray-500 fw-semibold fs-6 mb-3">Supervisor</span>
                        @if(count($outlet['supervisors']) > 0)
                        @foreach($outlet['supervisors'] as $supervisor)
                        <div class="d-flex align-items-center mb-2">
                            <div class="symbol symbol-40px me-3">
                                <div class="symbol-label bg-light-success">
                                    <i class="bi bi-person-check fs-2x text-success"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-gray-800 fw-bold">{{ $supervisor['name'] }}</span>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <span class="text-gray-500">Tidak ada data</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex flex-column">
                        <span class="text-gray-500 fw-semibold fs-6 mb-3">Leader</span>
                        @if(count($outlet['leaders']) > 0)
                        @foreach($outlet['leaders'] as $leader)
                        <div class="d-flex align-items-center mb-2">
                            <div class="symbol symbol-40px me-3">
                                <div class="symbol-label bg-light-warning">
                                    <i class="bi bi-person-star fs-2x text-warning"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-gray-800 fw-bold">{{ $leader['name'] }}</span>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <span class="text-gray-500">Tidak ada data</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employees Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <h2>Daftar Pegawai</h2>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="employees_table">
                    <thead class="bg-light-primary">
                        <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                            <th class="ps-2">Nama Pegawai</th>
                            <th class="text-center">Status Kehadiran</th>
                            <th class="text-center">Waktu Masuk</th>
                        </tr>
                    </thead>
                    <tbody class="fw-bold text-gray-600">
                        @forelse($outlet['employees'] as $emp)
                        <tr>
                            <td class="ps-2">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-3">
                                        @if(isset($emp['photo']) && $emp['photo'])
                                        <img src="{{ $emp['photo'] }}" alt="{{ $emp['name'] }}" class="symbol-label" />
                                        @else
                                        <div class="symbol-label bg-light-info">
                                            <i class="bi bi-person fs-2x text-info"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-gray-800 fw-bold">{{ $emp['name'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($emp['attendance_status'])
                                @if($emp['attendance_status'] == 'hadir')
                                <span class="badge badge-success">Hadir</span>
                                @elseif($emp['attendance_status'] == 'izin')
                                <span class="badge badge-warning">Izin</span>
                                @elseif($emp['attendance_status'] == 'sakit')
                                <span class="badge badge-danger">Sakit</span>
                                @else
                                <span class="badge badge-secondary">{{ ucfirst($emp['attendance_status']) }}</span>
                                @endif
                                @else
                                <span class="badge badge-light">Tanpa Keterangan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($emp['clock_in_time'])
                                <span class="text-gray-800 fw-bold">{{ \Carbon\Carbon::parse($emp['clock_in_time'])->format('H:i') }}</span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-10">
                                <div class="text-gray-500">Tidak ada data pegawai</div>
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
        // Initialize DataTable
        const table = $('#employees_table').DataTable({
            order: [
                [0, 'asc']
            ],
            pageLength: 15,
            pagingType: "full_numbers",
            info: true,
            language: {
                search: "",
                searchPlaceholder: "Cari pegawai...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _TOTAL_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    });
</script>
@endsection