<?php

use App\Http\Controllers\ClaimController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect()->route('tasks.index');
});

Route::resource('tasks', TaskController::class);
Route::resource('claims', ClaimController::class);

Route::get('/tasks/{id}/status', [TaskController::class, 'setStatus'])->name('tasks.status');
Route::get('/claims/{id}/status', [ClaimController::class, 'setStatus'])->name('claims.status');
Route::get('/diagram', [TaskController::class, 'diagram'])->name('diagram');
Route::get('/diagram2', [ClaimController::class, 'diagram'])->name('diagram2');

