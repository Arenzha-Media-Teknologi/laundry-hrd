<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Division;
use App\Models\Office;
use App\Models\WorkingPattern;
use App\Models\WorkingPatternItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkingPatternController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $workingPatterns = WorkingPattern::all();

        return view('working-patterns.index', [
            'working_patterns' => $workingPatterns,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('working-patterns.create');
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
            $name = $request->name;
            $items = $request->items;
            $numberOfDays = collect($items)->count();

            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('working_patterns')->where(function ($query) use ($name) {
                        return $query->where('name', $name);
                    }),
                    'max:255'
                ],
                'items.*.order' => 'required|numeric',
                'items.*.day_status' => ['required', Rule::in(['workday', 'holiday'])],
                'items.*.clock_in' => 'nullable|date_format:H:i,H:i:s',
                'items.*.clock_out' => 'nullable|date_format:H:i,H:i:s',
            ]);

            $workingPattern = new WorkingPattern();
            $workingPattern->name = ucwords($name);
            $workingPattern->number_of_days = $numberOfDays;
            $workingPattern->save();

            $workingPatternItems = collect($items)->map(function ($item) use ($workingPattern) {
                return [
                    'order' => $item['order'],
                    'day_status' => $item['day_status'],
                    'clock_in' => $item['clock_in'],
                    'clock_out' => $item['clock_out'],
                    'have_overtime' => $item['have_overtime'],
                    'overtime_start_time' => $item['overtime_start_time'],
                    'working_pattern_id' => $workingPattern->id,
                ];
            })->all();

            DB::table('working_pattern_items')->insert($workingPatternItems);

            // WorkingPatternItem::create($workingPatternItems);

            $newWorkingPattern = WorkingPattern::with(['items'])->find($workingPattern->id);

            DB::commit();

            return response()->json([
                'message' => 'Data pola kerja telah tersimpan',
                'data' => $newWorkingPattern,
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
        $workingPattern = WorkingPattern::with(['items'])->findOrFail($id);
        // return $workingPattern;
        return view('working-patterns.edit', [
            'working_pattern' => $workingPattern,
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
        DB::beginTransaction();
        try {
            $name = $request->name;
            $items = $request->items;
            $numberOfDays = collect($items)->count();

            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('working_patterns')->where(function ($query) use ($name) {
                        return $query->where('name', $name);
                    })->ignore($id),
                    'max:255'
                ],
                'items.*.order' => 'required|numeric',
                'items.*.day_status' => ['required', Rule::in(['workday', 'holiday'])],
                'items.*.clock_in' => 'nullable|date_format:H:i,H:i:s',
                'items.*.clock_out' => 'nullable|date_format:H:i,H:i:s',
            ]);

            $workingPattern = WorkingPattern::find($id);
            $workingPattern->name = ucwords($name);
            $workingPattern->number_of_days = $numberOfDays;
            $workingPattern->save();

            $workingPatternItems = collect($items)->map(function ($item) use ($workingPattern) {
                return [
                    'order' => $item['order'],
                    'day_status' => $item['day_status'],
                    'clock_in' => $item['day_status'] == 'workday' ? $item['clock_in'] : null,
                    'clock_out' => $item['day_status'] == 'workday' ? $item['clock_out'] : null,
                    'have_overtime' => $item['have_overtime'],
                    'overtime_start_time' => $item['have_overtime'] == 1 ? $item['overtime_start_time'] : null,
                    'working_pattern_id' => $workingPattern->id,
                ];
            })->all();

            DB::table('working_pattern_items')->where('working_pattern_id', $id)->delete();

            DB::table('working_pattern_items')->insert($workingPatternItems);

            // WorkingPatternItem::create($workingPatternItems);

            $newWorkingPattern = WorkingPattern::with(['items'])->find($workingPattern->id);

            DB::commit();

            return response()->json([
                'message' => 'Data pola kerja telah tersimpan',
                'data' => $newWorkingPattern,
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
            $workingPattern = WorkingPattern::findOrFail($id);
            $workingPattern->delete();
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
