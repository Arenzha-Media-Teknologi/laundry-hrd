<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Division;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $divisions = Division::with(['company'])->get();
        $companies = Company::all();

        return view('divisions.index', [
            'divisions' => $divisions,
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
            $name = $request->name;
            $initial = $request->initial;
            $address = $request->address;
            $companyId = $request->company_id;

            $validated = $request->validate([
                'company_id' => 'required|integer',
                'name' => [
                    'required',
                    Rule::unique('divisions')->where(function ($query) use ($name, $companyId) {
                        return $query->where('name', $name)
                            ->where('company_id', $companyId);
                    }),
                    'max:255'
                ],
                'initial' => [
                    'required',
                    Rule::unique('divisions')->where(function ($query) use ($initial, $companyId) {
                        return $query->where('initial', $initial)
                            ->where('company_id', $companyId);
                    }),
                    'max:10'
                ],
                'address' => 'max:255',
            ]);

            $division = new Division();
            $division->name = ucwords($name);
            $division->initial = strtoupper($initial);
            $division->address = $address;
            $division->company_id = $companyId;
            $division->save();

            $newDivision = Division::with(['company'])->find($division->id);

            return response()->json([
                'message' => 'Data divisi telah tersimpan',
                'data' => $newDivision,
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
            $name = $request->name;
            $initial = $request->initial;
            $address = $request->address;
            $companyId = $request->company_id;

            $division = Division::with(['company'])->findOrFail($id);

            $validated = $request->validate([
                'company_id' => 'required|integer',
                'name' => [
                    'required',
                    Rule::unique('divisions')->where(function ($query) use ($name, $companyId) {
                        return $query->where('name', $name)
                            ->where('company_id', $companyId);
                    })->ignore($division->id),
                    'max:255'
                ],
                'initial' => [
                    'required',
                    Rule::unique('divisions')->where(function ($query) use ($initial, $companyId) {
                        return $query->where('initial', $initial)
                            ->where('company_id', $companyId);
                    })->ignore($division->id),
                    'max:10'
                ],
                'address' => 'max:255',
            ]);

            $division->name = ucwords($name);
            $division->initial = strtoupper($initial);
            $division->address = $address;
            $division->company_id = $companyId;
            $division->save();

            return response()->json([
                'message' => 'Data divisi telah tersimpan',
                'data' => $division,
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
            $company = Division::findOrFail($id);
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
