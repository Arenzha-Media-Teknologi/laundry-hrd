<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PermissionCategory;
use Illuminate\Http\Request;

class PermissionCategoryApiController extends Controller
{
    public function getAll()
    {
        try {
            $permissionCategories = PermissionCategory::all();

            return response()->json([
                'message' => 'OK',
                'data' => $permissionCategories,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
