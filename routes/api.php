<?php

use App\Http\Controllers\api\ArrController;
use App\Http\Controllers\api\ArrRiskController;
use App\Http\Controllers\api\CommentController;
use App\Http\Controllers\api\DashboardController;
use App\Http\Controllers\api\EaiController;
use App\Http\Controllers\api\HiraController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// User API CALLS
Route::post('addUser', [UserController::class, 'addUser']);

Route::get('getRoles', [UserController::class, 'getRoles']);

Route::get('getUsers', [UserController::class, 'getUsers']);

Route::put('updateRoles', [UserController::class, 'updateRoles']);

Route::post('createOrUpdateUser', [UserController::class, 'createOrUpdateUser']);

Route::get('checkRoles/{user_name}', [UserController::class, 'checkRoles']);
Route::get('checkRoles/{user_name}', [UserController::class, 'checkRoles']);
Route::get('getCreators/{creator_id}', [UserController::class, 'getCreators']);

// HIRA API CALLS
Route::get('getDocumentNumber', [HiraController::class, 'getDocumentNumber']);
Route::get('getHiraForms', [HiraController::class, 'getHiraForms']);
Route::get('getHira', [HiraController::class, 'getHira']);
Route::get('getComment', [CommentController::class, 'getComment']);
Route::post('addHira', [HiraController::class, 'addHira']);
Route::post('addComment', [CommentController::class, 'addComment']);
Route::post('addNewField', [HiraController::class, 'addNewField']);
Route::put('changeStatus', [HiraController::class, 'changeStatus']);
Route::delete('deleteField', [HiraController::class, 'deleteField']);

// EAI API CALLS
Route::get('getEAIDocumentNumber', [EaiController::class, 'getDocumentNumber']);
Route::post('addEai', [EaiController::class, 'addEai']);
Route::get('getEai', [EaiController::class, 'getEai']);

// ARR API calls
Route::get('getARRDocumentNumber', [ArrController::class, 'getDocumentNumber']);
Route::post('addAssetDetails', [ArrController::class, 'addAssetDetails']);
Route::post('addRiskDetails', [ArrController::class, 'addRiskDetails']);
Route::get('getArrRisks', [ArrController::class, 'getArrRisks']);
Route::get('getSpecificFunction', [ArrController::class, 'getSpecificFunction']);

// Dashboard API CALLS
Route::get('getFilterParam', [DashboardController::class, 'getFilterParam']);
Route::get('filterDashboard', [DashboardController::class, 'filterDashboard']);
Route::post('generateReport', [DashboardController::class, 'generateReport']);



