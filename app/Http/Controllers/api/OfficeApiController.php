<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Office;
use Illuminate\Http\Request;

class OfficeApiController extends Controller
{
    public function getAll()
    {
        try {
            $offices = Office::all();

            return response()->json([
                'message' => 'OK',
                'data' => $offices,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
