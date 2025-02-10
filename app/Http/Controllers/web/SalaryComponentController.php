<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\SalaryComponent;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalaryComponentController extends Controller
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
            $type = $request->type;

            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('salary_components')->where(function ($query) use ($name, $type) {
                        return $query->where('name', $name)
                            ->where('type', $type);
                    }),
                    'max:255'
                ],
                'type' => [
                    Rule::in(['income', 'deduction'])
                ],
            ]);

            $component = new SalaryComponent();
            $component->name = ucwords($name);
            $component->type = $type;
            $component->save();

            $newComponent = SalaryComponent::find($component->id);

            return response()->json([
                'message' => 'Data komponen gaji telah tersimpan',
                'data' => $newComponent,
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
            $type = $request->type;

            $component = SalaryComponent::find($id);

            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('salary_components')->where(function ($query) use ($name, $type) {
                        return $query->where('name', $name)
                            ->where('type', $type);
                    })->ignore($component->id),
                    'max:255'
                ],
                'type' => [
                    Rule::in(['income', 'deduction'])
                ],
            ]);

            $component->name = ucwords($name);
            $component->type = $type;
            $component->save();

            // $newComponent = SalaryComponent::find($component->id);

            return response()->json([
                'message' => 'Data komponen gaji telah tersimpan',
                'data' => $component,
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
            $component = SalaryComponent::findOrFail($id);
            $component->delete();
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
