<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaffAuthController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(StudentAuthController::class)->group(function () {
    Route::group(['middleware' => ['auth:sanctum', 'student']], function () {
        Route::post('/student/logout', 'logout');
    });
    Route::post('/student/register', 'register');
    Route::post('/student/login', 'login');
    Route::get('/student/chart-student', 'chartStudent');
    Route::get('/student/get-student/{id}', 'studentGet');

});


Route::controller(StaffAuthController::class)->group(function () {
    Route::group(['middleware' => ['auth:sanctum', 'staff']], function () {
        Route::post('/staff/logout', 'logout');
    });
    Route::post('/staff/register', 'register');
    Route::post('/staff/login', 'login');
    Route::get('/staff/chart-staff', 'chartStaff');
    Route::post('/staff/evaluate', 'saveEvaluation');
    Route::post('/staff/staff-requirement', 'staffRequirement');
    Route::post('/staff/student-requirement', 'studentRequirement');
    Route::get('/staff/staff-requirement', 'showStaff');
    Route::get('/staff/students-requirement', 'showStudent');
    Route::post('/staff/students-done-requirement', 'studentDone');
    Route::put('staff/evaluate/{id}', 'studentEvaluation');
    Route::post('/staff/students-update-requirement', 'studentUpdateEvaluation');
});


Route::controller(AdminAuthController::class)->group(function () {
    Route::group(['middleware' => ['auth:sanctum', 'admin']], function () {
        Route::post('/admin/logout', 'logout');
    });
    Route::post('/admin/register', 'register');
    Route::post('/admin/login', 'login');
    Route::post('/staff/add-staff', 'addStaff');
    Route::get('/staff/show-staff', 'showStaff');
    Route::get('/student/show-student', 'showStudent');
    Route::post('/student/add-student', 'addStudent');
    Route::get('/student/update-student/{id}', 'studentUpdate');
    Route::get('/staff/update-staff/{id}', 'staffUpdate');
    Route::post('/staff/update-staff/{id}', 'finalStaffUpdate');
    Route::post('/student/update-student/{id}', 'finalStudentUpdate');
    Route::delete('/student/delete-student/{id}', 'studentDelete');
    Route::delete('/staff/delete-staff/{id}', 'staffDelete');
    Route::post('/admin/update-admin', 'adminUpdate');

});


Route::post('/forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm']);
Route::get('/reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm']);

Route::post('staff/forget-password', [ForgotPasswordController::class, 'submitForgetPasswordFormStaff']);
Route::get('/staff/reset-password', [ForgotPasswordController::class, 'submitResetPasswordFormStaff']);

Route::post('student/forget-password', [ForgotPasswordController::class, 'submitForgetPasswordFormStudent']);
Route::get('/student/reset-password', [ForgotPasswordController::class, 'submitResetPasswordFormStudent']);