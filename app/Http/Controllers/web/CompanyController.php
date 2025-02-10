<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyBusinessType;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->authorizeResource(Company::class, 'company');
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('view', Company::class)) {
            abort(403);
        }

        // $this->authorize('view', Company::class);

        $companies = Company::with(['mainCommissioner', 'presidentDirector', 'director', 'commisioner'])->get();
        $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();

        $companyBusinessTypes = CompanyBusinessType::all();

        return view('companies.index2', [
            'companies' => $companies,
            'employees' => $employees,
            'company_business_types' => $companyBusinessTypes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {
            if ($request->user()->cannot('create', Company::class)) {
                abort(403);
            }
            $validated = $request->validate([
                'name' => 'required|unique:companies|max:255',
                'initial' => 'required|max:10',
                'address' => 'max:255',
            ]);

            $company = new Company();
            $company->name = ucwords($request->name);
            $company->initial = strtoupper($request->initial);
            $company->phone = $request->phone;
            $company->type = $request->type;
            $company->email = $request->email;
            $company->address = $request->address;
            $company->npwp_number = $request->npwp_number;
            $company->npwp_address = $request->npwp_address;
            $company->main_commisioner = $request->main_commissioner;
            $company->commisioner = $request->commisioner;
            $company->president_director = $request->president_director;
            $company->director = $request->director;
            $company->corporate_id = $request->corporate_id;
            $company->company_code = $request->company_code;
            $company->business_type = $request->business_type;
            $company->debited_account = $request->debited_account;
            $company->save();

            return response()->json([
                'message' => 'Data perusahaan telah tersimpan',
                'data' => $company,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
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
    public function edit($id)
    {
        //
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

        try {
            if ($request->user()->cannot('update', Company::class)) {
                abort(403);
            }
            $company = Company::findOrFail($id);

            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('companies')->ignore($company->id),
                    'max:255',
                ],
                'initial' => 'required|max:10',
                'address' => 'max:255',
            ]);

            $company->name = ucwords($request->name);
            $company->initial = strtoupper($request->initial);
            $company->phone = $request->phone;
            $company->email = $request->email;
            $company->address = $request->address;
            $company->npwp_number = $request->npwp_number;
            $company->npwp_address = $request->npwp_address;
            $company->main_commisioner = $request->main_commissioner;
            $company->commisioner = $request->commisioner;
            $company->president_director = $request->president_director;
            $company->director = $request->director;
            $company->corporate_id = $request->corporate_id;
            $company->company_code = $request->company_code;
            $company->business_type = $request->business_type;
            $company->debited_account = $request->debited_account;
            $company->save();

            return response()->json([
                'message' => 'Perubahan telah tersimpan',
                'data' => $company,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
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
            if (request()->user()->cannot('delete', Company::class)) {
                abort(403);
            }
            $company = Company::findOrFail($id);
            $company->delete();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }
}
