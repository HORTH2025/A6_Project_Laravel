<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
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

Route::get('/books',[BookController::class ,'index']);
Route::get('/books/{id}',[BookController::class ,'show']);
Route::post('/books/create',[BookController::class ,'create']);
Route::put('/books/edit/{id}',[BookController::class ,'edit']);
Route::delete('/books/delete/{id}',[BookController::class ,'delete']);

Route::get('/authors', [AuthorController::class , 'index']);
Route::get('/authors/{id}', [AuthorController::class , 'show']);
Route::post('/authors/create', [AuthorController::class , 'create']);
Route::put('/authors/edit/{id}', [AuthorController::class , 'edit']);
Route::delete('/authors/delete/{id}', [AuthorController::class , 'delete']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
