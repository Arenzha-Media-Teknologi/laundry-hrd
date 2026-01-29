<?php

use App\Http\Controllers\api\ActivityApiController;
use App\Http\Controllers\api\AnnouncementApiController;
use App\Http\Controllers\api\AttendanceApiController;
use App\Http\Controllers\api\AuthApiController;
use App\Http\Controllers\api\CheckInApiController;
use App\Http\Controllers\api\EmployeeApiController;
use App\Http\Controllers\api\GlobalApiController;
use App\Http\Controllers\api\InspectionApiController;
use App\Http\Controllers\api\LeaveApplicationApiController;
use App\Http\Controllers\api\LeaveCategoryApiController;
use App\Http\Controllers\api\OfficeApiController;
use App\Http\Controllers\api\OutletOpeningApiController;
use App\Http\Controllers\api\PermissionApplicationApiController;
use App\Http\Controllers\api\PermissionCategoryApiController;
use App\Http\Controllers\api\PermissionNoteApiController;
use App\Http\Controllers\api\SickApplicationApiController;
use App\Http\Controllers\api\WorkingPatternApiController;
use App\Http\Controllers\web\LeaveCategoryController;
use App\Models\LeaveCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Global Routes
Route::controller(GlobalApiController::class)->prefix('global')->group(function () {
    Route::get('/create-employee-resources', 'createEmployeeResources');
    Route::get('/firebase-test', 'firebaseTest');
});

// Employee
Route::controller(EmployeeApiController::class)->prefix('employees')->group(function () {
    Route::get('/', 'index');
    Route::get('/aerplus-bonus-salary', 'aerplusBonusSalaryEmployees');
    Route::get('/birthdays', 'birthdays');
    Route::get('/birthdays-count', 'birthdaysCount');
    Route::get('/{id}', 'show');
    Route::get('/{id}/loans', 'loans');
    Route::get('/{id}/attendances', 'attendances');
    Route::get('/{id}/activities', 'activities');
    Route::get('/{id}/checkins', 'checkIns');
    Route::get('/{id}/attendance-summary', 'attendanceSummary');
    Route::get('/{id}/monthly-attendances', 'monthlyAttendances');
    Route::get('/{id}/sick-applications', 'sickApplications');
    Route::get('/{id}/leave-applications', 'leaveApplications');
    Route::get('/{id}/remaining-leaves', 'getRemainingLeaves');
    Route::get('/{id}/active-leaves', 'getActiveLeave');
    Route::get('/{id}/payslips', 'payslips');
    Route::get('/{id}/remaining-leaves', 'remainingLeaves');
    Route::get('/{id}/insights', 'insights');
    Route::get('/{id}/monthly-work-schedules', 'monthlyWorkSchedules');
    Route::get('/detail-v2/{id}', 'showV2');
    Route::patch('/{id}/change-password', 'changePassword');
    Route::patch('/{id}/edit-account', 'editAccount');
});

// Attendances
Route::controller(AttendanceApiController::class)->prefix('attendances')->group(function () {
    Route::get('/', 'index');
    Route::get('/time-offs', 'getTimeOffs');
    Route::get('/time-offs-count', 'getTimeOffsCount');
    Route::get('/long-shifts', 'getLongShifts');
    Route::get('/long-shifts-v2', 'getLongShiftsV2');
    Route::get('/long-shifts-count', 'getPendingLongShiftsCount');
    Route::get('/permissions', 'getPermissions');
    Route::get('/permissions-count', 'getPendingPermissionsCount');
    Route::get('/overtimes', 'getOvertimes');
    Route::get('/overtimes-count', 'getPendingOvertimesCount');
    Route::get('/{id}', 'show');
    Route::post('/action/clockin', 'clockIn');
    Route::post('/action/clockin-v2', 'clockInV2');
    Route::post('/action/clockin-v3', 'clockInV3');
    Route::post('/action/clockout', 'clockOut');
    Route::post('/action/clockout-v2', 'clockOutV2');
    Route::post('/action/clockout-v3', 'clockOutV3');
    Route::post('/long-shifts/approve-many', 'approveManyLongShift');
    Route::post('/long-shifts/reject-many', 'rejectManyLongShift');
    Route::post('/long-shifts/{id}/approve', 'approveLongShift');
    Route::post('/long-shifts/{id}/reject', 'rejectLongShift');
    Route::post('/overtimes/approve-many', 'approveManyOvertimes');
    Route::post('/overtimes/reject-many', 'rejectManyOvertimes');
    Route::post('/overtimes/{id}/approve', 'approveOvertimes');
    Route::post('/overtimes/{id}/reject', 'rejectOvertimes');
    Route::post('/permissions/approve-many', 'approveManyPermission');
    Route::post('/permissions/reject-many', 'rejectManyPermission');
    Route::post('/permissions/{id}/approve', 'approvePermission');
    Route::post('/permissions/{id}/reject', 'rejectPermission');
});

// Sick Application
Route::controller(SickApplicationApiController::class)->prefix('sick-applications')->group(function () {
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/store-base64', 'storeBase64');
    Route::post('/store-test', 'storeTest');
    Route::post('/{id}', 'update');
    Route::post('/{id}/approve', 'approve');
    Route::post('/{id}/reject', 'reject');
    Route::delete('/{id}', 'destroy');
});

// Sick Application
Route::controller(LeaveApplicationApiController::class)->prefix('leave-applications')->group(function () {
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::post('/{id}/approve', 'approve');
    Route::post('/{id}/reject', 'reject');
    Route::delete('/{id}', 'destroy');
});

