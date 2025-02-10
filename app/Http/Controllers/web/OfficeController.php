<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Office;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $offices = Office::with(['division.company'])->get();

        return view('offices.index', [
            'offices' => $offices,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $divisions = Division::all();
        $companies = Company::all();

        return view('offices.create', [
            'divisions' => $divisions,
            'companies' => $companies,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function move()
    {
        $divisions = Division::all();
        $companies = Company::all();
        $offices = Office::with(['division.company'])->get();
        // return $offices;
        $AERPLUS_DIVISION_ID = 12;

        $employees = Employee::whereHas('office', function ($q) use ($AERPLUS_DIVISION_ID) {
            // $q->where('division_id', $AERPLUS_DIVISION_ID);
        })->with(['office', 'activeCareer.jobTitle'])->where('active', 1)->get();

        return view('offices.move', [
            'divisions' => $divisions,
            'companies' => $companies,
            'employees' => $employees,
            'offices' => $offices,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function doMove(Request $request)
    {
        try {
            $employeeIds = $request->employee_ids;
            $newOfficeId = $request->office_id;

            Employee::query()->whereIn('id', $employeeIds)->update([
                'office_id' => $newOfficeId,
            ]);

            return response()->json([
                'message' => 'Data berhasil disimpan',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
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
            $companyId = $request->company_id;
            $divisionId = $request->division_id;
            $name = $request->name;
            $phone = $request->phone;
            $address = $request->address;
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $validated = $request->validate([
                'division_id' => 'required|integer',
                'name' => [
                    'required',
                    Rule::unique('offices')->where(function ($query) use ($name, $divisionId) {
                        return $query->where('name', $name)
                            ->where('division_id', $divisionId);
                    }),
                    'max:255'
                ],
                'phone' => 'max:50',
                'address' => 'max:255',
                'latitude' => 'required|max:50',
                'longitude' => 'required|max:50',
            ]);

            $office = new Office();
            $office->name = ucwords($name);
            $office->phone = $phone;
            $office->address = $address;
            $office->latitude = $latitude;
            $office->longitude = $longitude;
            $office->division_id = $divisionId;
            $office->save();

            $newOffice = Office::with(['division.company'])->find($office->id);

            return response()->json([
                'message' => 'Data kantor telah tersimpan',
                'data' => $newOffice,
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
        $office = Office::with(['division.company'])->findOrFail($id);
        $divisions = Division::all();
        $companies = Company::all();

        return view('offices.edit', [
            'office' => $office,
            'divisions' => $divisions,
            'companies' => $companies,
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
        try {
            $divisionId = $request->division_id;
            $name = $request->name;
            $phone = $request->phone;
            $address = $request->address;
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $office = Office::with(['division.company'])->find($id);

            $validated = $request->validate([
                'division_id' => 'required|integer',
                'name' => [
                    'required',
                    Rule::unique('offices')->where(function ($query) use ($name, $divisionId) {
                        return $query->where('name', $name)
                            ->where('division_id', $divisionId);
                    })->ignore($office->id),
                    'max:255'
                ],
                'phone' => 'max:50',
                'address' => 'max:255',
                'latitude' => 'required|max:50',
                'longitude' => 'required|max:50',
            ]);

            $office->name = ucwords($name);
            $office->phone = $phone;
            $office->address = $address;
            $office->latitude = $latitude;
            $office->longitude = $longitude;
            $office->division_id = $divisionId;
            $office->save();


            return response()->json([
                'message' => 'Data kantor telah tersimpan',
                'data' => $office,
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
            $office = Office::findOrFail($id);
            $office->delete();
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
