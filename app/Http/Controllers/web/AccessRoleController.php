<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\AccessRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccessRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accessRoles = AccessRole::all();

        return view('access-roles.index', [
            'access_roles' => $accessRoles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('access-roles.create');
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
            $validated = $request->validate([
                'name' => 'required|unique:access_roles|max:255',
                'permissions' => 'required',
            ]);

            $accessRole = new AccessRole();
            $accessRole->name = ucwords($request->name);
            $accessRole->permissions = strtoupper($request->permissions);
            $accessRole->save();

            return response()->json([
                'message' => 'Data perusahaan telah tersimpan',
                'data' => $accessRole,
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
        $accessRole = AccessRole::findOrFail($id);
        $permissions = json_decode(strtolower($accessRole->permissions));

        return view('access-roles.edit', [
            'access_role' => $accessRole,
            'permissions' => $permissions,
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
            $accessRole = AccessRole::findOrFail($id);

            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('access_roles')->ignore($accessRole->id),
                    'max:255',
                ],
                'permissions' => 'required',
            ]);

            // $accessRole = new AccessRole();
            $accessRole->name = ucwords($request->name);
            $accessRole->permissions = strtoupper($request->permissions);
            $accessRole->save();

            return response()->json([
                'message' => 'Perubahan telah tersimpan',
                'data' => $accessRole,
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
            $accessRole = AccessRole::findOrFail($id);
            $accessRole->delete();
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
