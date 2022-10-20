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
    /**
     * Get all courses based on logged user
     *
     * @return array Courses
     */
    public function getCourses()
    {
        if(Auth::id() == null){
            return [];
        }
        // get group user with promotion and group
        $groupsUsers = DB::table('group_user')
        ->join('groups', 'groups.id', 'group_user.group_id')
        ->join('promotions', 'promotions.id', 'groups.promotion_id')
        ->where('user_id', Auth::id())->get();
        $courses = [];
        // get all courses from the right semester
        foreach($groupsUsers as $group) {
        
            $semester = (date('Y') - date('Y', strtotime($group->start_year))) * 2;
            if (intval(date('m')) >= 9) {
                $semester += 1;
            } else if (intval(date('m')) < 2) {
                $semester -= 1;
            }
            
            $coursesQuery = DB::table('courses')
            ->join('modules', 'modules.id', '=', 'courses.module_id')
            ->join('promotions', 'promotions.id', '=', 'modules.promotion_id')
            ->join('groups', 'groups.promotion_id', '=', 'promotions.id')
            ->join('group_user', 'group_user.group_id', '=', 'groups.id')
            ->join('users', 'users.id', '=', 'group_user.user_id')
            ->select('courses.name as courseName', 'courses.shortname as courseShortName', 'modules.name AS moduleName', 'courses.id as courseId')
            ->where('users.id', '=', Auth::id())
            ->where('modules.semester', '=', $semester)
            ->where('groups.id', '=', $group->group_id)
            ->get();
            foreach ($coursesQuery as $course) {
                array_push($courses, $course);	
            }
        }
        
        return $courses;
    }

}