// Permission Application
Route::controller(PermissionApplicationApiController::class)->prefix('permission-applications')->group(function () {
    Route::post('/', 'store');
    Route::post('/{id}', 'update');
    Route::post('/{id}/approve', 'approve');
    Route::post('/{id}/reject', 'reject');
});

// Permission Application
Route::controller(AuthApiController::class)->prefix('auth')->group(function () {
    Route::post('/mobile/employee', 'loginEmployee');
    Route::post('/mobile/admin', 'loginAdmin');
    Route::post('/web/dashboard-employee', 'loginDashboardEmployee');
});

// Leave Categories
Route::controller(LeaveCategoryApiController::class)->prefix('leave-categories')->group(function () {
    Route::get('/', 'index');
});

// Working Patterns
Route::controller(WorkingPatternApiController::class)->prefix('working-patterns')->group(function () {
    Route::get('/', 'index');
});

// Ispections
Route::controller(InspectionApiController::class)->prefix('inspections')->group(function () {
    Route::get('/', 'getAll');
    Route::post('/', 'store');
    Route::delete('/{id}', 'destroy');
});

// Office
Route::controller(OfficeApiController::class)->prefix('offices')->group(function () {
    Route::get('/', 'getAll');
    Route::get('/daily-outlet-opening-report', 'getDailyOutletOpeningReport');
    Route::get('/daily-outlet-opening-report/{officeId}', 'getDailyOutletOpeningReportDetail');
    Route::get('/outlet-opening-statistics', 'getOutletOpeningStatistics');
});

// Announcement
Route::controller(AnnouncementApiController::class)->prefix('announcements')->group(function () {
    Route::get('/', 'getAll');
});

// Permission Categories
Route::controller(PermissionCategoryApiController::class)->prefix('permission-categories')->group(function () {
    Route::get('/', 'getAll');
});

// Permission Notes
Route::controller(PermissionNoteApiController::class)->prefix('permission-notes')->group(function () {
    Route::get('/', 'getAll');
});


// Check In
Route::controller(CheckInApiController::class)->prefix('checkins')->group(function () {
    Route::get('/', 'getAll');
    Route::get('/{id}', 'show');
    Route::post('/checkin', 'checkIn');
});

// Activity
Route::controller(ActivityApiController::class)->prefix('activities')->group(function () {
    Route::get('/current-state', 'getCurrentState');
    Route::get('/active-activity', 'getActiveActivity');
    Route::post('/check-in', 'checkIn');
    Route::post('/check-out', 'checkOut');
});

// Outlet Opening
Route::controller(OutletOpeningApiController::class)->prefix('outlet-openings')->group(function () {
    Route::get('/monthly', 'getMonthly');
    Route::get('/{id}', 'getOne');
    Route::post('/', 'store');
    // Route::put('/{id}', 'update');
    Route::post('/{id}/update', 'update');
});



// Route::get('/announcements', function () {
//     return response()->json([
//         'data' => collect(json_decode('[{"id":2,"title":"Judul Pengumuman","start_date":"2024-03-13 00:00:00","end_date":"2024-03-20 00:00:00","content":"<h1>Pengumuman kepada seluruh pegawai untuk tetap tenang<\/h1>\r\n<ul>\r\n<li>Tidak menggunakan sdsdf<\/li>\r\n<li>saasfd ajsdh. ajkhsdk ahskd<\/li>\r\n<li>dasjdh askdha sjhak s<\/li>\r\n<\/ul>","attachment":"https:\/\/magenta-hrd-v2.s3.ap-southeast-1.amazonaws.com\/announcements\/attachments\/HONOR%202023-05-13%20-%202023-05-19%20PT.%20Magenta%20Mediatama%20%281%29.txt","is_all_companies":1,"company_ids":"[]","created_by":15,"created_at":"2024-03-13T03:28:34.000000Z","updated_at":"2024-03-13T03:28:34.000000Z","companies":["Semua Perusahaan"],"created_by_employee":{"id":15,"name":"Marselino Fau","number":"20230001","gender":"male","place_of_birth":"Nias","date_of_birth":"1970-06-08","identity_type":"ktp","identity_number":"3671010806700000","identity_image":"https:\/\/magenta-hrd-v2.s3.ap-southeast-1.amazonaws.com\/employees\/ktp\/1688962073_ktp%20marsel.jpg","driver_license_type":null,"driver_license_number":null,"marital_status":"menikah","religion":"katolik","blood_group":"A","recent_education":"S1","education_institution_name":"Universitas Indonesia","study_program":"Psikologi","email":"marselfau@ymail.com","phone":"81380400130","address":"Jl. Pulau Dewa II P2 \/ 24 Moderland Rt. 005 \/ Rw.002 Kel. Kelapa Indah Kec. Tangerang Kota Tangerang","emergency_contact_name":"Inge","emergency_contact_relation":"Istri","emergency_contact_phone":"81298272675","start_work_date":"2011-10-03","type":"staff","photo":"https:\/\/arenzha.s3.ap-southeast-1.amazonaws.com\/photos\/1630637291-20210902_130843(1).jpg","active":1,"inactive_at":null,"active_at":null,"office_id":1,"magenta_daily_salary":1,"aerplus_daily_salary":0,"aerplus_overtime":0,"npwp_number":"NULL","npwp_effective_date":"NULL","npwp_status":"NULL","npwp_image":null,"is_tracked":0,"created_at":"2021-06-08T21:22:36.000000Z","updated_at":"2023-07-09T21:07:54.000000Z"}}]'))->each(function ($announcement) {
//             $content = strip_tags($announcement->content);
//             $content = html_entity_decode($content);
//             if (strlen($content) > 255) {
//                 $content = substr($content, 0, 253) . '...';
//             }
//             $announcement->content = $content;
//         })
//     ]);
// });
