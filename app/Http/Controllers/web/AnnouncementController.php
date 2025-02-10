<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Company;
use App\Models\Division;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::all();
        $announcements = Announcement::with(['createdByEmployee'])
            ->orderBy('created_at', 'DESC')
            ->get()
            ->each(function ($announcement) use ($companies) {
                if ($announcement->is_all_companies == 1) {
                    $announcement->companies = ['Semua Perusahaan'];
                } else {
                    $announcementCompaniesIds = json_decode($announcement->company_ids);
                    $announcementCompanies = collect($companies)->whereIn('id', $announcementCompaniesIds)->pluck('name')->all();
                    $announcement->companies = $announcementCompanies;
                }
            });

        return view('announcements.index', [
            'announcements' => $announcements,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $divisions = Division::all();
        $companies = Company::all();

        return view('announcements.create', [
            'divisions' => $divisions,
            'companies' => $companies,
        ]);
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
            $validator = Validator::make(request()->all(), [
                'title' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'content' => 'required|string',
            ], [
                'required' => ':attribute harus diisi',
                'date' => ':attribute bukan format tanggal yang valid',
                'string' => ':attribute bukan format teks yang valid',
            ], [
                'title' => 'judul',
                'start_date' => 'tanggal awal tampil',
                'end_date' => 'tanggal akhir tampil',
                'content' => 'isi',
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                $validationMessage = collect($validator->messages())->flatten()->join(', ');
                return response()->json([
                    'message' => $validationMessage,
                ], 400);
            }

            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');

                $filePath = 'announcements/attachments/' . $file->getClientOriginalName();
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $newAnnouncement = new Announcement;
            $newAnnouncement->title = $request->title;
            $newAnnouncement->start_date = $request->start_date;
            $newAnnouncement->end_date = $request->end_date;
            $newAnnouncement->content = $request->content;
            $newAnnouncement->attachment = $urlPath;
            $newAnnouncement->is_all_companies = $request->is_all_companies;
            $newAnnouncement->company_ids = $request->company_ids;
            $newAnnouncement->created_by = Auth::user()->employee->id ?? null;
            $newAnnouncement->save();

            DB::commit();
            return response()->json([
                'message' => 'Pengumuman berhasil disimpan'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
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
        $announcement = Announcement::findOrFail($id);
        $divisions = Division::all();
        $companies = Company::all();

        return view('announcements.edit', [
            'announcement' => $announcement,
            'divisions' => $divisions,
            'companies' => $companies,
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
            $validator = Validator::make(request()->all(), [
                'title' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'content' => 'required|string',
            ], [
                'required' => ':attribute harus diisi',
                'date' => ':attribute bukan format tanggal yang valid',
                'string' => ':attribute bukan format teks yang valid',
            ], [
                'title' => 'judul',
                'start_date' => 'tanggal awal tampil',
                'end_date' => 'tanggal akhir tampil',
                'content' => 'isi',
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                $validationMessage = collect($validator->messages())->flatten()->join(', ');
                return response()->json([
                    'message' => $validationMessage,
                ], 400);
            }

            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');

                $filePath = 'announcements/attachments/' . $file->getClientOriginalName();
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $newAnnouncement = Announcement::find($id);
            $newAnnouncement->title = $request->title;
            $newAnnouncement->start_date = $request->start_date;
            $newAnnouncement->end_date = $request->end_date;
            $newAnnouncement->content = $request->content;
            if (!empty($urlPath)) {
                $newAnnouncement->attachment = $urlPath;
            }
            $newAnnouncement->is_all_companies = $request->is_all_companies;
            $newAnnouncement->company_ids = $request->company_ids;
            $newAnnouncement->created_by = Auth::user()->employee->id ?? null;
            $newAnnouncement->save();

            DB::commit();
            return response()->json([
                'message' => 'Pengumuman berhasil disimpan'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
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
            $announcement = Announcement::find($id);
            $announcement->delete();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }

    public function detail($id)
    {
        $announcement = Announcement::with(['createdByEmployee'])->find($id);
        // return $announcement;
        return view('announcements.detail', [
            'announcement' => $announcement,
        ]);
    }
}
