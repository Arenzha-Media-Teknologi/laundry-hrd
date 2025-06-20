<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PermissionNote;
use Illuminate\Http\Request;

class PermissionNoteApiController extends Controller
{
    public function getAll()
    {
        try {
            $permissionNotes = PermissionNote::all();

            return response()->json([
                'message' => 'OK',
                'data' => $permissionNotes,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
