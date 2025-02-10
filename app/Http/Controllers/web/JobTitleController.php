<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\JobTitle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobTitleController extends Controller
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
            $designationId = $request->designation_id;

            $validated = $request->validate([
                'designation_id' => 'required|integer',
                'name' => [
                    'required',
                    Rule::unique('job_titles')->where(function ($query) use ($name, $designationId) {
                        return $query->where('name', $name)
                            ->where('designation_id', $designationId);
                    }),
                    'max:255'
                ],
            ]);

            $jobTitle = new JobTitle();
            $jobTitle->name = ucwords($name);
            $jobTitle->designation_id = $designationId;
            $jobTitle->save();

            $newDesignation = JobTitle::with(['designation.department'])->find($jobTitle->id);

            return response()->json([
                'message' => 'Job title telah tersimpan',
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
            $jobTitle = JobTitle::with(['designation.department'])->findOrFail($id);

            $name = $request->name;
            $designationId = $request->designation_id;

            $validated = $request->validate([
                'designation_id' => 'required|integer',
                'name' => [
                    'required',
                    Rule::unique('job_titles')->where(function ($query) use ($name, $designationId) {
                        return $query->where('name', $name)
                            ->where('designation_id', $designationId);
                    })->ignore($jobTitle->id),
                    'max:255'
                ],
            ]);

            $jobTitle->name = ucwords($name);
            $jobTitle->designation_id = $designationId;
            $jobTitle->save();

            return response()->json([
                'message' => 'Job title telah tersimpan',
                'data' => $jobTitle,
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
            $jobTitle = JobTitle::findOrFail($id);
            $jobTitle->delete();
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
