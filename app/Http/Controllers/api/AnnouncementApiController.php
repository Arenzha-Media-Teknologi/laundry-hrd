<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnnouncementApiController extends Controller
{
    public function getAll()
    {
        try {
            $companies = Company::all();
            $companyId = request()->query('company_id');
            $announcements = Announcement::with(['createdByEmployee'])
                ->where('start_date', '<=', Carbon::parse(date('Y-m-d H:i:s'))->toDateTimeString())
                ->where('end_date', '>=', Carbon::parse(date('Y-m-d H:i:s'))->toDateTimeString())
                // ->where('end_date', '<=', date('Y-m-d H:i:s'))
                ->orderBy('created_at', 'DESC')
                ->get()
                ->filter(function ($announcement) use ($companyId) {
                    $companyIds = json_decode($announcement->company_ids);
                    return $announcement->is_all_companies == 1 || in_array($companyId, $companyIds);
                })
                ->each(function ($announcement) use ($companies) {
                    if ($announcement->is_all_companies == 1) {
                        $announcement->companies = ['Semua Perusahaan'];
                    } else {
                        $announcementCompaniesIds = json_decode($announcement->company_ids);
                        $announcementCompanies = collect($companies)->whereIn('id', $announcementCompaniesIds)->pluck('name')->all();
                        $announcement->companies = $announcementCompanies;
                    }

                    $content = strip_tags($announcement->content);
                    $content = html_entity_decode($content);
                    $content = str_replace("\r\n", "", $content);
                    if (strlen($content) > 255) {
                        $content = substr($content, 0, 253) . '...';
                    }
                    $announcement->content = $content;
                });

            return response()->json([
                'data' => $announcements
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
