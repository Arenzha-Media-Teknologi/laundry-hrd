<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Mail\PayrollBcaEmailSent;
use App\Models\Office;
use App\Models\PayrollBcaEmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PayrollBcaEmailLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $offices = Office::with(['division.company'])->get();
        $logs = PayrollBcaEmailLog::with(['createdByEmployee'])->orderBy('created_at', 'DESC')->get();

        return view('payroll-bca-email-log.index', [
            // 'offices' => $offices,
            'logs' => $logs,
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
        //
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
        //
    }

    public function sendEmailView()
    {
        return view('payroll-bca-email-log.send-email');
    }

    public function sendEmail(Request $request)
    {
        DB::beginTransaction();
        try {
            $to = $request->to;
            $sender = $request->sender;
            $subject = $request->subject;
            $content = $request->content;
            // $createdBy = Auth::id();
            $createdBy = Auth::user()->employee->id ?? null;

            // return request()->all();

            $mailAttachments = [];

            $transactionFilePath = null;
            $transactionFileName = null;
            if ($request->hasFile('transaction_file')) {
                $file = $request->file('transaction_file');
                $transactionFileName = $file->getClientOriginalName();
                // Mail Attachment
                $mailAttachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'content' => file_get_contents($file),
                    'mime' => $file->getClientMimeType(),
                ];
                // AWS S3
                $name = time() . '_' . $file->getClientOriginalName();
                $filePath = 'payroll-bca/transaction-files/' . $name;
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $transactionFilePath = Storage::disk('s3')->url($filePath);
            }

            $checksumFilePath = null;
            $checksumFileName = null;
            if ($request->hasFile('checksum_file')) {
                $file = $request->file('checksum_file');
                $checksumFileName = $file->getClientOriginalName();

                // Mail Attachment
                $mailAttachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'content' => file_get_contents($file),
                    'mime' => $file->getClientMimeType(),
                ];

                // AWS S3
                $name = time() . '_' . $file->getClientOriginalName();
                $filePath = 'payroll-bca/checksum-files/' . $name . $file->getExtension();
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $checksumFilePath = Storage::disk('s3')->url($filePath);
            }

            // return $mailAttachments;

            Mail::to($to)->send(new PayrollBcaEmailSent($subject, $content, $mailAttachments));

            $payrollBcaEmailLog = new PayrollBcaEmailLog;
            $payrollBcaEmailLog->to = $to;
            $payrollBcaEmailLog->sender = $sender;
            $payrollBcaEmailLog->subject = $subject;
            $payrollBcaEmailLog->transaction_file_name = $transactionFileName;
            $payrollBcaEmailLog->checksum_file_name = $checksumFileName;
            $payrollBcaEmailLog->transaction_file = $transactionFilePath;
            $payrollBcaEmailLog->checksum_file = $checksumFilePath;
            $payrollBcaEmailLog->content = $content;
            $payrollBcaEmailLog->created_by = $createdBy;
            $payrollBcaEmailLog->save();

            DB::commit();
            return response()->json([
                'message' => 'Email berhasil dikirim',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
