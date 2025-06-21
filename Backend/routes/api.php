<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use Illuminate\Http\Request;




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

Route::prefix('members')->group(function () {
    Route::get('/', [MemberController::class, 'index']);
    Route::get('/{id}', [MemberController::class, 'show']);
    Route::post('/create', [MemberController::class, 'create']);
    Route::put('/update/{id}', [MemberController::class, 'update']);
    Route::delete('delete/{id}', [MemberController::class, 'delete']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
