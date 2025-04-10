<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EventCalendar;
use App\Models\Leave;
use App\Models\LeaveApplication;
use App\Models\LeaveCategory;
use App\Models\PermissionApplication;
use App\Models\PermissionCategory;
use App\Models\SickApplication;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TimeOffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $year = request()->query('year') ?? date('Y');
        $AERPLUS_DIVISION_ID = 12;
        $permissions = auth()->user()->group->permissions ?? "[]";
        $permissions = json_decode($permissions);
        $aerplusOnly = in_array('aerplus_only', $permissions);
        $exceptServer = in_array('except_server', $permissions);
        // return $aerplusOnly;
        $sickApplications = SickApplication::with(['employee']);

        if ($aerplusOnly) {
            $sickApplications = $sickApplications->whereHas('employee', function ($q) {
                $q->whereHas('office', function ($q2) {
                    $q2->where('division_id', 12);
                });
            });
        }

        if ($exceptServer) {
            $sickApplications = $sickApplications->whereHas('employee', function ($q) {
                $q->whereHas('activeCareer', function ($q2) {
                    $q2->whereIn('job_title_id', [190, 204, 233, 239]);
                });
            });
        }

        $sickApplications = $sickApplications->where('application_dates', 'LIKE', '%' . $year . '%')->orderBy('date', 'DESC')->get();
        $permissionApplications = PermissionApplication::whereHas('employee', function ($q) {
            $q->whereHas('office', function ($q2) {
                $q2->where('division_id', 12);
            });
        })->with(['employee', 'category'])->get();

        $leaveApplications = LeaveApplication::with(['employee']);

        if ($aerplusOnly) {
            $leaveApplications = $leaveApplications->whereHas('employee', function ($q) {
                $q->whereHas('office', function ($q2) {
                    $q2->where('division_id', 12);
                });
            });
        }

        if ($exceptServer) {
            $leaveApplications = $leaveApplications->whereHas('employee', function ($q) {
                $q->whereHas('activeCareer', function ($q2) {
                    $q2->whereIn('job_title_id', [190, 204, 233, 239]);
                });
            });
        }

        $leaveApplications = $leaveApplications->where('leave_category_id', '!=', 8)->where('application_dates', 'LIKE', '%' . $year . '%')->orderBy('date', 'DESC')->get();

        //-------

        $eventLeaveApplications = LeaveApplication::with(['employee']);

        if ($aerplusOnly) {
            $eventLeaveApplications = $eventLeaveApplications->whereHas('employee', function ($q) {
                $q->whereHas('office', function ($q2) {
                    $q2->where('division_id', 12);
                });
            });
        }

        if ($exceptServer) {
            $eventLeaveApplications = $eventLeaveApplications->whereHas('employee', function ($q) {
                $q->whereHas('activeCareer', function ($q2) {
                    $q2->whereIn('job_title_id', [190, 204, 233, 239]);
                });
            });
        }

        $eventLeaveApplications = $eventLeaveApplications->where('leave_category_id', 8)->where('application_dates', 'LIKE', '%' . $year . '%')->orderBy('date', 'DESC')->get();
        // return $permissionApplications;
        return view('timeoffs.index', [
            'year' => $year,
            'sick_applications' => $sickApplications,
            'permission_applications' => $permissionApplications,
            'leave_applications' => $leaveApplications,
            'event_leave_applications' => $eventLeaveApplications,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexV2()
    {
        $pendingSickApplicationsCount = SickApplication::query()->where('approval_status', 'pending')->count();
        $pendingLeaveApplicationsCount = LeaveApplication::query()->where('approval_status', 'pending')->where('leave_category_id', '!=', 8)->count();
        $pendingEventLeaveApplicationsCount = LeaveApplication::query()->where('approval_status', 'pending')->where('leave_category_id', 8)->count();

        $companies = Company::all();

        return view('timeoffs.v2.index', [
            'pending_sick_applications_count' => $pendingSickApplicationsCount,
            'pending_leave_applications_count' => $pendingLeaveApplicationsCount,
            'pending_event_leave_applications_count' => $pendingEventLeaveApplicationsCount,
            'companies' => $companies,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $AERPLUS_DIVISION_ID = 12;
        $permissions = auth()->user()->group->permissions ?? "[]";
        $permissions = json_decode($permissions);
        $aerplusOnly = in_array('aerplus_only', $permissions);
        $exceptServer = in_array('except_server', $permissions);

        $massLeaves = EventCalendar::query()->where('type', 'cuti_bersama')->whereYear('date', date('Y'))->get();
        // $leaveQuota = 12;

        $employees = Employee::query();
        if ($aerplusOnly) {
            $employees = $employees->whereHas('office', function ($q2) {
                $q2->where('division_id', 12);
            });
        }

        if ($exceptServer) {
            $employees = $employees->whereHas('activeCareer', function ($q2) {
                $q2->whereIn('job_title_id', [190, 204, 233, 239]);
            });
        }

        $employees = $employees->with([
            'office.division.company',
            'activeCareer.jobTitle',
            // 'leave',
            'leaveApplications' => function ($q) {
                $q->whereHas('category', function ($q2) {
                    $q2->where('type', 'annual_leave');
                })
                    ->where('approval_status', 'approved')
                    ->where('application_dates', 'like', '%' . date('Y') . '%');
            },
            'leave',
        ])
            ->get()
            ->each(function ($employee) use ($massLeaves) {
                $leaveQuota = $employee->leave->total ?? 0;
                $leaveApplicationsDates = collect($employee->leaveApplications)->pluck('application_dates')->flatMap(function ($leaveApplicationDates) {
                    return explode(',', $leaveApplicationDates);
                })->all();

                $leaveApplicationsUniqueDates = array_unique($leaveApplicationsDates);
                // $leavePeriod = new CarbonPeriod('2022-01-01', '2022-12-30');
                // $leavePeriodDates = [];
                // foreach ($leavePeriod as $key => $date) {
                //     $leavePeriodDates[] = $date->format('Y-m-d');
                // }
                $startDate = date('Y-01-01');
                $endDate = date('Y-12-t');

                $leaveApplicationsCount = collect($leaveApplicationsUniqueDates)->filter(function ($date) use ($startDate, $endDate) {
                    return $date >= $startDate && $date <= $endDate;
                })->count();

                $massLeavesCount = collect($massLeaves)->count();

                $takenLeave = $leaveApplicationsCount + $massLeavesCount;
                $remainingLeave = $leaveQuota - $takenLeave;
                $leave = [
                    'total' => $leaveQuota,
                    'taken' => $takenLeave,
                    'remaining' => $remainingLeave,
                ];

                // $employee->leave_application_dates = array_unique($leaveApplicationsDates);
                // $employee->leave_application_dates_unique = array_unique($leaveApplicationsDates);
                unset($employee->leave);
                $employee->leave = $leave;
                // $employee->new_leave = $leave;
                // $employee->leave_application_count = 0;
            });



        // return $employees;   

        $permissionCategories = PermissionCategory::all();
        $leaveCategories = LeaveCategory::all();
        return view('timeoffs.create', [
            'employees' => $employees,
            'permission_categories' => $permissionCategories,
            'leave_categories' => $leaveCategories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $type = $request->query('type');
        if ($type == null) {
            abort(404, '"type" is required');
        }

        if (!in_array($type, ['sakit', 'izin', 'cuti'])) {
            abort(404, 'only valid types are sakit, izin, and cuti');
        }

        $timeOff = null;
        if ($type == 'sakit') {
            $timeOff = SickApplication::with(['employee'])->findOrFail($id);
        } else if ($type == 'izin') {
            $timeOff = PermissionApplication::with(['employee'])->findOrFail($id);
        } else if ($type == 'cuti') {
            $timeOff = LeaveApplication::with(['employee'])->findOrFail($id);
        }

        if ($timeOff !== null) {
            if ($timeOff->approval_status !== 'pending') {
                abort(404);
            }
        }

        $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle', 'leave'])->get();
        $permissionCategories = PermissionCategory::all();
        return view('timeoffs.edit', [
            'time_off' => $timeOff,
            'employees' => $employees,
            'permission_categories' => $permissionCategories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function statusColor($status)
    {
        switch ($status) {
            case 'approved':
                return 'light-success';
            case 'pending':
                return 'light-warning';
            case 'rejected':
                return 'light-danger';
            default:
                return 'light';
        }
    }

    private function employeeColumn(Employee $employee)
    {
        $employeeColumn = ' <div class="d-flex align-items-center">';

        if (isset($employee->photo) && !empty($employee->photo)) {
            $employeeColumn .= '
            <div class="symbol symbol-40px symbol-circle">
                <div class="symbol-label" style="background-image:url(\'' . $employee->photo . '\')"></div>
            </div>
            ';
        } else {
            $employeeColumn .= '<div class="symbol symbol-40px symbol-circle">
                <div class="symbol-label fs-2 fw-semibold text-primary">' . substr($employee->name, 0, 1) . '</div>
            </div>';
        }

        $employeeColumn .= '<div class="ps-3">
            <p class="mb-1 fs-7 fw-bolder"><a href="/employees/' . $employee->id . '/detail-v2" class="text-gray-700 text-hover-primary">' . $employee->name . '</a></p>
                <span class="fs-7 text-muted">' . ($employee->office->division->company->name ?? '') . '</span>
        </div>';


        $employeeColumn .= ' </div>';

        return $employeeColumn;
    }

    public function datatableSickApplications()
    {
        $companyId = request()->query('company_id');
        $approvalStatus = request()->query('status');

        $sickApplications = SickApplication::query();

        if (isset($companyId) && !empty($companyId)) {
            $sickApplications = $sickApplications->whereHas('employee.office.division.company', function ($q) use ($companyId) {
                $q->where('id', $companyId);
            });
        }

        if (isset($approvalStatus) && !empty($approvalStatus)) {
            $sickApplications = $sickApplications->where('approval_status', $approvalStatus);
        }


        $sickApplications = $sickApplications->with(['employee' => function ($q) {
            $q->with(['activeCareer.jobTitle', 'office.division.company']);
        }]);


        $sickApplications = $sickApplications->orderBy('date', 'DESC')->select('sick_applications.*');

        return DataTables::eloquent($sickApplications)
            ->addColumn('employee', function (SickApplication $sickApplication) {
                if (isset($sickApplication->employee)) {
                    return $this->employeeColumn($sickApplication->employee);
                }
                return 'NAMA_PEGAWAI';
            })
            ->addColumn('formatted_date', function (SickApplication $sickApplication) {
                return Carbon::parse($sickApplication->date)->format('d/m/Y');
            })
            ->addColumn('formatted_application_dates', function (SickApplication $sickApplication) {
                $applicationDates = explode(',', $sickApplication->application_dates);
                $applicationStartDate = min($applicationDates) ?? '';
                $applicationEndDate = max($applicationDates) ?? '';
                $dates = '';
                if (count($applicationDates) > 1) {
                    $dates = Carbon::parse($applicationStartDate)->isoFormat('LL') . ' - ' . Carbon::parse($applicationEndDate)->isoFormat('LL');
                } else {
                    $dates = Carbon::parse($applicationStartDate)->isoFormat('LL');
                }

                $formattedApplicationDates = collect($applicationDates)->map(function ($applicationDate) {
                    return Carbon::parse($applicationDate)->format('d/m/Y');
                })->sort()->join(', ');
                return '
                <span class="badge badge-secondary cursor-pointer" data-bs-toggle="popover" title="Detail Tanggal" data-bs-content="' . $formattedApplicationDates . '">' . $dates . '</span>
              ';
            })
            ->addColumn('formatted_attachment', function (SickApplication $sickApplication) {
                $paths = explode('/', $sickApplication->attachment);
                $fileName = $paths[count($paths) - 1] ?? "";
                if (!empty($sickApplication->attachment)) {
                    return '<a href="' . $sickApplication->attachment . '" target="_blank"><span class="badge badge-light-info"><svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="16" height="16" class="bi bi-file-earmark-post-fill align-middle" viewBox="0 0 16 16">
                    <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1m-5-.5H7a.5.5 0 0 1 0 1H4.5a.5.5 0 0 1 0-1m0 3h7a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .5-.5"/><span class="ms-2 align-middle">' . $fileName . '</span></span></a>';
                }

                return '';
            })
            ->addColumn('status', function (SickApplication $sickApplication) {
                return ' <span class="badge badge-' . $this->statusColor($sickApplication->approval_status) . '  text-uppercase">
                ' . $sickApplication->approval_status . '
            </span>';
            })
            ->addColumn('action', function (SickApplication $sickApplication) {
                $action = '<div class="d-flex justify-content-end">';
                //                 <div class="btn-group" role="group" aria-label="Basic example">
                //   <button type="button" class="btn btn-primary">Left</button>
                //   <button type="button" class="btn btn-primary">Middle</button>
                //   <button type="button" class="btn btn-primary">Right</button>
                // </div>
                $action = '<div class="btn-group mt-2" role="group" aria-label="Basic example">';
                if (request()->user()->can('update', SickApplication::class)) {
                    if ($sickApplication->approval_status == 'pending') {
                        $action .= '<a href="/time-offs/' . $sickApplication->id . '/edit?type=sakit" class="btn btn-sm btn-icon btn-secondary">
                        <span class="svg-icon svg-icon-5 m-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                        </svg>
                        </span></a>';
                    }
                }
                if (request()->user()->can('delete', SickApplication::class)) {
                    $action .= '<button type="button" class="btn btn-sm btn-icon btn-secondary btn-delete"  data-id="' . $sickApplication->id . '" data-status="' . $sickApplication->approval_status . '">
                    <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                    <span class="svg-icon svg-icon-5 m-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black" />
                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black" />
                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </button>';
                }
                $action .= '</div>';

                $action .= '<div class="btn-group mt-2" role="group" aria-label="Basic example">';
                if (request()->user()->can('update', SickApplication::class)) {
                    if ($sickApplication->approval_status == 'pending') {
                        $action .= '<button class="btn btn-sm btn-icon btn-secondary btn-reject" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Tolak" data-id="' . $sickApplication->id . '">
                        <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                        <span class="svg-icon svg-icon-5 m-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </button>
                    <button class="btn btn-sm btn-icon btn-secondary btn-approve" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Setujui" data-id="' . $sickApplication->id . '">
                        <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                        <span class="svg-icon svg-icon-5 m-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z" fill="currentColor" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </button>';
                    }
                }
                $action .= '</div>';

                $action .= '</div>';

                return $action;
            })
            ->rawColumns(['employee', 'formatted_application_dates', 'formatted_attachment', 'status', 'action'])
            // ->addColumn('intro', 'Hi {{$name}}!')
            ->make(true);
    }

    public function datatableLeaveApplications()
    {
        $companyId = request()->query('company_id');
        $approvalStatus = request()->query('status');

        $leaveApplications = LeaveApplication::query();

        if (isset($companyId) && !empty($companyId)) {
            $leaveApplications = $leaveApplications->whereHas('employee.office.division.company', function ($q) use ($companyId) {
                $q->where('id', $companyId);
            });
        }

        if (isset($approvalStatus) && !empty($approvalStatus)) {
            $leaveApplications = $leaveApplications->where('approval_status', $approvalStatus);
        }

        $leaveApplications = $leaveApplications->with(['employee' => function ($q) {
            $q->with(['activeCareer.jobTitle', 'office.division.company']);
        }, 'category'])->where('leave_category_id', '!=', 8)->orderBy('date', 'DESC')->select('leave_applications.*');

        return DataTables::eloquent($leaveApplications)
            ->addColumn('employee', function (LeaveApplication $leaveApplication) {
                if (isset($leaveApplication->employee)) {
                    return $this->employeeColumn($leaveApplication->employee);
                }
                return 'NAMA_PEGAWAI';
            })
            ->addColumn('leave_category_name', function (LeaveApplication $leaveApplication) {
                return '<span class="badge badge-light-primary">' . $leaveApplication->category->name ?? "" . '</span>';
            })
            ->addColumn('formatted_date', function (LeaveApplication $leaveApplication) {
                return Carbon::parse($leaveApplication->date)->format('d/m/Y');
            })
            ->addColumn('formatted_application_dates', function (LeaveApplication $leaveApplication) {
                $applicationDates = explode(',', $leaveApplication->application_dates);
                $applicationStartDate = min($applicationDates) ?? '';
                $applicationEndDate = max($applicationDates) ?? '';
                $dates = '';
                if (count($applicationDates) > 1) {
                    $dates = Carbon::parse($applicationStartDate)->isoFormat('LL') . ' - ' . Carbon::parse($applicationEndDate)->isoFormat('LL');
                } else {
                    $dates = Carbon::parse($applicationStartDate)->isoFormat('LL');
                }

                $formattedApplicationDates = collect($applicationDates)->map(function ($applicationDate) {
                    return Carbon::parse($applicationDate)->format('d/m/Y');
                })->sort()->join(', ');
                return '
                <span class="badge badge-secondary cursor-pointer" data-bs-toggle="popover" title="Detail Tanggal" data-bs-content="' . $formattedApplicationDates . '">' . $dates . '</span>
              ';
            })
            ->addColumn('status', function (LeaveApplication $leaveApplication) {
                return ' <span class="badge badge-' . $this->statusColor($leaveApplication->approval_status) . '  text-uppercase">
                ' . $leaveApplication->approval_status . '
            </span>';
            })
            ->addColumn('action', function (LeaveApplication $leaveApplication) {
                $action = '<div class="d-flex justify-content-end">';
                //                 <div class="btn-group" role="group" aria-label="Basic example">
                //   <button type="button" class="btn btn-primary">Left</button>
                //   <button type="button" class="btn btn-primary">Middle</button>
                //   <button type="button" class="btn btn-primary">Right</button>
                // </div>
                $action = '<div class="btn-group mt-2" role="group" aria-label="Basic example">';
                if (request()->user()->can('update', LeaveApplication::class)) {
                    if ($leaveApplication->approval_status == 'pending') {
                        $action .= '<a href="/time-offs/' . $leaveApplication->id . '/edit?type=sakit" class="btn btn-sm btn-icon btn-secondary">
                        <span class="svg-icon svg-icon-5 m-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                        </svg>
                        </span></a>';
                    }
                }
                if (request()->user()->can('delete', LeaveApplication::class)) {
                    $action .= '<button type="button" class="btn btn-sm btn-icon btn-secondary btn-delete"  data-id="' . $leaveApplication->id . '" data-status="' . $leaveApplication->approval_status . '">
                    <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                    <span class="svg-icon svg-icon-5 m-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black" />
                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black" />
                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </button>';
                }
                $action .= '</div>';

                $action .= '<div class="btn-group mt-2" role="group" aria-label="Basic example">';
                if (request()->user()->can('update', LeaveApplication::class)) {
                    if ($leaveApplication->approval_status == 'pending') {
                        $action .= '<button class="btn btn-sm btn-icon btn-secondary btn-reject" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Tolak" data-id="' . $leaveApplication->id . '">
                        <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                        <span class="svg-icon svg-icon-5 m-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </button>
                    <button class="btn btn-sm btn-icon btn-secondary btn-approve" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Setujui" data-id="' . $leaveApplication->id . '">
                        <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                        <span class="svg-icon svg-icon-5 m-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z" fill="currentColor" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </button>';
                    }
                }
                $action .= '</div>';

                $action .= '</div>';

                return $action;
            })
            ->rawColumns(['employee', 'leave_category_name', 'formatted_application_dates', 'formatted_attachment', 'status', 'action'])
            // ->addColumn('intro', 'Hi {{$name}}!')
            ->make(true);
    }

    public function datatableEventLeaveApplications()
    {
        $companyId = request()->query('company_id');
        $approvalStatus = request()->query('status');

        $leaveApplications = LeaveApplication::query();

        if (isset($companyId) && !empty($companyId)) {
            $leaveApplications = $leaveApplications->whereHas('employee.office.division.company', function ($q) use ($companyId) {
                $q->where('id', $companyId);
            });
        }

        if (isset($approvalStatus) && !empty($approvalStatus)) {
            $leaveApplications = $leaveApplications->where('approval_status', $approvalStatus);
        }

        $leaveApplications = $leaveApplications->with(['employee' => function ($q) {
            $q->with(['activeCareer.jobTitle', 'office.division.company']);
        }, 'category'])->where('leave_category_id', 8)->orderBy('date', 'DESC')->select('leave_applications.*');

        return DataTables::eloquent($leaveApplications)
            ->addColumn('employee', function (LeaveApplication $leaveApplication) {
                if (isset($leaveApplication->employee)) {
                    return $this->employeeColumn($leaveApplication->employee);
                }
                return 'NAMA_PEGAWAI';
            })
            ->addColumn('leave_category_name', function (LeaveApplication $leaveApplication) {
                return '<span class="badge badge-light-primary">' . $leaveApplication->category->name ?? "" . '</span>';
            })
            ->addColumn('formatted_date', function (LeaveApplication $leaveApplication) {
                return Carbon::parse($leaveApplication->date)->format('d/m/Y');
            })
            ->addColumn('formatted_application_dates', function (LeaveApplication $leaveApplication) {
                $applicationDates = explode(',', $leaveApplication->application_dates);
                $applicationStartDate = min($applicationDates) ?? '';
                $applicationEndDate = max($applicationDates) ?? '';
                $dates = '';
                if (count($applicationDates) > 1) {
                    $dates = Carbon::parse($applicationStartDate)->isoFormat('LL') . ' - ' . Carbon::parse($applicationEndDate)->isoFormat('LL');
                } else {
                    $dates = Carbon::parse($applicationStartDate)->isoFormat('LL');
                }

                $formattedApplicationDates = collect($applicationDates)->map(function ($applicationDate) {
                    return Carbon::parse($applicationDate)->format('d/m/Y');
                })->sort()->join(', ');
                return '
                <span class="badge badge-secondary cursor-pointer" data-bs-toggle="popover" title="Detail Tanggal" data-bs-content="' . $formattedApplicationDates . '">' . $dates . '</span>
              ';
            })
            ->addColumn('status', function (LeaveApplication $leaveApplication) {
                return ' <span class="badge badge-' . $this->statusColor($leaveApplication->approval_status) . '  text-uppercase">
                ' . $leaveApplication->approval_status . '
            </span>';
            })
            ->addColumn('action', function (LeaveApplication $leaveApplication) {
                $action = '<div class="d-flex justify-content-end">';
                //                 <div class="btn-group" role="group" aria-label="Basic example">
                //   <button type="button" class="btn btn-primary">Left</button>
                //   <button type="button" class="btn btn-primary">Middle</button>
                //   <button type="button" class="btn btn-primary">Right</button>
                // </div>
                $action = '<div class="btn-group mt-2" role="group" aria-label="Basic example">';
                if (request()->user()->can('update', LeaveApplication::class)) {
                    if ($leaveApplication->approval_status == 'pending') {
                        $action .= '<a href="/time-offs/' . $leaveApplication->id . '/edit?type=sakit" class="btn btn-sm btn-icon btn-secondary">
                        <span class="svg-icon svg-icon-5 m-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                        </svg>
                        </span></a>';
                    }
                }
                if (request()->user()->can('delete', LeaveApplication::class)) {
                    $action .= '<button type="button" class="btn btn-sm btn-icon btn-secondary btn-delete"  data-id="' . $leaveApplication->id . '" data-status="' . $leaveApplication->approval_status . '">
                    <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                    <span class="svg-icon svg-icon-5 m-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black" />
                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black" />
                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </button>';
                }
                $action .= '</div>';

                $action .= '<div class="btn-group mt-2" role="group" aria-label="Basic example">';
                if (request()->user()->can('update', LeaveApplication::class)) {
                    if ($leaveApplication->approval_status == 'pending') {
                        $action .= '<button class="btn btn-sm btn-icon btn-secondary btn-reject" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Tolak" data-id="' . $leaveApplication->id . '">
                        <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                        <span class="svg-icon svg-icon-5 m-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </button>
                    <button class="btn btn-sm btn-icon btn-secondary btn-approve" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Setujui" data-id="' . $leaveApplication->id . '">
                        <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                        <span class="svg-icon svg-icon-5 m-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z" fill="currentColor" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </button>';
                    }
                }
                $action .= '</div>';

                $action .= '</div>';

                return $action;
            })
            ->rawColumns(['employee', 'leave_category_name', 'formatted_application_dates', 'formatted_attachment', 'status', 'action'])
            // ->addColumn('intro', 'Hi {{$name}}!')
            ->make(true);
    }
}
