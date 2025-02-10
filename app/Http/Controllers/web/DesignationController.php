<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            $departmentId = $request->department_id;

            $validated = $request->validate([
                'department_id' => 'required|integer',
                'name' => [
                    'required',
                    Rule::unique('designations')->where(function ($query) use ($name, $departmentId) {
                        return $query->where('name', $name)
                            ->where('department_id', $departmentId);
                    }),
                    'max:255'
                ],
            ]);

            $designation = new Designation();
            $designation->name = ucwords($name);
            $designation->department_id = $departmentId;
            $designation->save();

            $newDesignation = Designation::with(['department'])->find($designation->id);

            return response()->json([
                'message' => 'Bagian telah tersimpan',
                'data' => $newDesignation,
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
            $designation = Designation::with(['department'])->findOrFail($id);

            $name = $request->name;
            $departmentId = $request->department_id;

            $validated = $request->validate([
                'department_id' => 'required|integer',
                'name' => [
                    'required',
                    Rule::unique('designations')->where(function ($query) use ($name, $departmentId) {
                        return $query->where('name', $name)
                            ->where('department_id', $departmentId);
                    })->ignore($designation->id),
                    'max:255'
                ],
            ]);

            $designation->name = ucwords($name);
            $designation->department_id = $departmentId;
            $designation->save();

            return response()->json([
                'message' => 'Bagian telah tersimpan',
                'data' => $designation,
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
            $department = Designation::findOrFail($id);
            $department->delete();
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
