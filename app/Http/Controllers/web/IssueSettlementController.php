<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\CompanyBusinessType;
use App\Models\Employee;
use App\Models\IssueSettlement;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IssueSettlementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $offices = Office::with(['division.company'])->get();
        $issueSettlements = IssueSettlement::with(['issueSettlementable' => function ($q) {
            $q->with(['employee']);
        }, 'createdByEmployee'])->get();

        return view('issue-settlements.index', [
            'issue_settlements' => $issueSettlements,
            'offices' => $offices,
        ]);
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
            $note = $request->note;
            $source = $request->source;
            $sourceId = $request->source_id;
            $type = "";

            $issueSettlementable = null;
            $authId = Auth::id();

            if ($source == "late_attendance" || $source == "outside_attendance") {
                $issueSettlementable = Attendance::find($sourceId);
            } else if ($source == "running_activity") {
                $issueSettlementable = Activity::find($sourceId);
            }

            $issueSettlement = new IssueSettlement([
                'type' => $source,
                'note' => $note,
                'created_by' => $authId,
            ]);

            if (isset($issueSettlementable)) {
                $issueSettlementable->issueSettlements()->save($issueSettlement);
            }

            DB::commit();

            return response()->json([
                'message' => 'Konfirmasi berhasil disimpan',
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
        //
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
            $issueSettlement = IssueSettlement::find($id);
            $issueSettlement->delete();

            return response()->json([
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
