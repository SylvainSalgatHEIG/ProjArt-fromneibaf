<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Grade;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function getCourses()
    {

        $courses = DB::table('courses')
            ->join('modules', 'modules.id', '=', 'courses.module_id')
            ->join('promotions', 'promotions.id', '=', 'modules.promotion_id')
            ->join('groups', 'groups.promotion_id', '=', 'promotions.id')
            ->join('group_user', 'group_user.group_id', '=', 'groups.id')
            ->join('users', 'users.id', '=', 'group_user.user_id')
            ->select('courses.name', 'courses.shortname')
            ->where('users.id', '=', Auth::id())
            ->where('modules.semester', '=', 4)
            ->get();

        return $courses;
    }

}
