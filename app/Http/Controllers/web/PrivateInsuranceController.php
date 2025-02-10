<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Division;
use App\Models\PrivateInsurance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PrivateInsuranceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $insurances = PrivateInsurance::all();
        $divisions = Division::with(['company'])->get();
        $companies = Company::all();

        return view('private-insurances.index', [
            'insurances' => $insurances,
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
            $premiTabungan = $request->premi_tabungan;
            $premiKematian = $request->premi_kematian;

            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('private_insurances')->where(function ($query) use ($name) {
                        return $query->where('name', $name);
                    }),
                    'max:255'
                ],
                'premi_tabungan' => 'required',
                'premi_kematian' => 'required',
            ]);

            $insurance = new PrivateInsurance();
            $insurance->name = ucwords($name);
            $insurance->premi_tabungan = $premiTabungan;
            $insurance->premi_kematian = $premiKematian;

            $insurance->save();

            return response()->json([
                'message' => 'Jenis asuransi telah tersimpan',
                'data' => $insurance,
            ]);
        } catch (\Throwable $e) {
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
            $premiTabungan = $request->premi_tabungan;
            $premiKematian = $request->premi_kematian;

            $insurance = PrivateInsurance::findOrFail($id);

            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('private_insurances')->where(function ($query) use ($name) {
                        return $query->where('name', $name);
                    })->ignore($insurance->id),
                    'max:255'
                ],
                'premi_tabungan' => 'required',
                'premi_kematian' => 'required',
            ]);

            $insurance->name = ucwords($name);
            $insurance->premi_tabungan = $premiTabungan;
            $insurance->premi_kematian = $premiKematian;

            $insurance->save();

            return response()->json([
                'message' => 'Jenis asuransi telah tersimpan',
                'data' => $insurance,
            ]);
        } catch (\Throwable $e) {
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
            $insurance = PrivateInsurance::findOrFail($id);
            $insurance->delete();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }
}
