<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CareerController extends Controller
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
        DB::beginTransaction();
        try {
            $status = $request->status;
            $effectiveDate = $request->effective_date;
            $jobTitleId = $request->job_title_id;
            $employeeId = $request->employee_id;

            $request->validate([
                'status' => 'required|string',
                'effective_date' => 'required|date|after_or_equal:last_date_career',
                'job_title_id' => 'required|numeric',
                'employee_id' => 'required|numeric',
            ]);

            Career::where('employee_id', $employeeId)
                ->update(['active' => 0]);

            $career = new Career();
            $career->status = $status;
            $career->effective_date = $effectiveDate;
            $career->job_title_id = $jobTitleId;
            $career->employee_id = $employeeId;
            $career->save();

            $careers = Career::with(['jobTitle.designation.department'])->where('employee_id',  $employeeId)->orderBy('effective_date', 'desc')->orderBy('created_at', 'desc')->get();

            DB::commit();

            return response()->json([
                'message' => 'Karir telah tersimpan',
                'data' => [
                    'career' => $career,
                    'careers' => $careers,
                ],
            ]);
        } catch (Exception $e) {
            DB::rollBack();
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
        DB::beginTransaction();
        try {
            $status = $request->status;
            $effectiveDate = $request->effective_date;
            $jobTitleId = $request->job_title_id;
            $employeeId = $request->employee_id;

            $request->validate([
                'status' => 'required|string',
                'effective_date' => 'required|date',
                'job_title_id' => 'required|numeric',
                'employee_id' => 'required|numeric',
            ]);

            // Career::where('employee_id', $employeeId)
            //     ->update(['active' => 0]);

            $career = Career::with(['jobTitle.designation.department'])->find($id);
            $career->status = $status;
            $career->effective_date = $effectiveDate;
            $career->job_title_id = $jobTitleId;
            $career->employee_id = $employeeId;
            $career->save();

            $careers = Career::with(['jobTitle.designation.department'])->where('employee_id',  $employeeId)->orderBy('effective_date', 'desc')->orderBy('created_at', 'desc')->get();

            DB::commit();

            return response()->json([
                'message' => 'Karir telah tersimpan',
                'data' => [
                    'career' => $career,
                    'careers' => $careers,
                ],
            ]);
        } catch (Exception $e) {
            DB::rollBack();
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
            $career = Career::findOrFail($id);
            $career->delete();
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
