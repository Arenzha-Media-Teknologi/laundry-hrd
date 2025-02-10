<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\LoanName;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loans = Loan::with(['employee'])->get();

        return view('loans.index', [
            'loans' => $loans,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexV2()
    {
        $loans = Loan::with(['employee'])->get();

        $totalLoans = LoanItem::sum('basic_payment');
        $totalLoansCount = Loan::count();
        $totalPaidLoans = LoanItem::whereHas('salaryItem')->sum('basic_payment');
        $totalPaidLoansCount = LoanItem::whereHas('salaryItem')->count();
        $totalUnpaidLoans = LoanItem::whereDoesntHave('salaryItem')->sum('basic_payment');
        $totalUnpaidLoansCount = LoanItem::whereDoesntHave('salaryItem')->count();

        // return $totalLoans;

        return view('loans.v2.index', [
            'loans' => $loans,
            'statistic' => [
                'total_loans' => $totalLoans,
                'total_loans_count' => $totalLoansCount,
                'total_paid_loans' => $totalPaidLoans,
                'total_paid_loans_count' => $totalPaidLoansCount,
                'total_unpaid_loans' => $totalUnpaidLoans,
                'total_unpaid_loans_count' => $totalUnpaidLoansCount,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::with(['activeCareer.jobTitle', 'office'])->get();
        $loanNames = LoanName::all();

        return view('loans.create', [
            'min_year' => 2020,
            'employees' => $employees,
            'loan_names' => $loanNames,
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
            $effectiveDate = $request->effective_date;
            $amount = $request->amount;
            $installment = $request->installment;
            $employeeId = $request->employee_id;
            $loanNameId = $request->loan_name_id;
            $description = $request->description;
            $items = $request->items;

            $validated = $request->validate([
                'effective_date' => 'required|date',
                'amount' => 'required|numeric',
                'installment' => 'required|integer',
                'employee_id' => 'required|integer',
                'loan_name_id' => 'required|integer',
            ]);

            $itemsAmount = collect($items)->sum('paymentAmount');

            // return response()->json([
            //     'date' => [$itemsAmount, $amount],
            // ]);
            if ($itemsAmount !== (int) $amount) {
                throw new Exception('CODE ERROR: Total item tidak sama dengan jumlah pinjaman');
            }

            $loan = new Loan();
            $loan->effective_date = $effectiveDate;
            $loan->loan_name_id = $loanNameId;
            $loan->employee_id = $employeeId;
            $loan->amount = $amount;
            $loan->installment = $installment;
            $loan->description = $description;
            $loan->save();

            $dataItems = collect($items)->map(function ($item, $index) use ($loan) {
                return [
                    'loan_id' => $loan->id,
                    'installment_order' => $index + 1,
                    'payment_date' => $item['paymentDate'],
                    'basic_payment' => $item['paymentAmount'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];
            })->all();

            DB::table('loan_items')->insert($dataItems);

            DB::commit();

            return response()->json([
                'data' => $loan,
                'message' => 'Data telah tersimpan',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
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
        $loan = Loan::with(['items' => function ($q) {
            $q->withCount(['salaryItem']);
        }])->findOrFail($id);

        // return $loan;
        $totalPaymentAmount = 0;
        $hasSalaryItemLastIndex = -1;
        $loanItems = collect($loan->items)->map(function ($item, $index) use (&$totalPaymentAmount, $loan, &$hasSalaryItemLastIndex) {
            $totalPaymentAmount += $item->basic_payment;
            $remaining = $loan->amount - $totalPaymentAmount;

            if ($item->salary_item_count > 0 || $item->paid == 1) {
                $hasSalaryItemLastIndex = $index;
            }

            return [
                'paid' => $item->paid,
                'paymentDate' => $item->payment_date,
                'paymentAmount' => $item->basic_payment,
                'remaining' => $remaining,
                'salary_item_count' => $item->salary_item_count,
                'editable' => false,
            ];
        })->map(function ($item, $index) use ($hasSalaryItemLastIndex) {
            if ($index > $hasSalaryItemLastIndex) {
                $item['editable'] = true;
            }
            return $item;
        })->all();

        // return $loanItems;
        // return $loanItems;

        $employees = Employee::with(['activeCareer.jobTitle', 'office'])->get();
        $loanNames = LoanName::all();

        return view('loans.edit', [
            'loan' => $loan,
            'loan_items' => $loanItems,
            'min_year' => 2020,
            'employees' => $employees,
            'loan_names' => $loanNames,
            'last_index' => $hasSalaryItemLastIndex,
        ]);
    }

    /**
     * Show specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        $loan = Loan::with(['employee', 'items' => function ($q) {
            $q->withCount(['salaryItem']);
        }])->findOrFail($id);

        // return $loan;
        $totalPaymentAmount = 0;
        $loanItems = collect($loan->items)->map(function ($item, $index) use (&$totalPaymentAmount, $loan) {
            $totalPaymentAmount += $item->basic_payment;
            $remaining = $loan->amount - $totalPaymentAmount;

            $item['remaining'] = $remaining;

            return $item;
        });

        $finalLoans = $loanItems->all();

        $paidItems = $loanItems->filter(function ($item) {
            return $item->salary_item_count > 0 || $item->paid == 1;
        });

        $totalPaid = $paidItems->sum('basic_payment');
        $remaining = $loan->amount - $totalPaid;
        $completion = round(($totalPaid / $loan->amount) * 100);

        return view('loans.detail', [
            'loan' => $loan,
            'loan_items' => $finalLoans,
            'total_paid' => $totalPaid,
            'remaining' => $remaining,
            'completion' => $completion,
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
            $effectiveDate = $request->effective_date;
            $amount = $request->amount;
            $installment = $request->installment;
            $employeeId = $request->employee_id;
            $loanNameId = $request->loan_name_id;
            $description = $request->description;
            $items = $request->items;
            $lastIndex = $request->last_index;

            $validated = $request->validate([
                'effective_date' => 'required|date',
                'amount' => 'required|numeric',
                'installment' => 'required|integer',
                'employee_id' => 'required|integer',
                'loan_name_id' => 'required|integer',
            ]);

            $itemsAmount = collect($items)->sum('paymentAmount');

            if ($itemsAmount !== (int) $amount) {
                throw new Exception('CODE ERROR: Total item tidak sama dengan jumlah pinjaman');
            }

            $loan = Loan::find($id);
            $loan->effective_date = $effectiveDate;
            $loan->loan_name_id = $loanNameId;
            $loan->employee_id = $employeeId;
            $loan->amount = $amount;
            $loan->installment = $installment;
            $loan->description = $description;
            $loan->save();


            $nonEditableItem = collect($items)->where('editable', false)->first();
            $newItems = $items;

            if ($nonEditableItem !== null) {
                $editableItems = collect($items)->where('editable', true)->all();
                $newItems = $editableItems;
                DB::table('loan_items')->where('loan_id', $loan->id)->where('installment_order', '>', $lastIndex + 1)->delete();
            } else {
                DB::table('loan_items')->where('loan_id', $loan->id)->delete();
            }

            $dataItems = collect($newItems)->map(function ($item, $index) use ($loan) {
                return [
                    'loan_id' => $loan->id,
                    'installment_order' => $index + 1,
                    'payment_date' => $item['paymentDate'],
                    'basic_payment' => $item['paymentAmount'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];
            })->all();

            // Delete previous loan items

            // Insert new loan items
            DB::table('loan_items')->insert($dataItems);

            $response = Http::delete(env('AERPLUS_URL') . '/api/v1/journals', [
                'source' => 'MAGENTA_HRD_LOAN',
                'journalable_id' => $id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Data telah tersimpan',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
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
            $loan = Loan::findOrFail($id);
            $loan->delete();

            $response = Http::delete(env('AERPLUS_URL') . '/api/v1/journals', [
                'source' => 'MAGENTA_HRD_LOAN',
                'journalable_id' => $id,
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }

    private function statusColor($status)
    {
        switch ($status) {
            case 'approved':
                return 'light-success';
            case 'pending':
                return 'light-warning';
            case 'rejected':
                return 'light-danger';
            default:
                return 'light';
        }
    }

    private function employeeColumn(Employee $employee)
    {
        $employeeColumn = ' <div class="d-flex align-items-center">';

        if (isset($employee->photo) && !empty($employee->photo)) {
            $employeeColumn .= '
            <div class="symbol symbol-40px symbol-circle">
                <div class="symbol-label" style="background-image:url(\'' . $employee->photo . '\')"></div>
            </div>
            ';
        } else {
            $employeeColumn .= '<div class="symbol symbol-40px symbol-circle">
                <div class="symbol-label fs-2 fw-semibold text-primary">' . substr($employee->name, 0, 1) . '</div>
            </div>';
        }

        $employeeColumn .= '<div class="ps-3">
            <p class="mb-1 fs-7 fw-bolder"><a href="/employees/' . $employee->id . '/detail-v2" class="text-gray-700 text-hover-primary">' . $employee->name . '</a></p>
                <span class="fs-7 text-muted">' . ($employee->office->division->company->name ?? '') . '</span>
        </div>';


        $employeeColumn .= ' </div>';

        return $employeeColumn;
    }

    public function datatableLoans()
    {
        $companyId = request()->query('company_id');
        $approvalStatus = request()->query('status');

        $loans = Loan::with(['items', 'employee' => function ($q) {
            $q->with(['activeCareer.jobTitle', 'office.division.company']);
        }])->orderBy('effective_date', 'DESC')->select('loans.*');

        return DataTables::eloquent($loans)
            ->addColumn('employee', function (Loan $loan) {
                if (isset($loan->employee)) {
                    return $this->employeeColumn($loan->employee);
                }
                return 'NAMA_PEGAWAI';
            })
            ->addColumn('formatted_date', function (Loan $loan) {
                return Carbon::parse($loan->effective_date)->format('d/m/Y');
            })
            ->addColumn('first_installment', function (Loan $loan) {
                $firstInstallmentPaymentDate = collect($loan->items)->first()->payment_date ?? null;
                if (isset($firstInstallmentPaymentDate)) {
                    return '<span class="badge badge-secondary">' . Carbon::parse($firstInstallmentPaymentDate)->locale('id_ID')->isoFormat('LL') . '</span>';
                }
                return '';
            })
            ->addColumn('last_installment', function (Loan $loan) {
                $lastInstallmentPaymentDate = collect($loan->items)->last()->payment_date ?? null;
                if (isset($lastInstallmentPaymentDate)) {
                    return '<span class="badge badge-secondary">' . Carbon::parse($lastInstallmentPaymentDate)->locale('id_ID')->isoFormat('LL') . '</span>';
                }
                return '';
            })
            ->addColumn('formatted_amount', function (Loan $loan) {
                return number_format($loan->amount, 0, ',', '.');
            })
            // ->addColumn('installment', function (Loan $loan) {
            //     return count($loan->items);
            // })
            ->addColumn('status', function (Loan $loan) {
                $totalPaymentAmount = 0;
                $loanItems = collect($loan->items)->map(function ($item, $index) use (&$totalPaymentAmount, $loan) {
                    $totalPaymentAmount += $item->basic_payment;
                    $remaining = $loan->amount - $totalPaymentAmount;

                    $item['remaining'] = $remaining;

                    return $item;
                });

                $paidItems = $loanItems->filter(function ($item) {
                    return $item->salary_item_count > 0 || $item->paid == 1;
                });

                $totalPaid = $paidItems->sum('basic_payment');
                $completion = round(($totalPaid / $loan->amount) * 100);

                if ($completion >= 100) {
                    return '<span class="badge badge-light-success">Lunas</span>';
                }
                // return $completion;
                return '<span class="badge badge-light-warning">Belum Lunas</span>';
            })
            ->addColumn('action', function (Loan $loan) {
                $action = '<div class="d-flex justify-content-end">';
                if (request()->user()->can('view', Loan::class)) {
                    $action .= '
                    <a href="/loans/' . $loan->id . '/detail" class="btn btn-sm btn-icon btn-light ms-2">
                        <span class="svg-icon svg-icon-5 m-0">
                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect y="6" width="16" height="3" rx="1.5" fill="currentColor" />
                                <rect opacity="0.3" y="12" width="8" height="3" rx="1.5" fill="currentColor" />
                                <rect opacity="0.3" width="12" height="3" rx="1.5" fill="currentColor" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </a>
                    ';
                }
                if (request()->user()->can('update', Loan::class)) {
                    $action .= '
                    <a href="/loans/' . $loan->id . '/edit" class="btn btn-sm btn-icon btn-light-info ms-2">
                        <span class="svg-icon svg-icon-5 m-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                            </svg>
                        </span>
                    </a>
                    ';
                }
                if (request()->user()->can('delete', Loan::class)) {
                    $action .= '
                        <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2 btn-delete" data-id="' . $loan->id . '">
                            <span class=" svg-icon svg-icon-5 m-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black" />
                                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black" />
                                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black" />
                                </svg>
                            </span>
                        </button>
                    ';
                }

                $action .= '</div>';

                return $action;
            })
            ->rawColumns(['employee', 'first_installment', 'last_installment', 'status', 'action'])
            // ->addColumn('intro', 'Hi {{$name}}!')
            ->make(true);
    }

    public function getLoansByMonth()
    {
        try {
            $yearMonth = request()->query('year_month');

            if (empty($yearMonth)) {
                return response()->json([
                    'message' => 'Pilih bulan dan tahun',
                ], 400);
            }

            $paymentDate = date('Y-m-d', strtotime($yearMonth . '-25'));

            $loans = Loan::query()->whereHas('items', function ($q) use ($paymentDate) {
                $q->whereDoesntHave('salaryItem')->where('payment_date', $paymentDate);
            })->with(['employee'])->get();

            return response()->json([
                'data' => $loans,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function bulkHold(Request $request)
    {
        // return request()->all();
        DB::beginTransaction();
        try {
            $loanIds = $request->loans_ids;
            $yearMonth = $request->year_month;
            $paymentDate = date('Y-m-d', strtotime($yearMonth . '-25'));

            $selectedLoans = Loan::with(['items'])->whereIn('id', $loanIds)->get()->each(function ($loan) use ($paymentDate) {
                $monthLoanItem = collect($loan->items)->where('payment_date', $paymentDate)->first();

                if (isset($monthLoanItem)) {
                    $lastItem = collect($loan->items)->last();
                    $lastItemPaymentDate = $lastItem->payment_date ?? null;
                    $lastItemInstallmentOrder = $lastItem->installment_order ?? 0;
                    $lastItemBasicPayment = $lastItem->basic_payment ?? 0;
                    if ($lastItemPaymentDate) {
                        LoanItem::where('id', $monthLoanItem->id)->update([
                            'basic_payment' => 0,
                        ]);

                        $newLastPaymentDate = Carbon::parse($lastItemPaymentDate)->addMonth()->format('Y-m-25');

                        Loan::where('id', $loan->id)->update([
                            'installment' => $loan->installment + 1,
                        ]);

                        LoanItem::create([
                            'loan_id' => $loan->id,
                            'installment_order' => $lastItemInstallmentOrder + 1,
                            'payment_date' => $newLastPaymentDate,
                            'basic_payment' => $lastItemBasicPayment,
                        ]);
                        // $loan->new_last_payment = [
                        //     'loan_id' => $loan->id,
                        //     'installment_order' => $lastItemInstallmentOrder + 1,
                        //     'payment_date' => $newLastPaymentDate,
                        //     'basic_payment' => $lastItemBasicPayment,
                        //     'created_at' => Carbon::now()->toDateTimeString(),
                        //     'updated_at' => Carbon::now()->toDateTimeString(),
                        // ];
                    }
                }
            });

            DB::commit();

            return response()->json([
                'message' =>  'Perubahan berhasil disimpan',
                'data' => $selectedLoans,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }
}
