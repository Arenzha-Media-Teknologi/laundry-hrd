<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeFile;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
// use Intervention\Image\Image;
use Intervention\Image\Facades\Image;

class EmployeeFileController extends Controller
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
            // Validation
            $validated = $request->validate([
                'name' => 'required|max:255',
                'file' => 'required|max:5120',
                'employee_id' => 'required|integer',
            ]);

            $fileName = $request->name;
            $employeeId = $request->employee_id;

            $employee = Employee::find($employeeId);
            if ($employee == null) {
                throw new Error('Pegawai tidak ditemukan');
            }

            // Photo
            $filePath = null;
            $urlPath = null;
            $contentType = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $contentType = $file->getMimeType();

                $imagesMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                $finalFile = file_get_contents($file);

                if (in_array($contentType, $imagesMimeTypes)) {
                    // Compress Image
                    $imageWidth = Image::make($file->getRealPath())->width();
                    $ratio = 50 / 100;
                    $newWidth = floor($imageWidth * $ratio);
                    $finalFile = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->stream();
                }


                // Uploading Image
                $name = time() . '_' . $employeeId . '_' . ucwords(preg_replace('~[^A-Za-z0-9]~', '', $fileName)) . '.' . $file->extension();
                $filePath = 'employees/files/' . $name;
                $path = Storage::disk('s3')->put($filePath, $finalFile, 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $employeeFileData = [
                'name' => $fileName,
                'url' => $urlPath,
                'content_type' => $contentType,
                'employee_id' => $employeeId,
            ];

            // Store employee
            $file = EmployeeFile::create($employeeFileData);

            // Commit transaction
            DB::commit();

            // Return success response
            return response()->json([
                'message' => 'Pegawai telah tersimpan',
                'data' => $file,
            ]);
        } catch (\Throwable $e) {
            // Rollback transaction
            DB::rollBack();
            // Return error response
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
            // Validation
            $validated = $request->validate([
                'name' => 'required|max:255',
                'file' => 'max:5120',
                'employee_id' => 'required|integer',
            ]);

            $fileName = $request->name;
            $employeeId = $request->employee_id;

            $employee = Employee::find($employeeId);
            if ($employee == null) {
                throw new Error('Pegawai tidak ditemukan');
            }

            $file = EmployeeFile::find($id);

            // Photo
            $filePath = null;
            $urlPath = $file->url;
            $contentType = $file->content_type;
            $oldFile = $file->url;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $contentType = $file->getMimeType();

                $imagesMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                $finalFile = file_get_contents($file);

                if (in_array($contentType, $imagesMimeTypes)) {
                    // Compress Image
                    $imageWidth = Image::make($file->getRealPath())->width();
                    $ratio = 50 / 100;
                    $newWidth = floor($imageWidth * $ratio);
                    $finalFile = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->stream();
                }

                // Uploading Image
                $name = time() . '_' . $employeeId . '_' . ucwords(preg_replace('~[^A-Za-z0-9]~', '', $fileName)) . '.' . $file->extension();
                $filePath = 'employees/files/' . $name;
                $path = Storage::disk('s3')->put($filePath, $finalFile, 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');

                // Delete old file
                $oldFilePath = parse_url($oldFile);
                //return $file_path['path'];
                if (isset($oldFilePath['path'])) {
                    Storage::disk('s3')->delete($oldFilePath['path']);
                }
            }

            $employeeFileData = [
                'name' => $fileName,
                'url' => $urlPath,
                'content_type' => $contentType,
                'employee_id' => $employeeId,
            ];

            // Store employee
            $file = EmployeeFile::where('id', $id)->update($employeeFileData);

            // Commit transaction
            DB::commit();

            // Return success response
            return response()->json([
                'message' => 'Pegawai telah tersimpan',
                'data' => $file,
            ]);
        } catch (\Throwable $e) {
            // Rollback transaction
            DB::rollBack();
            // Return error response
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
        DB::beginTransaction();
        try {
            $file = EmployeeFile::findOrFail($id);
            $oldFile = $file->url;
            $file->delete();

            // Delete URL
            $oldFilePath = parse_url($oldFile);
            //return $file_path['path'];
            if (isset($oldFilePath['path'])) {
                Storage::disk('s3')->delete($oldFilePath['path']);
            }

            DB::commit();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }
}
