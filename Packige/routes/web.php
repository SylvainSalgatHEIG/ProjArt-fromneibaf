<?php

<<<<<<< HEAD:Packige/routes/web.php
use Illuminate\Support\Facades\Route;
=======
use App\Http\Controllers\DeadlineController;
use App\Http\Controllers\GradeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Models\Deadline;
use Illuminate\Support\Facades\Auth;
>>>>>>> cd35dcb33750b8e52936ec3ead60113ca448edfa:laravel/routes/web.php

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
<<<<<<< HEAD:Packige/routes/web.php
    return view('vue');
});
=======
    return view('welcome');
});

Route::resource('user', UserController::class);
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('grades', GradeController::class);

Route::resource('deadlines', DeadlineController::class);

Route::post('deadlinesFiltered', [DeadlineController::class,'filter']);


>>>>>>> cd35dcb33750b8e52936ec3ead60113ca448edfa:laravel/routes/web.php
