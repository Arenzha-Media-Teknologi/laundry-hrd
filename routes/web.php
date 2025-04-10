<?php

use App\Http\Controllers\web\AccessRoleController;
use App\Http\Controllers\web\ActivityController;
use App\Http\Controllers\web\AnnouncementController;
use App\Http\Controllers\web\AttendanceController;
use App\Http\Controllers\web\AuthController;
use App\Http\Controllers\web\BankAccountController;
use App\Http\Controllers\web\BpjsValueController;
use App\Http\Controllers\web\CareerController;
use App\Http\Controllers\web\CompanyController;
use App\Http\Controllers\web\CredentialGroupController;
use App\Http\Controllers\web\DailySalaryController;
use App\Http\Controllers\web\DashboardController;
use App\Http\Controllers\web\DepartmentController;
use App\Http\Controllers\web\DesignationController;
use App\Http\Controllers\web\DivisionController;
use App\Http\Controllers\web\EmployeeController;
use App\Http\Controllers\web\EmployeeFileController;
use App\Http\Controllers\web\EventCalendarsController;
use App\Http\Controllers\web\InsuranceController;
use App\Http\Controllers\web\JobTitleController;
use App\Http\Controllers\web\LeaveApplicationController;
use App\Http\Controllers\web\LeaveCategoryController;
use App\Http\Controllers\web\LeaveController;
use App\Http\Controllers\web\LoanController;
use App\Http\Controllers\web\OfficeController;
use App\Http\Controllers\web\PayrollBcaController;
use App\Http\Controllers\web\PayrollController;
use App\Http\Controllers\web\PayrollSettingController;
use App\Http\Controllers\web\PermissionApplicationController;
use App\Http\Controllers\web\PrivateInsuranceController;
use App\Http\Controllers\web\PrivateInsuranceValueController;
use App\Http\Controllers\web\ReportController;
use App\Http\Controllers\web\SalaryComponentController;
use App\Http\Controllers\web\SalaryController;
use App\Http\Controllers\web\SalaryDepositController;
use App\Http\Controllers\web\SalaryIncreaseController;
use App\Http\Controllers\web\SalaryValueController;
use App\Http\Controllers\web\SettingController;
use App\Http\Controllers\web\SickApplicationController;
use App\Http\Controllers\web\StructureController;
use App\Http\Controllers\web\ThrController;
use App\Http\Controllers\web\TimeOffController;
use App\Http\Controllers\web\WorkingPatternController;
use App\Models\AccessRole;
use App\Models\BankAccount;
use App\Models\BpjsValue;
use App\Models\CredentialGroup;
use App\Models\EventCalendar;
use App\Models\JobTitle;
use App\Models\SalaryComponent;
use Illuminate\Support\Facades\Route;
// ------------------------------2024 new update--------------------------------
use App\Http\Controllers\web\AttendancePeriodController;
use App\Http\Controllers\web\AttendanceQuotesController;
use App\Http\Controllers\web\CheckInController;
use App\Http\Controllers\web\IssueSettlementController;
use App\Http\Controllers\web\OvertimeApplicationController;
use App\Http\Controllers\web\OvertimeApplicationV2Controller;
use App\Http\Controllers\web\PayrollBcaEmailLogController;
use App\Http\Controllers\web\WorkScheduleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);


    // Dashboard
    Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
        Route::get('/', 'index');
        Route::get('/need-actions', 'getNeedActions');
        Route::get('/late-attendance-todos', 'getLateAttendanceTodos');
        Route::get('/outside-attendance-todos', 'getOutsideAttendanceTodos');
        Route::get('/running-activity-todos', 'getRunningActivityTodos');
    });

    // Employee Routes
    Route::controller(EmployeeController::class)->prefix('employees')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::get('/create-v2', 'createV2');
        Route::get('/data-completion', 'dataCompletion');
        Route::get('/{id}/detail', 'detail');
        Route::get('/{id}/detail-v2', 'detailV2');
        Route::get('/{id}/careers', 'careers');
        Route::get('/{id}/setting', 'setting');
        Route::get('/{id}/attendances', 'attendances');
        Route::get('/{id}/filtered-attendances', 'getFilteredAttendances');
        Route::get('/{id}/loans', 'loans');
        Route::get('/{id}/timeoffs', 'timeoffs');
        Route::get('/{id}/files', 'files');
        Route::get('/{id}/insurances', 'insurances');
        Route::get('/{id}/salaries', 'salaries');
        Route::get('/{id}/edit', 'edit');
        Route::get('/{id}/edit-v2', 'editV2');
        // Route::post('/', 'store');
        // Route::post('/{id}', 'update');
        // ! DEVELOPMENT ONLY
        Route::post('/', 'storeV2');
        Route::post('/{id}', 'updateV2');
        // ! DEVELOPMENT ONLY
        Route::post('/{id}/deactivate', 'deactivate');
        Route::post('/{id}/activate', 'activate');
        Route::post('/{id}/activate-leave', 'activateLeave');
        Route::post('/{id}/update-npwp', 'updateNpwp');
        Route::post('/{id}/update-working-pattern', 'updateWorkingPattern');
        Route::post('/{id}/update-payroll-setting', 'updatePayrollSetting');
        Route::post('/{id}/update-account', 'updateAccount');
        Route::post('/{id}/update-salary', 'updateSalary');
        Route::post('/{id}/update-bpjs', 'updateBpjs');
        Route::post('/{id}/create-private-insurance', 'createPrivateInsurance');
        Route::post('/{id}/update-private-insurance', 'updatePrivateInsurance');
        Route::post('/{id}/update-is-tracked', 'updateIsTracked');
        Route::delete('/{id}', 'destroy');
        Route::delete('/{id}/delete-private-insurance', 'deletePrivateInsurance');
    });

    // Company Routes
    Route::controller(CompanyController::class)->prefix('companies')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Division Routes
    Route::controller(DivisionController::class)->prefix('divisions')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Office Routes
    Route::controller(OfficeController::class)->prefix('offices')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::get('/move', 'move');
        Route::get('/{id}/edit', 'edit');
        Route::post('/', 'store');
        Route::post('/move', 'doMove');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Structures Routes
    Route::controller(StructureController::class)->prefix('structures')->group(function () {
        Route::get('/', 'index');
    });

    // Department Routes
    Route::controller(DepartmentController::class)->prefix('departments')->group(function () {
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Designation Routes
    Route::controller(DesignationController::class)->prefix('designations')->group(function () {
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Job Title Routes
    Route::controller(JobTitleController::class)->prefix('job-titles')->group(function () {
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Setting
    Route::prefix('settings')->group(function () {
        Route::get('/attendance', [SettingController::class, 'attendance']);
        Route::controller(PayrollSettingController::class)->prefix('payrolls')->group(function () {
            Route::get('/components', 'component');
            Route::get('/members', 'member');
            Route::post('/assign-employees', 'assignEmployees');
        });
    });

    // Salary Component
    Route::controller(SalaryComponentController::class)->prefix('salary-components')->group(function () {
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Salary Increase
    Route::controller(SalaryIncreaseController::class)->prefix('salary-increases')->group(function () {
        Route::get('/', 'indexV3');
        Route::post('/', 'store');
        // Route::post('/action/increase', 'increase');
    });

    // Salary Value
    Route::controller(SalaryValueController::class)->prefix('salary-values')->group(function () {
        Route::get('/', 'indexV3');
        Route::post('/', 'store');
    });

    // THR
    Route::controller(ThrController::class)->prefix('thr')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::post('/', 'store');
    });

    // Insurance
    Route::controller(InsuranceController::class)->prefix('insurances')->group(function () {
        Route::get('/create', 'createV2');
        Route::prefix('/export')->group(function () {
            Route::get('/', 'exportReport');
        });
        Route::post('/', 'store');
        // Route::post('/action/increase', 'increase');
    });

    // Career
    Route::controller(CareerController::class)->prefix('careers')->group(function () {
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Attendance
    Route::controller(AttendanceController::class)->prefix('attendances')->group(function () {
        Route::get('/', 'index');
        Route::get('/action/upload', 'upload');
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::post('/action/do-upload', 'doUploadFromMachineAppExperiment');
        Route::delete('/{id}', 'destroy');
    });

    // Working Pattern
    Route::controller(WorkingPatternController::class)->prefix('working-patterns')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::get('/{id}/edit', 'edit');
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Daily Salary
    Route::controller(DailySalaryController::class)->prefix('daily-salaries')->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}/edit', 'edit');
        Route::get('/{id}/print', 'print');
        Route::get('/action/generate', 'generate');
        Route::get('/action/generate-aerplus', 'generateAerplus');
        Route::post('/pay', 'pay');
        Route::post('/action/bulk-save', 'bulkSave');
        Route::post('/action/bulk-save-aerplus', 'bulkSaveAerplus');
        Route::delete('/{id}/delete', 'destroy');
        // Route::delete('/{id}/delete-aerplus', 'destroyAerplus');
        Route::delete('/action/bulk-delete', 'bulkDestroy');
        // Route::delete('/action/bulk-delete-aerplus', 'bulkDestroyAerplus');
        Route::prefix('/export')->group(function () {
            Route::get('/aerplus-report', 'exportAerplusReport');
            Route::get('/aerplus-summary-report', 'exportAerplusSummaryReport');
            Route::get('/magenta-report', 'exportMagentaReport');
        });
    });

    // Payroll
    Route::controller(PayrollController::class)->prefix('payrolls')->group(function () {
        Route::get('/', 'index');
        Route::get('/monthly', 'indexMonthly');
        Route::get('/monthly/payment', 'monthlyPayment');
        Route::get('/monthly/test', 'indexMonthlyTest');
        Route::get('/daily', 'indexDaily');
        Route::get('/daily/generate', 'generateDaily');
        Route::get('/daily/show-by-period', 'showByPeriodDaily');
        Route::get('/daily-aerplus', 'indexDailyAerplus');
        Route::get('/daily-aerplus/generate', 'generateDailyAerplus');
        Route::get('/daily-aerplus/show-by-period', 'showByPeriodDailyAerplus');
        Route::post('/daily/save-setting', 'updateMagentaDailySalarySetting');
    });

    // Payroll BCA
    Route::controller(PayrollBcaController::class)->prefix('payroll-bca')->group(function () {
        Route::get('/', 'index');
        Route::get('/monthly', 'monthly');
        Route::get('/monthly/multi-payroll-template', 'exportMultiPayrollTemplate');
        Route::get('/monthly/export-transaction-txt', 'exportTransactionTxt');
        Route::get('/daily-magenta', 'dailyMagenta');
        Route::get('/daily-magenta/multi-payroll-template', 'multiPayrollTemplateDailyMagenta');
        Route::get('/daily-magenta/export-transaction-txt', 'transactionTxtDailyMagenta');
        Route::get('/daily-aerplus', 'dailyAerplus');
        Route::get('/daily-aerplus/multi-payroll-template', 'multiPayrollTemplateDailyAerplus');
        Route::get('/daily-aerplus/export-transaction-txt', 'transactionTxtDailyAerplus');
    });

    // Salary 
    Route::controller(SalaryController::class)->prefix('salaries')->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}/edit', 'edit');
        Route::get('/{id}/print', 'printV2');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
        Route::delete('/action/bulk-delete', 'bulkDestroy');
        Route::prefix('/action')->group(function () {
            Route::get('/generate', 'generate');
            Route::post('/bulk-save', 'bulkSave');
            Route::post('/pay', 'pay');
            Route::post('/unpaid', 'unpaid');
        });
        Route::prefix('/export')->group(function () {
            Route::get('/report/monthly', 'exportMonthlyReport');
        });
    });

    // Loan
    Route::controller(LoanController::class)->prefix('loans')->group(function () {
        Route::get('/', 'indexV2');
        Route::get('/create', 'create');
        Route::get('/resources/get-loans-by-month', 'getLoansByMonth');
        Route::get('/datatable/loans', 'datatableLoans');
        Route::get('/{id}/edit', 'edit');
        Route::get('/{id}/detail', 'detail');
        Route::post('/', 'store');
        Route::post('/action/bulk-hold', 'bulkHold');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Bank Accounts
    Route::controller(BankAccountController::class)->prefix('bank-accounts')->group(function () {
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Sick Application
    Route::controller(SickApplicationController::class)->prefix('sick-applications')->group(function () {
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::post('/{id}/approve', 'approve');
        Route::post('/{id}/reject', 'reject');
        Route::delete('/{id}', 'destroy');
    });

    // Permission Application
    Route::controller(PermissionApplicationController::class)->prefix('permission-applications')->group(function () {
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Leave Application
    Route::controller(LeaveApplicationController::class)->prefix('leave-applications')->group(function () {
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::post('/{id}/approve', 'approve');
        Route::post('/{id}/reject', 'reject');
        Route::delete('/{id}', 'destroy');
    });

    // Time Off
    Route::controller(TimeOffController::class)->prefix('time-offs')->group(function () {
        Route::get('/', 'indexV2');
        Route::get('/create', 'create');
        Route::get('/{id}/edit', 'edit');
        Route::prefix('datatables')->group(function () {
            Route::get('/sick-applications', 'datatableSickApplications');
            Route::get('/leave-applications', 'datatableLeaveApplications');
            Route::get('/event-leave-applications', 'datatableEventLeaveApplications');
        });
    });

    // Leave
    Route::controller(LeaveController::class)->prefix('leaves')->group(function () {
        Route::get('/', 'index');
        Route::post('/reset', 'reset');
        Route::post('/{id}', 'update');
    });

    // Leave
    Route::controller(EmployeeFileController::class)->prefix('employee-files')->group(function () {
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Roles
    Route::controller(AccessRoleController::class)->prefix('access-roles')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::get('/{id}/edit', 'edit');
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Event Calendar
    Route::controller(EventCalendarsController::class)->prefix('event-calendars')->group(function () {
        Route::get('/', 'index');
        // Route::get('/create', 'create');
        // Route::get('/{id}/edit', 'edit');
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Reports
    Route::controller(ReportController::class)->prefix('reports')->group(function () {
        Route::get('/', 'index');
        Route::prefix('/employees')->group(function () {
            Route::get('/all', 'employees');
            Route::get('/all-export', 'employeesExport');
        });
        Route::prefix('/leaves')->group(function () {
            Route::get('/all', 'leaves');
            Route::get('/by-employee', 'leavesByEmployee');
        });
        Route::prefix('/salaries')->group(function () {
            Route::get('/monthly', 'monthlySalaries');
            Route::get('/monthly-export', 'monthlySalariesExport');
            Route::get('/thr', 'thrV2');
            Route::get('/thr-export', 'thrExportV2');
            Route::get('/taxes', 'taxes');
            Route::get('/deposits', 'deposits');
            Route::get('/deposits-export', 'depositsExport');
            Route::get('/leaves', 'leavePayroll');
            Route::get('/leaves-export', 'leavePayrollExport');
        });
        Route::prefix('/attendances')->group(function () {
            Route::get('/all', 'attendances');
            Route::get('/all-export', 'attendancesExport');
            Route::get('/by-employee', 'attendancesByEmployee');
            Route::get('/by-employee-export', 'attendancesByEmployeeExport');
            Route::get('/absences', 'absences');
            Route::get('/absences-export', 'absencesExport');
        });
        Route::prefix('/insurances')->group(function () {
            Route::get('/bpjs-ketenagakerjaan', 'bpjsKetenagakerjaan');
            Route::get('/bpjs-ketenagakerjaan-export', 'bpjsKetenagakerjaanExport');
            Route::get('/bpjs-mandiri', 'bpjsMandiri');
            Route::get('/bpjs-mandiri-export', 'bpjsMandiriExport');
            Route::get('/insurances-list', 'insurancesList');
            Route::get('/insurances-list-export', 'insurancesListExport');
            Route::get('/privates/{id}', 'privateInsurances');
            Route::get('/privates/{id}/export', 'privateInsurancesExport');
        });
        // Route::prefix('/attendances-period')->group(function () {
        //     Route::get('/all', 'attendancesPeriod');
        // });
    });

    // Private Insurance Routes
    Route::controller(PrivateInsuranceController::class)->prefix('private-insurances')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // BPJS Values Routes
    Route::controller(BpjsValueController::class)->prefix('bpjs-values')->group(function () {
        // Route::get('/', 'index');
        Route::post('/', 'store');
        // Route::post('/{id}', 'update');
        // Route::delete('/{id}', 'destroy');
    });

    // Private Insurances Values Routes
    Route::controller(PrivateInsuranceValueController::class)->prefix('private-insurance-values')->group(function () {
        // Route::get('/', 'index');
        Route::post('/', 'store');
        // Route::post('/{id}', 'update');
        // Route::delete('/{id}', 'destroy');
    });

    // Leave Categories
    Route::controller(LeaveCategoryController::class)->prefix('leave-categories')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Credential Group
    Route::controller(CredentialGroupController::class)->prefix('credential-groups')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::get('/{id}/edit', 'edit');
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Credential Group
    Route::controller(SalaryDepositController::class)->prefix('salary-deposits')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::get('/report', 'report');
        Route::get('/{id}/edit', 'edit');
        Route::get('/{id}/detail', 'detail');
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::post('/{id}/redeem', 'redeemDeposit');
        Route::delete('/{id}', 'destroy');
    });

    // Credential Group
    // Route::controller(AuthController::class)->group(function () {
    //     Route::post('/logout', 'logout');
    // });

    // 2024 new update
    // attendancePeriod
    Route::controller(AttendancePeriodController::class)->prefix('reports/attendances-period')->group(function () {
        Route::get('/', 'index');
    });
    // attendancePeriod
    Route::controller(PayrollBcaEmailLogController::class)->prefix('payroll-bca-email-log')->group(function () {
        Route::get('/', 'index');
        Route::get('/send-email', 'sendEmailView');
        Route::post('/send-email', 'sendEmail');
    });

    // Announcement
    Route::controller(AnnouncementController::class)->prefix('announcements')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        // Route::get('/{id}/detail', 'detail');
        Route::get('/{id}/edit', 'edit');
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Check In
    Route::controller(CheckInController::class)->prefix('checkins')->group(function () {
        Route::get('/detail', 'detail');
    });

    // Activity
    Route::controller(ActivityController::class)->prefix('activities')->group(function () {
        Route::get('/detail', 'detail');
    });

    // Activity
    Route::controller(OvertimeApplicationController::class)->prefix('overtime-applications')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
    });

    // Overtime Application V2
    Route::controller(OvertimeApplicationV2Controller::class)->prefix('overtime-applications-v2')->group(function () {
        Route::get('/', 'index');
        Route::get('/number', 'number');
        Route::get('/create', 'create');
        Route::get('/{id}/edit', 'edit');
        Route::get('/{id}/confirmation', 'confirmation');
        Route::get('/{id}/print', 'print');
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::post('/{id}/confirm', 'confirm');
    });

    // Quotes
    Route::controller(AttendanceQuotesController::class)->prefix('attendance-quotes')->group(function () {
        Route::post('/', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Issue Settlement
    Route::controller(IssueSettlementController::class)->prefix('issue-settlements')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::delete('/{id}', 'destroy');
    });

    // Work Schedule
    Route::controller(WorkScheduleController::class)->prefix('work-schedules')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::get('/action/generate-schedule', 'generate');
        Route::get('/action/generate-schedule-edit', 'generateEditData');
        Route::get('/export/by-employee', 'exportByEmployee');
        Route::get('/export/by-office', 'exportByOffice');
        Route::get('/{id}/detail', 'show');
        Route::get('/{id}/edit', 'edit');
        Route::post('/store', 'store');
        Route::post('/{id}/update', 'update');
        Route::delete('/{id}', 'destroy');
    });
});
// Credential Group
// Route::controller(AuthController::class)->group(function () {
// });

// Credential Group
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'loginPage')->middleware('guest')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
});

Route::controller(AnnouncementController::class)->prefix('announcements')->group(function () {
    Route::get('/{id}/detail', 'detail');
});
