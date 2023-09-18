<?php

use App\Http\Controllers\api\CommentController;
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

// User API CALSS
Route::post('addUser', [UserController::class, 'addUser']);

Route::get('getRoles', [UserController::class, 'getRoles']);

Route::get('getUsers', [UserController::class, 'getUsers']);

Route::put('updateRoles', [UserController::class, 'updateRoles']);

Route::post('createOrUpdateUser', [UserController::class, 'createOrUpdateUser']);

Route::get('checkRoles/{user_name}', [UserController::class, 'checkRoles']);

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


