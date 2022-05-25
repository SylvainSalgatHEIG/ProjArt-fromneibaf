<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Grade;
use App\Models\Module;
use Illuminate\Http\Request;

class GradeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gradesArray = [];
        $grades = Grade::get();
        $lastModule = '';
        $lastCourse = '';
        foreach ($grades as $grade) {
            $course = Course::where('id', $grade->course_id)->first();
            $module = Module::where('id', $course->module_id)->first();

            if ($course->name != $lastCourse) {
                $lastCourse = $course->name;
                if ($module->name != $lastModule) {
                    $gradesArray[$module->name] = [];
                    $lastModule = $module->name;
                }
                $gradesArray[$module->name][$course->name]['grades'] = [];
            }

            array_push($gradesArray[$module->name][$course->name]['grades'], ['grade' => $grade->grade, 'coefficient' => $grade->coefficient]);
            $gradesArray[$module->name][$course->name]['average'] = $this->getCourseAverage($gradesArray[$module->name][$course->name]);
        }

        echo '<pre>';
        print_r($gradesArray);
        // print_r($gradesArray[$module->name][$course->name]);
        echo '</pre>';

        // return view('view_grades', compact('grades'));
    }

    public function getCourseAverage($courseGrades)
    {
        $sum = 0;
        $gradeCounter = 0;
        echo '<pre>';
        foreach ($courseGrades['grades'] as $grade) {
            // print_r($grade);
            $sum += $grade['grade']*$grade['coefficient'];
            $gradeCounter += $grade['coefficient'];
        }   
        $average = $sum / $gradeCounter;
        // print_r($courseGrades);
        echo '</pre>';
        return $average;
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
