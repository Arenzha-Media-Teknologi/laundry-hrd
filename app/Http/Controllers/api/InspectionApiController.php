<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class InspectionApiController extends Controller
{
    public function getAll()
    {
        $whereClauses = request()->query();
        try {
            $inspections = Inspection::query()->where($whereClauses)->get();
            return response()->json([
                'message' => 'OK',
                'data' => $inspections,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required',
                'datetime' => 'required|date',
                'latitude' => 'required|max:255',
                'longitude' => 'required|max:255',
                'address' => 'required|max:255',
            ]);

            $inspection = new Inspection;
            $inspection->employee_id = $request->employee_id;
            $inspection->datetime = $request->datetime;
            $inspection->latitude = $request->latitude;
            $inspection->longitude = $request->longitude;
            $inspection->address = $request->address;

            // Photo
            // $urlPath = null;
            // if ($request->hasFile('image')) {
            //     $urlPath = $this->handleUploadImage($request, 'image', 'inspections/photos/');
            // }
            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                // Compress Image
                // $imageWidth = Image::make($file->getRealPath())->width();
                // $ratio = 30 / 100;
                // $newWidth = floor($imageWidth * $ratio);
                // $resizedImage = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
                //     $constraint->aspectRatio();
                // })->stream();

                // Uploading Image
                $name = time() . '_inspection_' . $file->getClientOriginalName();
                // $filePath = 'expenses/images/' . $name;
                // $path = Storage::disk('s3')->put($filePath, $file, 'public');
                $filePath = 'inspections/photos/';
                $path = $file->storePubliclyAs($filePath, $name, 's3');
                $urlPath = Storage::disk('s3')->url($filePath . $name);
            }

            $inspection->image = $urlPath;
            $inspection->save();

            return response()->json([
                'message' => 'Data berhasil disimpan',
                'data' => $inspection,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            Inspection::find($id)->delete();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * handleUploadImage
     */
    private function handleUploadImage(Request $request, $imageKey, $dir)
    {
        $file = $request->file($imageKey);
        // Compress Image
        // $imageWidth = Image::make($file->getRealPath())->width();
        // $ratio = 50 / 100;
        // $newWidth = floor($imageWidth * $ratio);
        // $resizedImage = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
        //     $constraint->aspectRatio();
        // })->stream();

        // Uploading Image
        $name = time() . '_' . $file->getClientOriginalName();
        $filePath = $dir . $name;
        $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
        $urlPath = Storage::disk('s3')->url($filePath);

        return $urlPath;
    }
}
