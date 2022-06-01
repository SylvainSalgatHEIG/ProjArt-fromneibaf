<?php

use App\Http\Controllers\DeadlineController;
use App\Http\Controllers\GradeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TestApi;

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
    return view('vue');
});

Route::resource('user', UserController::class);
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('grades', GradeController::class);

Route::resource('deadlines', DeadlineController::class);

Route::post('deadlinesFiltered', [DeadlineController::class,'filter']);
Route::post('deadlines/add', [DeadlineController::class,'store']);

Route::get('/api/groups', [GroupController::class,'getGroups']);
