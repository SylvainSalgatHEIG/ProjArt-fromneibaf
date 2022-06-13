<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Grade;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{

    public function getGrades()
    {
        if (Auth::id() == null) {
            return [];
        }
        // get group user with promotion and group
        $groupsUsers = DB::table('group_user')
            ->join('groups', 'groups.id', 'group_user.group_id')
            ->join('promotions', 'promotions.id', 'groups.promotion_id')
            ->where('user_id', Auth::id())->get();

        $modules = [];

        // get all modules from the right semester
        foreach ($groupsUsers as $group) {

            $semester = (date('Y') - date('Y', strtotime($group->start_year))) * 2;
            if (intval(date('m')) >= 9 || intval(date('m')) < 3) {
                $semester -= 1;
            }

            $modulesQuery = DB::table('modules')
                ->join('promotions', 'promotions.id', '=', 'modules.promotion_id')
                ->join('groups', 'groups.promotion_id', '=', 'promotions.id')
                ->join('group_user', 'group_user.group_id', '=', 'groups.id')
                ->join('users', 'users.id', '=', 'group_user.user_id')
                ->select('modules.name', 'modules.semester', 'modules.id')
                ->where('users.id', '=', Auth::id())
                ->where('modules.semester', '=', $semester)
                ->where('groups.id', '=', $group->group_id)
                ->get();
            foreach ($modulesQuery as $module) {
                array_push($modules, $module);
            }
        }

        $gradesArray = [];
        foreach ($modules as $module) {
            $gradesArray[$module->name] = [];

            $courses = DB::table('courses')
                ->where('module_id', '=', $module->id)
                ->get();

            foreach ($courses as $course) {
                $gradesArray[$module->name][$course->shortname]['weighting'] = $course->weighting;
                $gradesArray[$module->name][$course->shortname]['grades'] = [];
                $gradesArray[$module->name][$course->shortname]['id'] = $course->id;

                $grades = DB::table('grades')
                    ->join('users', 'users.id', '=', 'grades.user_id')
                    ->join('courses', 'grades.course_id', '=', 'courses.id')
                    ->where('users.id', '=', Auth::id())
                    ->where('courses.id', '=', $course->id)
                    ->select('grades.*')
                    ->get();

                foreach ($grades as $grade) {
                    array_push($gradesArray[$module->name][$course->shortname]['grades'], ['id' => $grade->id, 'grade' => $grade->grade, 'coefficient' => $grade->coefficient]);
                }
                $gradesArray[$module->name][$course->shortname]['average'] = $this->getCourseAverage($gradesArray[$module->name][$course->shortname]);
            }
            $gradesArray[$module->name]['average'] = $this->getModuleAverage($gradesArray[$module->name]);
            $gradesArray[$module->name]['id'] = $module->id;
        }

        return $gradesArray;
    }

    public function getCourseAverage($courseGrades)
    {
        if (empty($courseGrades['grades'])) {
            return 0;
        }
        $sum = 0;
        $gradeCounter = 0;
        foreach ($courseGrades['grades'] as $grade) {
            $sum += $grade['grade'] * $grade['coefficient'];
            $gradeCounter += $grade['coefficient'];
        }
        $average = $sum / $gradeCounter;
        return round($average, 1);
    }

    public function getModuleAverage($moduleData)
    {
        $sum = 0;
        $gradeCounter = 0;
        foreach ($moduleData as $course) {
            $sum += $course['average'] * $course['weighting'];
            $gradeCounter += $course['weighting'];
        }
        if ($gradeCounter == 0) {
            return 0;
        }
        $average = $sum / $gradeCounter;
        return round($average * 2) / 2;
    }

    public function addGrade(Request $request)
    {

        // dd($request->course);

        // $courseId = DB::table('courses')
        //     ->join('modules', 'modules.id', '=', 'courses.module_id')
        //     ->join('promotions', 'promotions.id', '=', 'modules.promotion_id')
        //     ->join('groups', 'groups.promotion_id', '=', 'promotions.id')
        //     ->join('group_user', 'group_user.group_id', '=', 'groups.id')
        //     ->join('users', 'users.id', '=', 'group_user.user_id')
        //     ->select('courses.id')
        //     ->where('users.id', '=', Auth::id())
        //     ->where('courses.shortname', '=', $request->course)
        //     ->first();

        $grade = new Grade;

        $grade->coefficient = $request->coefficient;
        $grade->grade = $request->grade;
        $grade->user_id = Auth::id();
        $grade->course_id = $request->course;
        $grade->save();
        if (!$grade) {
            return 'FAILED';
        }
        return $grade->id;
    }

    public function editGrade(Request $request)
    {
        $update = DB::table('grades')
            ->where('id', $request->id)
            ->update(['grade' => $request->grade, 'coefficient' => $request->coefficient]);
        if ($update) {
            return 'success';
        } else {
            return 'erreur';
        }
    }

    public function deleteGrade(Request $request)
    {
        $deleted = DB::table('grades')->where('id', '=', $request->id)->delete();
        return 'success';
    }
}
