<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\WorkScheduleWorkingPattern;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkScheduleWorkingPatternController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $workScheduleWorkingPatterns = WorkScheduleWorkingPattern::all();

        return view('work-schedule-working-pattern.index', [
            'work_schedule_working_patterns' => $workScheduleWorkingPatterns,
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
            $startTime = $request->start_time;
            $endTime = $request->end_time;
            $color = $request->color;
            $haveOvertime = $request->have_overtime;
            $overtimeStartTime = $request->overtime_start_time;

            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('work_schedule_working_patterns')->where(function ($query) use ($name) {
                        return $query->where('name', $name);
                    }),
                    'max:255'
                ],
                'start_time' => [
                    'required',
                ],
                'end_time' => [
                    'required',
                ],
                'color' => [
                    'required',
                ],
            ]);

            if ($haveOvertime == 1) {
                if (empty($overtimeStartTime)) {
                    throw new Error('Jam Mulai Lembur harus diisi');
                } else {
                    if (Carbon::parse($overtimeStartTime)->toTimeString() < Carbon::parse($endTime)->toTimeString()) {
                        throw new Error('Jam Mulai Lembur tidak boleh lebih kecil dari jam keluar');
                    }
                }
            }

            $workingPattern = new WorkScheduleWorkingPattern();
            $workingPattern->name = ucwords($name);
            $workingPattern->start_time = $startTime;
            $workingPattern->end_time = $endTime;
            $workingPattern->color = $color;
            $workingPattern->have_overtime = $haveOvertime;
            $workingPattern->overtime_start_time = $overtimeStartTime;
            $workingPattern->save();

            return response()->json([
                'message' => 'Data telah tersimpan',
                'data' => $workingPattern,
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
            $startTime = $request->start_time;
            $endTime = $request->end_time;
            $color = $request->color;
            $haveOvertime = $request->have_overtime;
            $overtimeStartTime = $request->overtime_start_time;

            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('work_schedule_working_patterns')->where(function ($query) use ($name, $id) {
                        return $query->where('name', $name)->where('id', '!=', $id);
                    }),
                    'max:255'
                ],
                'start_time' => [
                    'required',
                ],
                'end_time' => [
                    'required',
                ],
                'color' => [
                    'required',
                ],
            ]);

            if ($haveOvertime == 1) {
                if (empty($overtimeStartTime)) {
                    throw new Error('Jam Mulai Lembur harus diisi');
                } else {
                    if (Carbon::parse($overtimeStartTime)->toTimeString() < Carbon::parse($endTime)->toTimeString()) {
                        throw new Error('Jam Mulai Lembur tidak boleh lebih kecil dari jam keluar');
                    }
                }
            }

            $workingPattern = WorkScheduleWorkingPattern::find($id);
            $workingPattern->name = ucwords($name);
            $workingPattern->start_time = $startTime;
            $workingPattern->end_time = $endTime;
            $workingPattern->color = $color;
            $workingPattern->have_overtime = $haveOvertime;
            $workingPattern->overtime_start_time = $overtimeStartTime;
            $workingPattern->save();

            return response()->json([
                'message' => 'Data telah tersimpan',
                'data' => $workingPattern,
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
            $workingPattern = WorkScheduleWorkingPattern::findOrFail($id);
            $workingPattern->delete();
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
