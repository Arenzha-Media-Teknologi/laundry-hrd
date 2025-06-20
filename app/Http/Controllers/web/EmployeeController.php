<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Career;
use App\Models\Company;
use App\Models\Counter;
use App\Models\Credential;
use App\Models\CredentialGroup;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Division;
use App\Models\DriversLicense;
use App\Models\Employee;
use App\Models\EmployeeBpjs;
use App\Models\EventCalendar;
use App\Models\JobTitle;
use App\Models\Leave;
use App\Models\LeaveApplication;
use App\Models\Loan;
use App\Models\Office;
use App\Models\PermissionApplication;
use App\Models\PrivateInsurance;
use App\Models\SalaryComponent;
use App\Models\SickApplication;
use App\Models\WorkingPattern;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
// use Intervention\Image\Image;
use Intervention\Image\Facades\Image;
use Kreait\Laravel\Firebase\Facades\Firebase;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $employees = Employee::query()->whereHas('office.division', function ($q) {
        //     $q->where('id', 12);
        // })->get();
        // return json_encode($employees->pluck('id')->all());
        if (request()->user()->cannot('view', Employee::class)) {
            abort(403);
        }

        $companyId = request()->query('company_id');
        $divisionId = request()->query('division_id');
        $active = request()->query('active') ?? 1;
        $searchKeyword = request()->query('search');

        $permissions = json_decode(Auth::user()->group->permissions ?? "[]", true);

        $isAerplusOnly = false;
        if (in_array('view_employee_aerplus', $permissions)) {
            $isAerplusOnly = true;
        }

        // return $permissions;

        if ($isAerplusOnly) {
            $companyId = 6;
            $divisionId = 12;
        }

        $filteredCompanyName = "Semua Perusahaan";
        $filteredDivisionName = "Semua Divisi";
        $filteredActive = "Semua Status";

        $employeesQuery = Employee::with(['activeCareer.jobTitle', 'office' => function ($q) {
            $q->with(['division' => function ($q2) {
                $q2->with(['company']);
            }]);
        }]);

        if (isset($companyId) && $companyId !== "") {
            $employeesQuery->whereHas('office.division.company', function ($q) use ($companyId) {
                $q->where('id', $companyId);
            });
            $filteredCompanyName = Company::find($companyId)->name ?? "Semua Perusahaan";
        }

        if (isset($divisionId) && $divisionId !== "") {
            $employeesQuery->whereHas('office.division', function ($q) use ($divisionId) {
                $q->where('id', $divisionId);
            });
            $filteredDivisionName = Division::find($divisionId)->name ?? "Semua Divisi";
        }

        if (isset($active) && $active !== "") {
            $employeesQuery->where('active', $active);
            $filteredActive = $active == 1 ? 'Aktif' : 'Nonaktif';
            // $employeesQuery->orWhere('number', 'LIKE', '%' . $searchKeyword . '%');
        }

        if (isset($searchKeyword)) {
            $employeesQuery->where('name', 'LIKE', '%' . $searchKeyword . '%');
            // $employeesQuery->orWhere('number', 'LIKE', '%' . $searchKeyword . '%');
        }

        $employees = $employeesQuery->paginate(10)->withQueryString();

        $activeEmployeesCount = Employee::query()->where('active', true)->get()->count();
        $inactiveEmployeesCount = Employee::query()->where('active', false)->get()->count();
        $employeeStatistics = [
            'active_employees_count' => $activeEmployeesCount,
            'inactive_employees_count' => $inactiveEmployeesCount,
        ];

        $companies = Company::with(['divisions' => function ($divisionQuery) {
            $divisionQuery->with(['offices' => function ($officeQuery) {
                $officeQuery->withCount(['employees as active_employees_count' => function ($query) {
                    $query->where('active', 1);
                }, 'employees as inactive_employees_count' => function ($query) {
                    $query->where('active', '!=', 1);
                }]);
            }]);
        }])
            ->get()
            ->each(function ($company) {
                $divisions = collect($company->divisions)->each(function ($division) {
                    $activeEmployeesCount = collect($division->offices)->sum(function ($office) {
                        return $office->active_employees_count ?? 0;
                    });
                    $inactiveEmployeesCount = collect($division->offices)->sum(function ($office) {
                        return $office->inactive_employees_count ?? 0;
                    });

                    $division->active_employees_count = $activeEmployeesCount;
                    $division->inactive_employees_count = $inactiveEmployeesCount;
                });

                $activeEmployeesCount = collect($divisions)->sum(function ($division) {
                    return $division->active_employees_count ?? 0;
                });
                $inactiveEmployeesCount = collect($divisions)->sum(function ($division) {
                    return $division->inactive_employees_count ?? 0;
                });

                $company->active_employees_count = $activeEmployeesCount;
                $company->inactive_employees_count = $inactiveEmployeesCount;
            });


        // return $companies;
        $allCompanies = Company::all();
        $allDivisions = Division::all();
        if ($isAerplusOnly) {
            $allCompanies = Company::query()->where('id', 6)->get();
            $allDivisions = Division::query()->where('id', 12)->get();
        }

        // return $employees;
        return view('employees.index', [
            'employees' => $employees,
            'statistics' => $employeeStatistics,
            'companies' => $companies,
            'all_companies' => $allCompanies,
            'all_divisions' => $allDivisions,
            'filtered_company_name' => $filteredCompanyName,
            'filtered_division_name' => $filteredDivisionName,
            'filtered_active' => $filteredActive,
            'filter' => [
                'active' => $active,
            ],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $companies = Company::all();
        $divisions = Division::all();
        $offices = Office::all();
        $departments = Department::all();
        $designations = Designation::all();
        $jobTitles = JobTitle::all();

        return view('employees.create', [
            'companies' => $companies,
            'divisions' => $divisions,
            'offices' => $offices,
            'departments' => $departments,
            'designations' => $designations,
            'jobTitles' => $jobTitles,
        ]);
    }

    /**
     * Show the form for creating a new resource (version 2).
     *
     * @return \Illuminate\Http\Response
     */
    public function createV2()
    {

        if (request()->user()->cannot('create', Employee::class)) {
            abort(403);
        }

        $companies = Company::all();
        $divisions = Division::all();
        $offices = Office::all();
        $departments = Department::all();
        $designations = Designation::all();
        $jobTitles = JobTitle::all();
        $privateInsurances = PrivateInsurance::all();
        $employees = Employee::where('active', 1)->get();

        return view('employees.v2.create', [
            'companies' => $companies,
            'divisions' => $divisions,
            'offices' => $offices,
            'departments' => $departments,
            'designations' => $designations,
            'jobTitles' => $jobTitles,
            'private_insurances' => $privateInsurances,
            'employees' => $employees,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmployeeRequest $request)
    {
        if (request()->user()->cannot('create', Employee::class)) {
            abort(403);
        }

        $currentYear = date('Y');
        // $currentYear = 2023;

        DB::beginTransaction();
        try {


            $currentDateTime = date('Y-m-d H:m:s');

            // Validation
            $validated = $request->validated();

            $validated = $request->safe()->except(['job_title_id', 'employment_status', 'company_id', 'division_id']);

            // Employee Number
            // Check counter for employees table. If there isn't, then create one
            $counter = Counter::query()->firstOrCreate([
                'table' => 'employees'
            ]);

            // Check if counter exist
            if ($counter == null) {
                // Return error response
                return response()->json([
                    'message' => 'Cannot find the counter for table employees',
                ], 500);
            }

            // Get counter value
            $counterValue = $counter?->value;

            // Check current year employee
            $currentYearEmployee = Employee::query()->whereYear('created_at', $currentYear)->first();
            if ($currentYearEmployee == null) {
                $counterValue = 1;
            }

            // Check employee number exist
            $employeeNumber = $currentYear . sprintf('%04d', $counterValue);
            $employeeWithNumber = Employee::query()->where('number', $employeeNumber)->first();
            if ($employeeWithNumber !== null) {
                // Return error response
                return response()->json([
                    'message' => 'Internal Error: Nomor pegawai sudah digunakan. Hubungi pengembang aplikasi',
                ], 500);
            }

            // Create new employee number
            // $partialEmployeeNumber = $currentYear . sprintf('%04d', $counterValue);

            // Photo
            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');

                // Compress Image
                $imageWidth = Image::make($file->getRealPath())->width();
                $ratio = 50 / 100;
                $newWidth = floor($imageWidth * $ratio);
                $resizedImage = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->stream();

                // Uploading Image
                $name = time() . '_' . $file->getClientOriginalName();
                $filePath = 'employees/photos/' . $name;
                $path = Storage::disk('s3')->put($filePath, $resizedImage, 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            // Add custom field
            $validated['active_at'] = $currentDateTime;
            $validated['photo'] = $urlPath;
            $validated['number'] = $employeeNumber;

            // Store employee
            $employee = Employee::create($validated);

            // Store career
            $employmentStatus = $request->employment_status;
            $jobTitleId = $request->job_title_id;
            $startWorkDate = $request->start_work_date;

            Career::create([
                'status' => $employmentStatus,
                'effective_date' => $startWorkDate,
                'employee_id' => $employee->id,
                'job_title_id' => $jobTitleId,
            ]);

            // Save new employees counter value
            $counter->value = $counterValue + 1;
            $counter->save();

            // Commit transaction
            DB::commit();

            // Return success response
            return response()->json([
                'message' => 'Pegawai telah tersimpan',
                'data' => $employee,
            ]);
        } catch (Exception $e) {
            // Rollback transaction
            DB::rollBack();
            // Return error response
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeV2(StoreEmployeeRequest $request)
    {

        if (request()->user()->cannot('create', Employee::class)) {
            abort(403);
        }

        $currentYear = date('Y');
        // $currentYear = 2023;

        DB::beginTransaction();
        try {
            $currentDateTime = date('Y-m-d H:m:s');

            // Validation
            $validated = $request->validated();

            $validated = $request->safe()->except(['job_title_id', 'employment_status', 'company_id', 'division_id']);

            // // Employee Number
            // // Check counter for employees table. If there isn't, then create one
            // $counter = Counter::query()->firstOrCreate([
            //     'table' => 'employees'
            // ]);

            // // Check if counter exist
            // if ($counter == null) {
            //     // Return error response
            //     return response()->json([
            //         'message' => 'Cannot find the counter for table employees',
            //     ], 500);
            // }

            // // Get counter value
            // $counterValue = $counter?->value;

            // // Check current year employee
            // $currentYearEmployee = Employee::query()->whereYear('created_at', $currentYear)->first();
            // if ($currentYearEmployee == null) {
            //     $counterValue = 1;
            // }

            // Check employee number exist
            // !----
            // $employeeNumber = $currentYear . sprintf('%04d', 1);
            $employeeNumber = date('y') . sprintf('%04d', 1);
            // !----
            // $employeeWithNumber = Employee::query()->where('number', $employeeNumber)->first();
            // if ($employeeWithNumber !== null) {
            //     // Return error response
            //     return response()->json([
            //         'message' => 'Internal Error: Nomor pegawai sudah digunakan',
            //     ], 500);
            // }

            // Create new employee number
            // $partialEmployeeNumber = $currentYear . sprintf('%04d', $counterValue);

            // Photo
            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('photo')) {
                $urlPath = $this->handleUploadImage($request, 'photo', 'employees/photos/');

                // $file = $request->file('photo');

                // // Compress Image
                // $imageWidth = Image::make($file->getRealPath())->width();
                // $ratio = 50 / 100;
                // $newWidth = floor($imageWidth * $ratio);
                // $resizedImage = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
                //     $constraint->aspectRatio();
                // })->stream();

                // // Uploading Image
                // $name = time() . '_' . $file->getClientOriginalName();
                // $filePath = 'employees/photos/' . $name;
                // $path = Storage::disk('s3')->put($filePath, $resizedImage, 'public');
                // $urlPath = Storage::disk('s3')->url($filePath);
            }

            $identityImageUrlPath = null;
            if ($request->hasFile('identity_image')) {
                $identityImageUrlPath = $this->handleUploadImage($request, 'identity_image', 'employees/ktp/');
            }

            $npwpImageUrlPath = null;
            if ($request->hasFile('npwp_image')) {
                $npwpImageUrlPath = $this->handleUploadImage($request, 'npwp_image', 'employees/npwp/');
            }

            // Add custom field
            $validated['active_at'] = $currentDateTime;
            $validated['photo'] = $urlPath;
            $validated['identity_image'] = $identityImageUrlPath;
            $validated['npwp_image'] = $npwpImageUrlPath;
            $validated['number'] = $employeeNumber;

            // Store employee
            $employee = Employee::create($validated);

            // Store career
            $employmentStatus = $request->employment_status;
            $jobTitleId = $request->job_title_id;
            $startWorkDate = $request->start_work_date;

            Career::create([
                'status' => $employmentStatus,
                'effective_date' => $startWorkDate,
                'employee_id' => $employee->id,
                'job_title_id' => $jobTitleId,
            ]);


            // Store driver's licenses
            $driversLicenses = json_decode($request->drivers_licenses, true);

            $driversLicensesData = [];

            collect($driversLicenses)->each(function ($license, $index) use ($request, &$driversLicensesData, $employee) {
                $filePath = null;
                $urlPath = null;
                $imageKey = 'drivers_license_image_' . $index;
                if ($request->hasFile($imageKey)) {
                    $urlPath = $this->handleUploadImage($request, $imageKey, 'employees/drivers_licenses/');
                }

                array_push($driversLicensesData, [
                    'type' => $license['type'],
                    'number' => $license['number'],
                    'expire_date' => $license['expireDate'],
                    'image' => $urlPath,
                    'employee_id' => $employee->id,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    // 'has_image' => $request->hasFile('drivers_license_image_' . $index),
                ]);
            });


            if (count($driversLicensesData) > 0) {
                DB::table('drivers_licenses')->insert($driversLicensesData);
            }

            // Store component
            $salaryComponents = SalaryComponent::all();
            $employeeSalaryComponents = [];

            $salaryComponentTypes = ['gaji_pokok', 'uang_harian', 'tunjangan', 'lembur', 'uang_makan', 'tunjangan_harian'];
            foreach ($salaryComponentTypes as $salaryComponentType) {
                $salaryComponent = collect($salaryComponents)->where('salary_type', $salaryComponentType)->first();
                $salaryAmount = $request->{'salary_' . $salaryComponentType};

                // array_push($employeeSalaryComponents, $salaryComponent);
                if (isset($salaryComponent)) {
                    array_push($employeeSalaryComponents, [
                        'employee_id' => $employee->id,
                        'salary_component_id' => $salaryComponent->id,
                        'amount' => $salaryAmount,
                        'coefficient' => 2,
                        'effective_date' => date('Y-01-01'),
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]);
                }
            }

            DB::table('employee_salary_component')->insert($employeeSalaryComponents);

            // Insurances
            $insurances = json_decode($request->insurances, true);
            $bpjsInsurances = collect($insurances)->where('type', 'bpjs')->all();
            $privateInsurances = collect($insurances)->where('type', 'private')->all();

            $newEmployeeBpjs = new EmployeeBpjs();
            $bpjsMandiri = collect($bpjsInsurances)->where('id', 'bpjs_mandiri')->first();
            if (isset($bpjsMandiri)) {
                $filePath = null;
                $urlPath = null;
                $imageKey = 'bpjs_mandiri_image';
                if ($request->hasFile($imageKey)) {
                    $urlPath = $this->handleUploadImage($request, $imageKey, 'employees/bpjs_cards/');
                }

                $newEmployeeBpjs->mandiri_number = $bpjsMandiri['number'];
                $newEmployeeBpjs->mandiri_card_image = $urlPath;
            }

            $bpjsKetenagakerjaan = collect($bpjsInsurances)->where('id', 'bpjs_ketenagakerjaan')->first();
            if (isset($bpjsKetenagakerjaan)) {
                $filePath = null;
                $urlPath = null;
                $imageKey = 'bpjs_ketenagakerjaan_image';
                if ($request->hasFile($imageKey)) {
                    $urlPath = $this->handleUploadImage($request, $imageKey, 'employees/bpjs_cards/');
                }

                $newEmployeeBpjs->ketenagakerjaan_number = $bpjsKetenagakerjaan['number'];
                $newEmployeeBpjs->ketenagakerjaan_start_year = $bpjsKetenagakerjaan['startYear'];
                $newEmployeeBpjs->ketenagakerjaan_card_image = $urlPath;
            }

            if (isset($bpjsMandiri) || isset($bpjsKetenagakerjaan)) {
                $newEmployeeBpjs->employee_id = $employee->id;
                $newEmployeeBpjs->save();
            }

            if (count($privateInsurances) > 0) {
                $employeePrivateInsurances = [];

                collect($privateInsurances)->each(function ($insurance, $index) use ($request, &$employeePrivateInsurances, $employee) {
                    $filePath = null;
                    $urlPath = null;
                    $imageKey = 'private_insurance_image_' . $index;
                    if ($request->hasFile($imageKey)) {
                        $urlPath = $this->handleUploadImage($request, $imageKey, 'employees/private_insurance_cards/');
                    }

                    array_push($employeePrivateInsurances, [
                        'employee_id' => $employee->id,
                        'private_insurance_id' => $insurance['id'],
                        'number' => $insurance['number'],
                        'start_year' => $insurance['startYear'],
                        'card_image' => $urlPath,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                        // 'has_image' => $request->hasFile('drivers_license_image_' . $index),
                    ]);
                });


                if (count($employeePrivateInsurances) > 0) {
                    DB::table('employee_private_insurance')->insert($employeePrivateInsurances);
                }
            }

            // await firestore.collection('employee_locations').doc(id.toString()).set({
            //     employee_id: id,
            //     name,
            //     photo: null,
            //     latitude: 0,
            //     longitude: 0,
            //     address: null,
            //     is_tracked: false,
            //   });

            // STORE TO FIREBASE
            // $firestore = Firebase::firestore();
            // $database = $firestore->database();
            // $collectionReference = $database->collection('employee_locations');
            // $documentReference = $collectionReference->document($employee->id);
            // $documentReference->set([
            //     'employee_id' => $employee->id,
            //     'name' => $employee->name,
            //     'photo' => $employee->photo,
            //     'latitude' => 0,
            //     'longitude' => 0,
            //     'address' => null,
            //     'is_tracked' => false,
            // ]);
            // END: STORE TO FIREBASE

            // // Save new employees counter value
            // $counter->value = $counterValue + 1;
            // $counter->save();

            // Commit transaction
            DB::commit();

            // Return success response
            return response()->json([
                'message' => 'Pegawai telah tersimpan',
                'data' => $employee,
            ]);
        } catch (\Throwable $e) {
            // Rollback transaction
            DB::rollBack();
            // Return error response
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * handleUploadImage
     */
    private function handleUploadImage(Request $request, $imageKey, $dir)
    {
        $file = $request->file($imageKey);
        // Compress Image
        $imageWidth = Image::make($file->getRealPath())->width();
        $ratio = 50 / 100;
        $newWidth = floor($imageWidth * $ratio);
        $resizedImage = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
            $constraint->aspectRatio();
        })->stream();

        // Uploading Image
        $name = time() . '_' . $file->getClientOriginalName();
        $filePath = $dir . $name;
        $path = Storage::disk('s3')->put($filePath, $resizedImage, 'public');
        $urlPath = Storage::disk('s3')->url($filePath);

        return $urlPath;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (request()->user()->cannot('viewDetail', Employee::class)) {
            abort(403);
        }

        $employee = Employee::findOrFail($id);
        return view('employees._id.detail', [
            'employee' => $employee,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        if (request()->user()->cannot('viewDetail', Employee::class)) {
            abort(403);
        }

        $employee = Employee::with(['activeCareer', 'bankAccounts', 'activeWorkingPatterns', 'salaryComponents'])->findOrFail($id);

        // Have working patterns
        $haveWorkingPattern = false;
        if (count($employee->activeWorkingPatterns) > 0) {
            $haveWorkingPattern = true;
        }
        // if (is_array($employee->activeWorkingPatterns)) {
        // }

        // Have Salaries
        $haveSalaries = false;
        if (count($employee->salaryComponents) > 0) {
            $haveSalaries = true;
        }

        // Have Career
        $haveCareer = false;
        if ($employee->activeCareer !== null) {
            $haveCareer = true;
        }


        $employeeCompletion = [
            'working_pattern' => $haveWorkingPattern,
            'salary' => $haveSalaries,
            'career' => $haveCareer,
        ];

        // return $employeeCompletion;
        // return $employee->salaryComponents;

        $completionCount = collect($employeeCompletion)->count();
        $completedCount = collect($employeeCompletion)->flatten()->filter(fn($item) => $item)->count();
        $incompleteCount = $completionCount - $completedCount;
        $completionPercentage = round(($completedCount / $completionCount) * 100);
        // return collect($employeeCompletion)->flatten()->all();

        return view('employees._id.detail', [
            'employee' => $employee,
            'completion' => [
                'items' => $employeeCompletion,
                'items_count' => $completionCount,
                'completed_count' => $completedCount,
                'incomplete_count' => $incompleteCount,
                'percentage' => $completionPercentage,
            ],
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detailV2($id)
    {
        if (request()->user()->cannot('viewDetail', Employee::class)) {
            abort(403);
        }

        $year = date('Y');
        $employee = Employee::with([
            'activeCareer.jobTitle.designation.department',
            'bankAccounts',
            'activeWorkingPatterns',
            'salaryComponents',
            'driversLicenses',
            'bpjs',
            'privateInsurances' => function ($q) use ($year) {
                $q->with(['values' => function ($q2) use ($year) {
                    $q2->where('year', $year);
                }]);
            },
            'bpjsValues' => function ($q) use ($year) {
                $q->where('year', $year)->orderBy('id', 'desc');
            },
        ])->findOrFail($id);

        // return $employee;

        // Have working patterns
        $haveWorkingPattern = false;
        if (count($employee->activeWorkingPatterns) > 0) {
            $haveWorkingPattern = true;
        }
        // if (is_array($employee->activeWorkingPatterns)) {
        // }

        // Have Salaries
        $haveSalaries = false;
        if (count($employee->salaryComponents) > 0) {
            $haveSalaries = true;
        }

        // Have Career
        $haveCareer = false;
        if ($employee->activeCareer !== null) {
            $haveCareer = true;
        }


        $employeeCompletion = [
            'working_pattern' => $haveWorkingPattern,
            'salary' => $haveSalaries,
            'career' => $haveCareer,
        ];

        // return $employeeCompletion;
        // return $employee->salaryComponents;

        $completionCount = collect($employeeCompletion)->count();
        $completedCount = collect($employeeCompletion)->flatten()->filter(fn($item) => $item)->count();
        $incompleteCount = $completionCount - $completedCount;
        $completionPercentage = round(($completedCount / $completionCount) * 100);
        // return collect($employeeCompletion)->flatten()->all();

        // Salary values
        $salaryTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'lembur'];
        $employeeSalaryValues = collect($salaryTypes)->mapWithKeys(function ($salaryType) use ($employee) {
            $component = collect($employee->salaryComponents)->where('salary_type', $salaryType)->first();
            $value = $component->pivot->amount ?? 0;
            return [
                $salaryType => $value,
            ];
        })->all();



        return view('employees._id.v2.detail', [
            'employee' => $employee,
            'salary_values' => $employeeSalaryValues,
            'completion' => [
                'items' => $employeeCompletion,
                'items_count' => $completionCount,
                'completed_count' => $completedCount,
                'incomplete_count' => $incompleteCount,
                'percentage' => $completionPercentage,
            ],
        ]);
    }

    /**
     * Display careers from specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function careers($id)
    {
        $employee = Employee::with(['careers' => function ($query) {
            $query->with(['jobTitle.designation.department'])->orderBy('effective_date', 'desc')->orderBy('created_at', 'desc');
        }, 'activeCareer'])->findOrFail($id);

        $departments = Department::with(['designations.jobTitles'])->get();
        $designations = Designation::with(['department'])->get();
        $jobTitles = JobTitle::with(['designation.department'])->get();
        $lastDateCareer = $employee?->activeCareer?->effective_date;

        return view('employees._id.careers', [
            'employee' => $employee,
            'departments' => $departments,
            'designations' => $designations,
            'jobTitles' => $jobTitles,
            'last_date_career' => $lastDateCareer,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = Employee::with(['office.division.company', 'salaryComponents'])->findOrFail($id);
        $companies = Company::all();
        $divisions = Division::all();
        $offices = Office::all();
        $departments = Department::all();
        $designations = Designation::all();
        $jobTitles = JobTitle::all();

        $oldEmployeeNumber = $employee?->office?->division?->company?->initial . '-' . $employee?->office?->division?->initial . '-' . $employee->number;

        return view('employees._id.edit', [
            'employee' => $employee,
            'old_employee_number' => $oldEmployeeNumber,
            'companies' => $companies,
            'divisions' => $divisions,
            'offices' => $offices,
            'departments' => $departments,
            'designations' => $designations,
            'jobTitles' => $jobTitles,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editV2($id)
    {
        $employee = Employee::with(['office.division.company', 'activeCareer.jobTitle.designation.department', 'salaryComponents', 'driversLicenses', 'privateInsurances', 'bpjs'])->findOrFail($id);
        $companies = Company::all();
        $divisions = Division::all();
        $offices = Office::all();
        $departments = Department::all();
        $designations = Designation::all();
        $jobTitles = JobTitle::all();
        $privateInsurances = PrivateInsurance::all();

        // Salary values
        $salaryTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'lembur', 'uang_makan', 'tunjangan_harian'];
        $employeeSalaryValues = collect($salaryTypes)->mapWithKeys(function ($salaryType) use ($employee) {
            $component = collect($employee->salaryComponents)->where('salary_type', $salaryType)->first();
            $value = $component->pivot->amount ?? 0;
            return [
                $salaryType => $value,
            ];
        })->all();

        // Drivers licenses
        $employeeDriversLicenses = collect($employee->driversLicenses)->map(function ($license) {
            return [
                'image' => $license->image,
                'type' => $license->type,
                'number' => $license->number,
                'expireDate' => $license->expire_date,
                'previewImage' => $license->image,
            ];
        })->all();

        // BPJS
        $insurances = [];
        if (isset($employee->bpjs)) {
            if (isset($employee->bpjs->ketenagakerjaan_number)) {
                array_push($insurances, [
                    'image' => $employee->bpjs->ketenagakerjaan_card_image ?? null,
                    'number' => $employee->bpjs->ketenagakerjaan_number ?? '',
                    'startYear' => $employee->bpjs->ketenagakerjaan_start_year ?? '',
                    'previewImage' => $employee->bpjs->ketenagakerjaan_card_image ?? null,
                    'id' => 'bpjs_ketenagakerjaan',
                    'type' => 'bpjs',
                    'name' => 'BPJS Ketenagakerjaan',
                ]);
            }

            if (isset($employee->bpjs->mandiri_number)) {
                array_push($insurances, [
                    'image' => $employee->bpjs->mandiri_card_image ?? null,
                    'number' => $employee->bpjs->mandiri_number ?? '',
                    'startYear' => $employee->bpjs->mandiri_start_year ?? '',
                    'previewImage' => $employee->bpjs->mandiri_card_image ?? null,
                    'id' => 'bpjs_mandiri',
                    'type' => 'bpjs',
                    'name' => 'BPJS Mandiri',
                ]);
            }
        }

        // Private Insurances
        collect($employee->privateInsurances)->each(function ($privateInsurance) use (&$insurances) {
            array_push($insurances, [
                'image' => $privateInsurance->pivot->card_image ?? null,
                'number' => $privateInsurance->pivot->number ?? '',
                'startYear' => $privateInsurance->pivot->start_year ?? '',
                'previewImage' => $privateInsurance->pivot->card_image ?? null,
                'id' => $privateInsurance->id ?? null,
                'type' => 'private',
                'name' => $privateInsurance->name,
            ]);
        });

        // return $insurances;

        $oldEmployeeNumber = $employee?->office?->division?->company?->initial . '-' . $employee?->office?->division?->initial . '-' . $employee->number;

        $employees = Employee::where('active', 1)->get();

        return view('employees._id.v2.edit', [
            'employee' => $employee,
            'old_employee_number' => $oldEmployeeNumber,
            'companies' => $companies,
            'divisions' => $divisions,
            'offices' => $offices,
            'departments' => $departments,
            'designations' => $designations,
            'jobTitles' => $jobTitles,
            'private_insurances' => $privateInsurances,
            'salary_values' => $employeeSalaryValues,
            'drivers_licenses' => $employeeDriversLicenses,
            'employee_insurances' => $insurances,
            'employees' => $employees,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        $currentYear = date('Y');
        // $currentYear = 2023;

        DB::beginTransaction();
        try {
            $currentDateTime = date('Y-m-d H:m:s');

            // Validation
            $validated = $request->validated();

            $validated = $request->safe()->except(['job_title_id', 'employment_status', 'company_id', 'division_id']);

            // Employee Number
            // Check counter for employees table. If there isn't, then create one
            $counter = Counter::query()->firstOrCreate([
                'table' => 'employees'
            ]);

            // Check if counter exist
            if ($counter == null) {
                // Return error response
                return response()->json([
                    'message' => 'Cannot find the counter for table employees',
                ], 500);
            }

            // Get counter value
            $counterValue = $counter?->value;

            // Check current year employee
            $currentYearEmployee = Employee::query()->whereYear('created_at', $currentYear)->whereNotIn('id', [$id])->first();
            if ($currentYearEmployee == null) {
                $counterValue = 1;
            }

            // Check employee number exist
            $employeeNumber = substr($currentYear, 2) . sprintf('%04d', $counterValue);
            $employeeWithNumber = Employee::query()->where('number', $employeeNumber)->whereNotIn('id', [$id])->first();
            if ($employeeWithNumber !== null) {
                // Return error response
                return response()->json([
                    'message' => 'Internal Error: Nomor pegawai sudah digunakan. Laporkan kepada pengembang aplikasi',
                ], 500);
            }

            // Create new employee number
            // $partialEmployeeNumber = $currentYear . sprintf('%04d', $counterValue);

            // Photo
            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');

                // Compress Image
                $imageWidth = Image::make($file->getRealPath())->width();
                $ratio = 50 / 100;
                $newWidth = floor($imageWidth * $ratio);
                $resizedImage = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->stream();

                // Uploading Image
                $name = time() . '_' . $file->getClientOriginalName();
                $filePath = 'employees/photos/' . $name;
                $path = Storage::disk('s3')->put($filePath, $resizedImage, 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            } else {
                $urlPath = $request->photo;
            }

            // Add custom field
            $validated['active_at'] = $currentDateTime;
            $validated['photo'] = $urlPath;
            if ($request->division_id !== $request->old_division_id) {
                $validated['number'] = $employeeNumber;
            }

            // Store employee
            $employee = Employee::where(['id' => $id])->update($validated);

            // Store career
            // $employmentStatus = $request->employment_status;
            // $jobTitleId = $request->job_title_id;
            // $startWorkDate = $request->start_work_date;

            // Career::create([
            //     'status' => $employmentStatus,
            //     'effective_date' => $startWorkDate,
            //     'employee_id' => $employee->id,
            //     'job_title_id' => $jobTitleId,
            // ]);

            // Save new employees counter value
            if ($request->division_id !== $request->old_division_id) {
                $counter->value = $counterValue + 1;
                $counter->save();
            }

            // Commit transaction
            DB::commit();

            // Return success response
            return response()->json([
                'message' => 'Pegawai telah tersimpan',
                'data' => $employee,
            ]);
        } catch (Exception $e) {
            // Rollback transaction
            DB::rollBack();
            // Return error response
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage V2.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateV2(UpdateEmployeeRequest $request, $id)
    {
        $currentYear = date('Y');
        // $currentYear = 2023;
        // return $currentYear;

        DB::beginTransaction();
        try {
            $currentDateTime = date('Y-m-d H:m:s');

            // Validation
            $validated = $request->validated();

            $validated = $request->safe()->except(['job_title_id', 'employment_status', 'company_id', 'division_id']);

            // Check employee number exist
            $employeeNumber = $currentYear . sprintf('%04d', 1);

            // Photo
            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('photo')) {
                $urlPath = $this->handleUploadImage($request, 'photo', 'employees/photos/');
            } else {
                // $urlPath = $request->photo;
                $requestImage = $request->photo;
                if (!isset($requestImage) || $requestImage == 'null') {
                    $requestImage = null;
                }
                $urlPath = $requestImage;
            }

            $identityImageUrlPath = null;
            if ($request->hasFile('identity_image')) {
                $identityImageUrlPath = $this->handleUploadImage($request, 'identity_image', 'employees/ktp/');
            } else {
                $requestImage = $request->identity_image;
                if (!isset($requestImage) || $requestImage == 'null') {
                    $requestImage = null;
                }
                $identityImageUrlPath = $requestImage;
            }

            $npwpImageUrlPath = null;
            if ($request->hasFile('npwp_image')) {
                $npwpImageUrlPath = $this->handleUploadImage($request, 'npwp_image', 'employees/npwp/');
            } else {
                // $npwpImageUrlPath = $request->npwp_image;
                $requestImage = $request->npwp_image;
                if (!isset($requestImage) || $requestImage == 'null') {
                    $requestImage = null;
                }
                $npwpImageUrlPath = $requestImage;
            }

            // Add custom field
            // $validated['active_at'] = $currentDateTime;
            $validated['photo'] = $urlPath;
            $validated['identity_image'] = $identityImageUrlPath;
            $validated['npwp_image'] = $npwpImageUrlPath;
            $validated['number'] = $employeeNumber;

            // Store employee
            // $employee = Employee::create($validated);
            $employee = Employee::where(['id' => $id])->update($validated);

            // Store career
            $employmentStatus = $request->employment_status;
            $jobTitleId = $request->job_title_id;
            $startWorkDate = $request->start_work_date;

            $activeCareer = Career::query()
                ->where('employee_id', $id)
                ->where('active', 1)
                ->orderBy('id', 'desc')
                ->first();

            if (isset($activeCareer) && isset($activeCareer->id)) {
                Career::where(['id' => $activeCareer->id])->update([
                    'status' => $employmentStatus,
                    'effective_date' => $startWorkDate,
                    'job_title_id' => $jobTitleId,
                ]);
            } else {
                Career::create([
                    'status' => $employmentStatus,
                    'effective_date' => $startWorkDate,
                    'employee_id' => $id,
                    'job_title_id' => $jobTitleId,
                ]);
            }


            // Store driver's licenses
            $driversLicenses = json_decode($request->drivers_licenses, true);

            $driversLicensesData = [];

            collect($driversLicenses)->each(function ($license, $index) use ($request, &$driversLicensesData, $employee, $id) {
                $filePath = null;
                $urlPath = null;
                $imageKey = 'drivers_license_image_' . $index;
                if ($request->hasFile($imageKey)) {
                    $urlPath = $this->handleUploadImage($request, $imageKey, 'employees/drivers_licenses/');
                } else {
                    $urlPath = $license['image'];
                }

                array_push($driversLicensesData, [
                    'type' => $license['type'],
                    'number' => $license['number'],
                    'expire_date' => $license['expireDate'],
                    'image' => $urlPath,
                    'employee_id' => $id,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]);
            });


            if (count($driversLicensesData) > 0) {
                DB::table('drivers_licenses')->where('employee_id', $id)->delete();
                DB::table('drivers_licenses')->insert($driversLicensesData);
            }

            // Store component
            $salaryComponents = SalaryComponent::all();
            $employeeSalaryComponents = [];
            $coreSalaryComponentsIds = [];

            $salaryComponentTypes = ['gaji_pokok', 'uang_harian', 'tunjangan', 'lembur', 'uang_makan', 'tunjangan_harian'];
            foreach ($salaryComponentTypes as $salaryComponentType) {
                $salaryComponent = collect($salaryComponents)->where('salary_type', $salaryComponentType)->first();
                $salaryAmount = $request->{'salary_' . $salaryComponentType};

                if (isset($salaryComponent)) {
                    array_push($coreSalaryComponentsIds, $salaryComponent->id);
                    array_push($employeeSalaryComponents, [
                        'employee_id' => $id,
                        'salary_component_id' => $salaryComponent->id,
                        'amount' => $salaryAmount,
                        'coefficient' => 2,
                        'effective_date' => date('Y-01-01'),
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]);
                }
            }

            if (count($employeeSalaryComponents) > 0) {
                DB::table('employee_salary_component')->where('employee_id', $id)->whereIn('salary_component_id', $coreSalaryComponentsIds)->delete();
                DB::table('employee_salary_component')->insert($employeeSalaryComponents);
            }

            // Insurances
            $insurances = json_decode($request->insurances, true);
            $bpjsInsurances = collect($insurances)->where('type', 'bpjs')->all();
            $privateInsurances = collect($insurances)->where('type', 'private')->all();

            $newEmployeeBpjs = EmployeeBpjs::query()->where('employee_id', $id)->first();
            $bpjsMandiri = collect($bpjsInsurances)->where('id', 'bpjs_mandiri')->first();
            if (isset($bpjsMandiri)) {
                $filePath = null;
                $urlPath = null;
                $imageKey = 'bpjs_mandiri_image';
                if ($request->hasFile($imageKey)) {
                    $urlPath = $this->handleUploadImage($request, $imageKey, 'employees/bpjs_cards/');
                } else {
                    $urlPath = $bpjsMandiri['image'] ?? null;
                }

                $newEmployeeBpjs->mandiri_number = $bpjsMandiri['number'];
                $newEmployeeBpjs->mandiri_card_image = $urlPath;
            }

            $bpjsKetenagakerjaan = collect($bpjsInsurances)->where('id', 'bpjs_ketenagakerjaan')->first();
            if (isset($bpjsKetenagakerjaan)) {
                $filePath = null;
                $urlPath = null;
                $imageKey = 'bpjs_ketenagakerjaan_image';
                if ($request->hasFile($imageKey)) {
                    $urlPath = $this->handleUploadImage($request, $imageKey, 'employees/bpjs_cards/');
                } else {
                    $urlPath = $bpjsKetenagakerjaan['image'] ?? null;
                }

                $newEmployeeBpjs->ketenagakerjaan_number = $bpjsKetenagakerjaan['number'];
                $newEmployeeBpjs->ketenagakerjaan_start_year = $bpjsKetenagakerjaan['startYear'];
                $newEmployeeBpjs->ketenagakerjaan_card_image = $urlPath;
            }

            if (isset($bpjsMandiri) || isset($bpjsKetenagakerjaan)) {
                $newEmployeeBpjs->employee_id = $id;
                $newEmployeeBpjs->save();
            }

            if (count($privateInsurances) > 0) {
                $employeePrivateInsurances = [];

                collect($privateInsurances)->each(function ($insurance, $index) use ($request, &$employeePrivateInsurances, $employee, $id) {
                    $filePath = null;
                    $urlPath = null;
                    $imageKey = 'private_insurance_image_' . $index;
                    if ($request->hasFile($imageKey)) {
                        $urlPath = $this->handleUploadImage($request, $imageKey, 'employees/private_insurance_cards/');
                    } else {
                        $urlPath = $insurance['image'];
                    }

                    array_push($employeePrivateInsurances, [
                        'employee_id' => $id,
                        'private_insurance_id' => $insurance['id'],
                        'number' => $insurance['number'],
                        'start_year' => $insurance['startYear'],
                        'card_image' => $urlPath,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]);
                });


                if (count($employeePrivateInsurances) > 0) {
                    DB::table('employee_private_insurance')->where('employee_id', $id)->delete();
                    DB::table('employee_private_insurance')->insert($employeePrivateInsurances);
                }
            }

            // UPDATE TO FIREBASE
            // $updatedEmployee = Employee::find($id);
            // $firestore = Firebase::firestore();
            // $database = $firestore->database();
            // $collectionReference = $database->collection('employee_locations');
            // $documentReference = $collectionReference->document($id);
            // $documentReference->update([
            //     'employee_id' => $id,
            //     'name' => $updatedEmployee->name,
            //     'photo' => $updatedEmployee->photo,
            // ]);
            // END: UPDATE TO FIREBASE

            // Commit transaction
            DB::commit();

            // Return success response
            return response()->json([
                'message' => 'Pegawai telah tersimpan',
                'data' => $employee,
            ]);
        } catch (\Throwable $e) {
            // Rollback transaction
            DB::rollBack();
            // Return error response
            return response()->json([
                'message' => $e->getMessage(),
                // 'file' => $e->getFile(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::find($id);
            $employee->delete();

            return response()->json([
                'message' => 'Pegawai berhasil dihapus',
                'data' => $employee,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the employee NPWP.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateNpwp(Request $request, $id)
    {
        try {
            $employee = Employee::find($id);
            $employee->npwp_number = $request->npwp_number;
            $employee->npwp_effective_date = $request->npwp_effective_date;
            $employee->npwp_status = $request->npwp_status;
            $employee->save();

            return response()->json([
                'message' => 'Perubahan berhasil disimpan',
                'data' => $employee,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource setting.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setting($id)
    {
        $employee = Employee::with(['activeCareer', 'bankAccounts', 'activeWorkingPatterns', 'credential'])->findOrFail($id);
        $workingPatterns = WorkingPattern::with(['items'])->get();
        $credentialGroups = CredentialGroup::all();
        $aerplusOffices = Office::query()->where('division_id', 12)->get();

        $activeWorkingPattern = null;
        $activeWorkingPatternId = '';
        if (count($employee->activeWorkingPatterns) > 0) {
            $activeWorkingPattern = $employee->activeWorkingPatterns[0];
            if (isset($activeWorkingPattern['id'])) {
                $activeWorkingPatternId = $activeWorkingPattern['id'];
            }
        }

        return view('employees._id.setting', [
            'employee' => $employee,
            'working_patterns' => $workingPatterns,
            'credential_groups' => $credentialGroups,
            'active_working_pattern_id' => $activeWorkingPatternId,
            'aerplus_offices' => $aerplusOffices,
        ]);
    }

    /**
     * Update the employee working pattern.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateWorkingPattern(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $workingPatternId = $request->working_pattern_id;

            $employee = Employee::find($id);
            $employee->workingPatterns()->detach();
            $employee->workingPatterns()->attach($workingPatternId, ['active' => true]);

            DB::commit();

            return response()->json([
                'message' => 'Perubahan berhasil disimpan',
                'data' => $employee,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Perubahan gagal disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the employee payroll setting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePayrollSetting(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $employee = Employee::where('id', $id)->update($request->all());

            DB::commit();

            return response()->json([
                'message' => 'Perubahan berhasil disimpan',
                'data' => $employee,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Perubahan gagal disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the employee tracking setting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateIsTracked(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $employee = Employee::where('id', $id)->update($request->all());

            $isTracked = $request->is_tracked;
            // UPDATE TO FIREBASE
            $updatedEmployee = Employee::find($id);
            $firestore = Firebase::firestore();
            $database = $firestore->database();
            $collectionReference = $database->collection('employee_locations');
            $documentReference = $collectionReference->document($updatedEmployee->id);
            $documentReference->set([
                'employee_id' => $updatedEmployee->id,
                'name' => $updatedEmployee->name,
                'photo' => $updatedEmployee->photo,
                'latitude' => 0,
                'longitude' => 0,
                'address' => null,
                'is_tracked' => $isTracked,
            ]);
            // END: UPDATE TO FIREBASE

            DB::commit();

            return response()->json([
                'message' => 'Perubahan berhasil disimpan',
                'data' => $employee,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Perubahan gagal disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the employee account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAccount(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // $employee = Employee::where('id', $id)->update($request->all());
            $username = $request->username;
            $password = $request->password;
            $credentialGroupId = $request->credential_group_id;
            $mobile_access = $request->mobile_access;
            $mobile_access_type = $request->mobile_access_type;
            $isAerplusSupervisor = $request->is_aerplus_supervisor;
            $isAerplusTechnician = $request->is_aerplus_technician;
            $accessibleOffices = $request->accessible_offices;

            $validated = $request->validate([
                'username' => [
                    'required',
                    Rule::unique('credentials')->where(function ($query) use ($username) {
                        return $query->where('username', $username);
                    })->ignore($id, 'employee_id'),
                    'max:255'
                ],
                // 'password' => 'required|max:255',
                'credential_group_id' => 'required',
                'mobile_access' => 'required',
                'mobile_access_type' => [
                    Rule::in(['regular', 'admin'])
                ],
            ]);

            $modelData = [
                'username' => $username,
                // 'password' => Hash::make($password),
                'credential_group_id' => $credentialGroupId,
                'mobile_access' => $mobile_access,
                'mobile_access_type' => $mobile_access_type,
                'is_aerplus_supervisor' => $isAerplusSupervisor,
                'is_aerplus_technician' => $isAerplusTechnician,
                'accessible_offices' => $accessibleOffices,
            ];

            if (!empty($password)) {
                $modelData['password'] = Hash::make($password);
            }

            $credential = Credential::updateOrCreate(
                ['employee_id' => $id],
                $modelData,
            );

            DB::commit();

            return response()->json([
                'message' => 'Perubahan berhasil disimpan',
                'data' => $credential,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Perubahan gagal disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource attendances.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function attendances($id)
    {
        $currentMonth = date('m');
        $startDate = date("Y-m-01");
        $endDate = date("Y-m-t");
        $employee = Employee::with(['activeCareer', 'attendances' => function ($q) use ($currentMonth, $startDate, $endDate) {
            $q->with(['leaveApplication.category', 'longShiftConfirmer', 'permissionCategory'])->whereBetween('date', [$startDate, $endDate])->orderBy('id', 'desc');
        }, 'activeWorkingPatterns.items'])->findOrFail($id);

        // return $employee;

        $attendances = $employee->attendances;
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        $activeWorkingPattern = null;
        if (count($employee->activeWorkingPatterns) > 0) {
            $activeWorkingPattern = $employee->activeWorkingPatterns[0];
        }
        $newActiveWorkingPattern = collect($activeWorkingPattern)->all();
        $workingPatternItems = [];

        if (isset($newActiveWorkingPattern['items'])) {
            $workingPatternItems = $newActiveWorkingPattern['items'];
        }

        $eventCalendars = EventCalendar::all();

        $statistics = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'cuti' => 0,
            'na' => 0,
        ];

        $datesRange = $this->getDatesFromRange($startDate, $endDate);
        $finalAttendances = collect($datesRange)->map(function ($date, $key) use ($attendances, $eventCalendars, $days, $workingPatternItems, &$statistics) {
            $carbonDate = Carbon::parse($date);
            $dayIndex = $carbonDate->dayOfWeekIso;
            $attendance = collect($attendances)->where('date', $date)->first();
            $events = collect($eventCalendars)->where('date', $date)->values()->all();
            $workingPatternItem = collect($workingPatternItems)->where('order', $dayIndex)->first();
            // $activeWorkingPatternItems =

            $item = [
                'date' => $date,
                'iso_date' => $carbonDate->isoFormat('ll'),
                'day' => $days[$dayIndex - 1],
                'day_status' => null,
                'attendance' => null,
                'events' => $events,
            ];

            if ($attendance !== null) {
                $item['attendance'] = $attendance;
                if (isset($item['attendance']['status'])) {
                    $status = $item['attendance']['status'];
                    if ($status == 'hadir') {
                        $statistics['hadir'] += 1;
                    } else if ($status == 'sakit') {
                        $statistics['sakit'] += 1;
                    } else if ($status == 'izin') {
                        $statistics['izin'] += 1;
                    } else if ($status == 'cuti') {
                        $statistics['cuti'] += 1;
                    }
                }
            } else {
                $statistics['na'] += 1;
            }

            if ($workingPatternItem !== null) {
                $item['day_status'] = $workingPatternItem['day_status'];
            }

            return $item;
        })->all();

        return view('employees._id.attendances', [
            'employee' => $employee,
            'attendances' => $finalAttendances,
            'statistics' => $statistics,
            'dates' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        ]);
    }


    /**
     * Display the specified resource attendances.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getFilteredAttendances(Request $request, $id)
    {
        try {
            $currentMonth = date('m');
            $startDate = date("Y-m-01");
            $endDate = date("Y-m-t");

            if ($request->query('start_date') !== null && $request->query('end_date') !== null) {
                $startDate = $request->query('start_date');
                $endDate = $request->query('end_date');
            }

            $employee = Employee::with(['activeCareer', 'attendances' => function ($q) use ($currentMonth, $startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])->orderBy('id', 'desc');
            }, 'activeWorkingPatterns.items'])->findOrFail($id);

            $attendances = $employee->attendances;
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

            $activeWorkingPattern = null;
            if (count($employee->activeWorkingPatterns) > 0) {
                $activeWorkingPattern = $employee->activeWorkingPatterns[0];
            }
            $newActiveWorkingPattern = collect($activeWorkingPattern)->all();
            $workingPatternItems = [];

            if (isset($newActiveWorkingPattern['items'])) {
                $workingPatternItems = $newActiveWorkingPattern['items'];
            }

            $eventCalendars = EventCalendar::all();

            $statistics = [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'cuti' => 0,
                'na' => 0,
            ];

            $datesRange = $this->getDatesFromRange($startDate, $endDate);
            $finalAttendances = collect($datesRange)->map(function ($date, $key) use ($attendances, $eventCalendars, $days, $workingPatternItems, &$statistics) {
                $carbonDate = Carbon::parse($date);
                $dayIndex = $carbonDate->dayOfWeekIso;
                $attendance = collect($attendances)->where('date', $date)->first();
                $events = collect($eventCalendars)->where('date', $date)->values()->all();
                $workingPatternItem = collect($workingPatternItems)->where('order', $dayIndex)->first();
                // $activeWorkingPatternItems =

                $item = [
                    'date' => $date,
                    'iso_date' => $carbonDate->isoFormat('ll'),
                    'day' => $days[$dayIndex - 1],
                    'day_status' => null,
                    'attendance' => null,
                    'events' => $events,
                ];

                if ($attendance !== null) {
                    $item['attendance'] = $attendance;
                    if (isset($item['attendance']['status'])) {
                        $status = $item['attendance']['status'];
                        if ($status == 'hadir') {
                            $statistics['hadir'] += 1;
                        } else if ($status == 'sakit') {
                            $statistics['sakit'] += 1;
                        } else if ($status == 'izin') {
                            $statistics['izin'] += 1;
                        } else if ($status == 'cuti') {
                            $statistics['cuti'] += 1;
                        }
                    }
                } else {
                    $statistics['na'] += 1;
                }

                if ($workingPatternItem !== null) {
                    $item['day_status'] = $workingPatternItem['day_status'];
                }

                return $item;
            })->all();

            return response()->json([
                'message' => 'OK',
                'data' => [
                    'attendances' => $finalAttendances,
                    'statistics' => $statistics,
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
            //throw $th;
        }
    }

    public function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        $array = array();
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        return $array;
    }

    /**
     * Display the specified resource loans.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function loans($id)
    {
        $employee = Employee::with(['loans' => function ($q) {
            $q->with(['name', 'items' => function ($q2) {
                $q2->withCount(['salaryItem']);
            }]);
        }, 'activeCareer', 'activeWorkingPatterns.items'])->findOrFail($id);

        $loans = collect($employee->loans)->map(function ($loan) {
            $items = $loan->items;
            $paidItems = collect($items)->filter(function ($item) {
                return $item->salary_item_count > 0;
            });
            $totalPaidItem = $paidItems->sum('basic_payment');
            $completion = round(($totalPaidItem / $loan->amount) * 100);
            $status = $completion > 100 ? 'completed' : 'on_progress';
            $loan['total_paid'] = $totalPaidItem;
            $loan['completion'] = $completion;
            $loan['status'] = $status;
            return $loan;
        });

        $finalLoans = $loans->all();

        $totalLoans = $loans->count();
        $totalLoansAmount = $loans->sum('amount');
        $totalPaidLoans = $loans->sum('total_paid');
        $remainingLoans = $totalLoansAmount - $totalPaidLoans;

        $statistics = [
            'total_loans' => $totalLoans,
            'total_loans_amount' => $totalLoansAmount,
            'remaining_loans' => $remainingLoans,
        ];


        return view('employees._id.loans', [
            'employee' => $employee,
            'loans' => $finalLoans,
            'statistics' => $statistics,
        ]);
    }


    /**
     * Display the specified resource loans.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function insurances($id)
    {
        $year = date('Y');
        $employee = Employee::with([
            'loans' => function ($q) {
                $q->with(['name', 'items' => function ($q2) {
                    $q2->withCount(['salaryItem']);
                }]);
            },
            'bpjs',
            'privateInsurances' => function ($q) use ($year, $id) {
                $q->with(['values' => function ($q2) use ($year, $id) {
                    $q2->where('year', $year)->where('employee_id', $id);
                }]);
            },
            'activeCareer',
            'activeWorkingPatterns.items',
            'bpjsValues' => function ($q) use ($year, $id) {
                $q->where('year', $year)->where('employee_id', $id)->orderBy('id', 'desc');
            },
        ])->findOrFail($id);

        // return $employee;

        $privateInsurances = PrivateInsurance::all();

        return view('employees._id.insurances', [
            'employee' => $employee,
            'private_insurances' => $privateInsurances,
        ]);
    }

    /**
     * Display the specified resource loans.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function timeoffs($id)
    {
        $employee = Employee::with(['sickApplications', 'permissionApplications', 'leaveApplications', 'activeCareer', 'activeWorkingPatterns.items'])->findOrFail($id);
        // $sickApplications = SickApplication::with(['employee'])->where('employee_id', $id)->get();
        // $permissionApplications = PermissionApplication::with(['employee', 'category'])->where('employee_id', $id)->get();
        // $leaveApplications = LeaveApplication::with(['employee'])->where('employee_id', $id)->get();
        // return $permissionApplications;
        return view('employees._id.timeoffs', [
            'employee' => $employee,
            'sick_applications' => $employee->sickApplications,
            'permission_applications' => $employee->permissionApplications,
            'leave_applications' => $employee->leaveApplications,
        ]);
    }

    /**
     * Display the specified resource loans.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function files($id)
    {
        $employee = Employee::with(['files', 'bpjs', 'activeCareer', 'activeWorkingPatterns.items', 'driversLicenses'])->findOrFail($id);

        $employee->bpjs_ketenagakerjaan_image = null;
        $employee->bpjs_mandiri_image = null;

        if (isset($employee->bpjs->ketenagakerjaan_card_image)) {
            $employee->bpjs_ketenagakerjaan_image = $employee->bpjs->ketenagakerjaan_card_image;
        }

        if (isset($employee->bpjs->mandiri_card_image)) {
            $employee->bpjs_mandiri_image = $employee->bpjs->mandiri_card_image;
        }

        return view('employees._id.files', [
            'employee' => $employee,
            'files' => $employee->files,
        ]);
    }

    /**
     * Display the specified resource salaries.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function salaries($id)
    {
        $employee = Employee::with(['files', 'activeCareer', 'activeWorkingPatterns.items', 'driversLicenses', 'salaryComponents' => function ($salaryComponentQuery) {
            // $salaryComponentQuery->orderBy('id', 'DESC');
            // $salaryComponentQuery->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
            $salaryComponentQuery->orderBy('employee_salary_component.id', 'desc')->orderBy('employee_salary_component.effective_date', 'desc');
        }])->findOrFail($id);

        // Salary values
        $salaryTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'lembur', 'tunjangan_harian', 'uang_makan'];
        $employeeSalaryValues = collect($salaryTypes)->mapWithKeys(function ($salaryType) use ($employee) {
            $component = collect($employee->salaryComponents)->where('salary_type', $salaryType)->first();
            $value = $component->pivot->amount ?? 0;
            $coefficient = $component->pivot->coefficient ?? 1;
            return [
                $salaryType => [
                    'value' => $value,
                    'coefficient' => $coefficient,
                ],
            ];
        })->all();

        return view('employees._id.salaries', [
            'employee' => $employee,
            'salary_values' => $employeeSalaryValues,
        ]);
    }

    /**
     * Update the employee NPWP.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSalary(Request $request, $id)
    {
        try {
            $salaryComponents = SalaryComponent::all();
            $employeeSalaryComponents = [];
            $coreSalaryComponentsIds = [];

            $salaryComponentTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'lembur', 'tunjangan_harian', 'uang_makan'];
            foreach ($salaryComponentTypes as $salaryComponentType) {
                $salaryComponent = collect($salaryComponents)->where('salary_type', $salaryComponentType)->first();
                $salaryAmount = $request->{'salary_' . $salaryComponentType . '_value'} ?? 0;
                $salaryCoefficient = $request->{'salary_' . $salaryComponentType . '_coefficient'} ?? 1;

                if (isset($salaryComponent)) {
                    array_push($coreSalaryComponentsIds, $salaryComponent->id);
                    array_push($employeeSalaryComponents, [
                        'employee_id' => $id,
                        'salary_component_id' => $salaryComponent->id,
                        'amount' => $salaryAmount,
                        'coefficient' => $salaryCoefficient,
                        'effective_date' => date('Y-01-01'),
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]);
                }
            }

            if (count($employeeSalaryComponents) > 0) {
                DB::table('employee_salary_component')->where('employee_id', $id)->whereIn('salary_component_id', $coreSalaryComponentsIds)->delete();
                DB::table('employee_salary_component')->insert($employeeSalaryComponents);
            }

            return response()->json([
                'message' => 'Perubahan berhasil disimpan',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate employee leave.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activateLeave(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $totalLeave = 12;
            $newLeave = new Leave();
            $newLeave->total = $totalLeave;
            $newLeave->employee_id = $id;
            $newLeave->save();

            DB::commit();

            $employee = Employee::find($id);

            return response()->json([
                'message' => 'Cuti ' . $employee->name . ' berhasil diaktifkan',
                'data' => $newLeave,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Perubahan gagal disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate employee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $employee = Employee::find($id);
            $employee->active = true;
            $employee->save();

            DB::commit();

            return response()->json([
                'message' =>  $employee->name . ' berhasil diaktifkan',
                'data' => $employee,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Perubahan gagal disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate employee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deactivate(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $employee = Employee::find($id);
            $employee->active = false;
            $employee->save();

            DB::commit();

            return response()->json([
                'message' =>  $employee->name . ' berhasil dinonaktifkan',
                'data' => $employee,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Perubahan gagal disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate employee leave.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateBpjs(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $ketenagakerjaanNumber = $request->ketenagakerjaan_number;
            $ketenagakerjaanStartYear = $request->ketenagakerjaan_start_year;
            $mandiriNumber = $request->mandiri_number;

            // Photo
            $filePath = null;
            $ketenagakerjaanImageUrlPath = null;
            if ($request->hasFile('ketenagakerjaan_image')) {
                $ketenagakerjaanImageUrlPath = $this->handleUploadImage($request, 'ketenagakerjaan_image', 'employees/bpjs_cards/');
            } else {
                $requestImage = $request->ketenagakerjaan_image;
                if (!isset($requestImage) || $requestImage == 'null') {
                    $requestImage = null;
                }
                $ketenagakerjaanImageUrlPath = $requestImage;
            }

            $mandiriImageUrlPath = null;
            if ($request->hasFile('mandiri_image')) {
                $mandiriImageUrlPath = $this->handleUploadImage($request, 'mandiri_image', 'employees/bpjs_cards/');
            } else {
                // $mandiriImageUrlPath = $request->mandiri_image;
                $requestImage = $request->mandiri_image;
                if (!isset($requestImage) || $requestImage == 'null') {
                    $requestImage = null;
                }
                $mandiriImageUrlPath = $requestImage;
            }

            $bpjs = EmployeeBpjs::updateOrCreate(
                ['employee_id' => $id],
                [
                    'ketenagakerjaan_number' => $ketenagakerjaanNumber,
                    'ketenagakerjaan_start_year' => $ketenagakerjaanStartYear,
                    'ketenagakerjaan_card_image' => $ketenagakerjaanImageUrlPath,
                    'mandiri_number' => $mandiriNumber,
                    'mandiri_card_image' => $mandiriImageUrlPath,
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Perubahan berhasil disimpan',
                'data' => $bpjs,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Perubahan gagal disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate employee leave.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createPrivateInsurance(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $privateInsuranceId = $request->private_insurance_id;
            $number = $request->number;
            $startYear = $request->start_year;

            $validated = $request->validate([
                'private_insurance_id' => 'required|numeric',
                'number' => ['required', 'max:255'],
                'start_year' => ['required', 'numeric'],
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);

            $hasSamePrivateInsurance = DB::table('employee_private_insurance')->where('employee_id', $id)->where('private_insurance_id', $privateInsuranceId)->exists();

            if ($hasSamePrivateInsurance) {
                throw new Error('Pegawai ini sudah memiliki asuransi yang sama');
            }

            $employee = Employee::find($id);

            $imageUrlPath = null;
            if ($request->hasFile('image')) {
                $imageUrlPath = $this->handleUploadImage($request, 'image', 'employees/private_insurance_cards/');
            } else {
                // $imageUrlPath = $request->mandiri_image;
                $requestImage = $request->image;
                if (!isset($requestImage) || $requestImage == 'null') {
                    $requestImage = null;
                }
                $imageUrlPath = $requestImage;
            }

            $employee->privateInsurances()->attach($privateInsuranceId, [
                'card_image' => $imageUrlPath,
                'number' => $number,
                'start_year' => $startYear,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);


            DB::commit();

            return response()->json([
                'message' => 'Perubahan berhasil disimpan',
                'data' => $employee,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Perubahan gagal disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deletePrivateInsurance(Request $request, $id)
    {
        try {
            $privateInsuranceId = $request->query('private_insurance_id');

            if ($privateInsuranceId == null) {
                throw new Error('"private_insurance_id" is required');
            }

            $employee = Employee::find($id);

            $employee->privateInsurances()->detach([$privateInsuranceId]);

            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate employee leave.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePrivateInsurance(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $privateInsuranceId = $request->private_insurance_id;
            $number = $request->number;
            $startYear = $request->start_year;

            $validated = $request->validate([
                'private_insurance_id' => 'required|numeric',
                'number' => ['required', 'max:255'],
                'start_year' => ['required', 'numeric'],
            ]);

            // $hasSamePrivateInsurance = DB::table('employee_private_insurance')->where('employee_id', $id)->where('private_insurance_id', $privateInsuranceId)->exists();

            // if ($hasSamePrivateInsurance) {
            //     throw new Error('Pegawai ini sudah memiliki asuransi yang sama');
            // }

            $employee = Employee::find($id);

            $imageUrlPath = null;
            if ($request->hasFile('image')) {
                $imageUrlPath = $this->handleUploadImage($request, 'image', 'employees/private_insurance_cards/');
            } else {
                // $imageUrlPath = $request->mandiri_image;
                $requestImage = $request->image;
                if (!isset($requestImage) || $requestImage == 'null') {
                    $requestImage = null;
                }
                $imageUrlPath = $requestImage;
            }

            $employee->privateInsurances()->updateExistingPivot($privateInsuranceId, [
                'number' => $number,
                'start_year' => $startYear,
                'card_image' => $imageUrlPath,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Perubahan berhasil disimpan',
                'data' => $employee,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Perubahan gagal disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function dataCompletion()
    {
        $attributes = [
            [
                'key' => 'start_work_date',
                'name' => 'Tanggal Mulai Bekerja',
            ],
            [
                'key' => 'employment_status',
                'name' => 'Status Pegawai',
            ],
            [
                'key' => 'npwp_status',
                'name' => 'PTKP',
            ],
            [
                'key' => 'company',
                'name' => 'Perusahaan',
            ],
            [
                'key' => 'division',
                'name' => 'Division',
            ],
            [
                'key' => 'office',
                'name' => 'Kantor',
            ],
            [
                'key' => 'department',
                'name' => 'Departemen',
            ],
            [
                'key' => 'designation',
                'name' => 'Bagian',
            ],
            [
                'key' => 'job_title',
                'name' => 'Job Title',
            ],
            [
                'key' => 'date_of_birth',
                'name' => 'Tanggal Lahir',
            ],
            [
                'key' => 'place_of_birth',
                'name' => 'Tempat Lahir',
            ],
            [
                'key' => 'address',
                'name' => 'Alamat',
            ],
            [
                'key' => 'email',
                'name' => 'Email',
            ],
            [
                'key' => 'phone',
                'name' => 'HP',
            ],
            [
                'key' => 'identity_number',
                'name' => 'Nomor KTP',
            ],
            [
                'key' => 'identity_image',
                'name' => 'Foto KTP',
            ],
            [
                'key' => 'npwp_number',
                'name' => 'Nomor NPWP',
            ],
            [
                'key' => 'npwp_image',
                'name' => 'Foto NPWP',
            ],
            [
                'key' => 'recent_education',
                'name' => 'Pendidikan Terakhir',
            ],
            [
                'key' => 'education_institution_name',
                'name' => 'Nama Institusi',
            ],
            [
                'key' => 'driver_license',
                'name' => 'SIM',
            ],
            [
                'key' => 'emergency_contact_name',
                'name' => 'Nama Kontak Darurat',
            ],
            [
                'key' => 'emergency_contact_phone',
                'name' => 'HP Kontak Darurat',
            ],
            [
                'key' => 'emergency_contact_relation',
                'name' => 'Hubungan Kontak Darurat',
            ],
            [
                'key' => 'emergency_contact_relation',
                'name' => 'Relasi Kontak Darurat',
            ],
            [
                'key' => 'salary_gaji_pokok',
                'name' => '(Penggajian) Gaji Pokok',
            ],
            [
                'key' => 'salary_uang_harian',
                'name' => '(Penggajian) Uang Harian',
            ],
            [
                'key' => 'salary_lembur',
                'name' => '(Penggajian) Lembur',
            ],
            [
                'key' => 'salary_tunjangan',
                'name' => '(Penggajian) Tunjangan',
            ],
            [
                'key' => 'bpjs',
                'name' => 'BPJS',
            ],
        ];

        $salaryComponents = SalaryComponent::all();
        $gajiPokokComponent = collect($salaryComponents)->where('salary_type', 'gaji_pokok')->first();
        $uangHarianComponent = collect($salaryComponents)->where('salary_type', 'uang_harian')->first();
        $lemburComponent = collect($salaryComponents)->where('salary_type', 'lembur')->first();
        $tunjanganComponent = collect($salaryComponents)->where('salary_type', 'tunjangan')->first();

        $mappedSalaryComponents = [
            'gaji_pokok' => $gajiPokokComponent,
            'uang_harian' => $uangHarianComponent,
            'lembur' => $lemburComponent,
            'tunjangan' => $tunjanganComponent,
        ];

        $employees = Employee::with(['activeCareer.jobTitle.designation.department', 'office.division.company', 'bpjs'])->get()->each(function ($employee) use ($attributes, $mappedSalaryComponents) {
            $incompleteAttributes = [];
            foreach ($attributes as $attribute) {
                $attributeValue = $employee->{$attribute['key']} ?? null;

                if ($attribute['key'] == 'employment_status') {
                    $attributeValue = $employee->activeCareer->status ?? null;
                }

                // Company
                if ($attribute['key'] == 'office') {
                    $attributeValue = $employee->office->id ?? null;
                }

                if ($attribute['key'] == 'division') {
                    $attributeValue = $employee->office->division->id ?? null;
                }

                if ($attribute['key'] == 'company') {
                    $attributeValue = $employee->office->division->company->id ?? null;
                }

                // Employment
                if ($attribute['key'] == 'job_title') {
                    $attributeValue = $employee->activeCareer->jobTitle->id ?? null;
                }

                if ($attribute['key'] == 'designation') {
                    $attributeValue = $employee->activeCareer->jobTitle->designation->id ?? null;
                }

                if ($attribute['key'] == 'department') {
                    $attributeValue = $employee->activeCareer->jobTitle->designation->department->id ?? null;
                }

                // Penggajian
                if ($attribute['key'] == 'salary_gaji_pokok') {
                    $attributeValue = $mappedSalaryComponents['gaji_pokok'];
                }

                if ($attribute['key'] == 'salary_uang_harian') {
                    $attributeValue = $mappedSalaryComponents['uang_harian'];
                }

                if ($attribute['key'] == 'salary_lembur') {
                    $attributeValue = $mappedSalaryComponents['lembur'];
                }

                if ($attribute['key'] == 'salary_tunjangan') {
                    $attributeValue = $mappedSalaryComponents['tunjangan'];
                }

                // SIM
                if ($attribute['key'] == 'driver_license') {
                    $designationId = $employee->activeCareer->jobTitle->designation->id ?? null;

                    $attributeValue = "exist";

                    if (isset($designationId)) {
                        if ($designationId == 13) {
                            $attributeValue = $employee->driver_license_number ?? null;
                        }
                    }
                }

                // Insurance
                if ($attribute['key'] == 'bpjs') {
                    $attributeValue = $employee->bpjs ?? null;
                }


                if (!isset($attributeValue) || $attributeValue == "NULL") {
                    array_push($incompleteAttributes, $attribute['name']);
                }
            }

            $employee->incomplete_attributes = $incompleteAttributes;
        })->filter(function ($employee) {
            return count($employee->incomplete_attributes) > 0;
        })->all();

        // return $employees;

        return view('employees.data-completion', [
            'employees' => $employees,
        ]);
    }
}
