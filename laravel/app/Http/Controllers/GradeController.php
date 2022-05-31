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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // echo '<pre>';
        $modules = DB::table('modules')
            ->join('promotions', 'promotions.id', '=', 'modules.promotion_id')
            ->join('groups', 'groups.promotion_id', '=', 'promotions.id')
            ->join('group_user', 'group_user.group_id', '=', 'groups.id')
            ->join('users', 'users.id', '=', 'group_user.user_id')
            ->select('modules.name', 'modules.semester', 'modules.id')
            ->where('users.id', '=', Auth::id())
            ->where('modules.semester', '=', 4)
            ->get();

        $gradesArray = [];
        $lastCourse = '';
        foreach ($modules as $module) {
            $gradesArray[$module->name] = [];

            $courses = DB::table('courses')
                ->where('module_id', '=', $module->id)
                ->get();

            foreach ($courses as $course) {
                $gradesArray[$module->name][$course->name]['weighting'] = $course->weighting;
                $gradesArray[$module->name][$course->name]['grades'] = [];

                $grades = DB::table('grades')
                    ->join('users', 'users.id', '=', 'grades.user_id')
                    ->join('courses', 'grades.course_id', '=', 'courses.id')
                    ->where('users.id', '=', Auth::id())
                    ->where('courses.id', '=', $course->id)
                    ->select('grades.*')
                    ->get();

                // print_r($grades);
                foreach ($grades as $grade) {
                    array_push($gradesArray[$module->name][$course->name]['grades'], ['grade' => $grade->grade, 'coefficient' => $grade->coefficient]);
                }
                $gradesArray[$module->name][$course->name]['average'] = $this->getCourseAverage($gradesArray[$module->name][$course->name]);
            }
            $gradesArray[$module->name]['average'] = $this->getModuleAverage($gradesArray[$module->name]);
        }

        // print_r($gradesArray);
        // print_r($modules);
        // echo '</pre>';

        return view('view_grades', compact('gradesArray'));
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
