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
    /**
     * Get an array containing every grades from the connected user.
     * The array is sorted by module then by course.
     * The grades are from the current semester
     * 
     * @return array [moduleName1[id, [courseShortName1[id, weighting, grades[1[id, name, coefficient, grade], ...]]]courseShortName2[...]] moduleName2[...]]
     */
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

        // get all modules from the right semester
        $modules = [];

        foreach ($groupsUsers as $group) {

            $semester = (date('Y') - date('Y', strtotime($group->start_year))) * 2;
            if (intval(date('m')) >= 9) {
                $semester += 1;
            } else if (intval(date('m')) < 2) {
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

        // get all the grades for each modules and courses
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
                    array_push($gradesArray[$module->name][$course->shortname]['grades'], ['id' => $grade->id, 'name' => $grade->name, 'grade' => $grade->grade, 'coefficient' => $grade->coefficient]);
                }
            }
            $gradesArray[$module->name]['id'] = $module->id;
        }

        return $gradesArray;
    }

    /**
     * Add a new grade to the database for the connected user
     *
     * @param Request $request data given by the add form (name, grade, coefficient, course)
     * @return int added grade id
     */
    public function addGrade(Request $request)
    {

        $grade = new Grade;

        $grade->name = $request->name;
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

    /**
     * Edit a grade from the connected user
     *
     * @param Request $request data given by the edit form (id, name, grade, coefficient)
     * @return string success or error
     */
    public function editGrade(Request $request)
    {
        $update = DB::table('grades')
            ->where('id', $request->id)
            ->update(['name' => $request->name, 'grade' => $request->grade, 'coefficient' => $request->coefficient]);
        if ($update) {
            return 'success';
        } else {
            return 'erreur';
        }
    }

    /**
     * Delete a grade from the database for the connected user
     *
     * @param Request $request data given by the form (id)
     * @return string success
     */
    public function deleteGrade(Request $request)
    {
        $deleted = DB::table('grades')->where('id', '=', $request->id)->delete();
        return 'success';
    }
}
