<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Division;
use App\Models\LeaveCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeaveCategoryController extends Controller
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
        $leaveCategories = LeaveCategory::all();

        return view('leave-categories.index', [
            'divisions' => $divisions,
            'companies' => $companies,
            'leave_categories' => $leaveCategories,
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
            $maxDay = $request->max_day;
            $maxAdvanceRequestDay = $request->max_advance_request_day;
            $allowEmployeeSubmission = $request->allow_employee_submission;

            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('leave_categories')->where(function ($query) use ($name) {
                        return $query->where('name', $name);
                    }),
                    'max:255'
                ],
                'max_day' => 'required|integer',
                'max_advance_request_day' => 'required|integer',
                'allow_employee_submission' => 'required',
            ]);

            $leaveCategory = new LeaveCategory();
            $leaveCategory->name = ucwords($name);
            $leaveCategory->max_day = $maxDay;
            $leaveCategory->max_advance_request_day = $maxAdvanceRequestDay;
            $leaveCategory->allow_employee_submission = $allowEmployeeSubmission;
            $leaveCategory->save();

            return response()->json([
                'message' => 'Data telah tersimpan',
                'data' => $leaveCategory,
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
            $maxDay = $request->max_day;
            $maxAdvanceRequestDay = $request->max_advance_request_day;
            $allowEmployeeSubmission = $request->allow_employee_submission;

            $leaveCategory = LeaveCategory::findOrFail($id);

            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('leave_categories')->where(function ($query) use ($name) {
                        return $query->where('name', $name);
                    })->ignore($leaveCategory->id),
                    'max:255'
                ],
                'max_day' => 'required|integer',
                'max_advance_request_day' => 'required|integer',
                'allow_employee_submission' => 'required',
            ]);

            $leaveCategory->name = ucwords($name);
            $leaveCategory->max_day = $maxDay;
            $leaveCategory->max_advance_request_day = $maxAdvanceRequestDay;
            $leaveCategory->allow_employee_submission = $allowEmployeeSubmission;
            $leaveCategory->save();

            return response()->json([
                'message' => 'Data telah tersimpan',
                'data' => $leaveCategory,
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
            $leaveCategory = LeaveCategory::findOrFail($id);
            $leaveCategory->delete();
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
