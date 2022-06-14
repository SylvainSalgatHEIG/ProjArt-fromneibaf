<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DeadlineController;
use App\Http\Controllers\GradeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TestApi;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ConnexionController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LogoutController;

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

// Route::resource('user', UserController::class);
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/api/courses', [CourseController::class,'getCourses']);

Route::get('/api/groups', [GroupController::class,'getGroups']);

Route::get('/api/grades/get', [GradeController::class,'getGrades']);
Route::post('/api/grades/add', [GradeController::class,'addGrade']);
Route::post('/api/grades/edit', [GradeController::class,'editGrade']);
Route::post('/api/grades/delete', [GradeController::class,'deleteGrade']);

Route::get('/api/deadlines/get', [DeadlineController::class,'getDeadlines']);
Route::post('/api/deadlines/add', [DeadlineController::class,'addDeadline']);
Route::post('/api/deadlines/edit', [DeadlineController::class,'editDeadline']);
Route::post('/api/deadlines/delete', [DeadlineController::class,'deleteDeadline']);
Route::post('deadlinesFiltered', [DeadlineController::class,'filter']);
Route::get('/api/deadline/check/{deadlineId}/{action}', [DeadlineController::class,'checkDeadline']);

Route::get('/api/usergroups', [GroupController::class,'getUserGroups']);

Route::post('/api/register/validator', [RegisterController::class, 'validator']);

Route::get('/api/events', [EventController::class,'getEvents']);

Route::get('/api/user/get', [UserController::class,'getUser']);
Route::get('/api/user/links', [UserController::class,'getLinks']);
Route::post('/api/user/link/add', [UserController::class,'addLink']);
Route::post('/api/user/link/edit', [UserController::class,'editLink']);
Route::post('/api/user/link/delete', [UserController::class,'deleteLink']);
Route::post('/api/user/changeScheduleView', [UserController::class,'changeScheduleView']);

Route::get('/api/connexion/status', [ConnexionController::class, 'getConnexionStatus']);

Route::get('/logout', [LogoutController::class, 'perform']);
