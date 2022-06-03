<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Deadline;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeadlineController extends Controller
{

    public function getDeadlines()
    {
        $deadlineArray = [];

        $userId = Auth::id();
        $user = User::where('id', $userId)->first();

        $userGroups = $user->groups()->get();

        $userPromotion = $userGroups[0]->promotion()->get();

        $modulesList = $userPromotion[0]->modules()->get();
        $coursesList = [];
        foreach ($modulesList as $module) {
            $courses = $module->courses()->get();
            foreach ($courses as $course) {
                array_push($coursesList, $course);
            }
        }

        $deadlineArray = $user->deadlines()->get();


        foreach ($deadlineArray as $deadline) {
            $course = Course::where('id', $deadline->course_id)->get();
            $deadline['course'] = $course;
        }

        foreach ($deadlineArray as $deadline) {
            $deadlineUser = DB::table('deadline_user')
                ->join('users','users.id','=','deadline_user.user_id')
                ->join('deadlines','deadlines.id','=','deadline_user.deadline_id')
                ->where('deadline_user.user_id','=',$userId)
                ->where('deadline_user.deadline_id','=',$deadline->id)
                ->get();
            
            $deadline['check'] = $deadlineUser;
            
        }

        return $deadlineArray;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = Auth::id();
        $user = User::where('id', $userId)->first();

        $userGroups = $user->groups()->get();

        $userPromotion = $userGroups[0]->promotion()->get();

        $modulesList = $userPromotion[0]->modules()->get();
        $coursesList = [];
        foreach ($modulesList as $module) {
            $courses = $module->courses()->get();
            foreach ($courses as $course) {
                array_push($coursesList, $course);
            }
        }

        $groupSelected = $userGroups->first()->name;

        $deadlines = $user->deadlines()->get();

        

        foreach ($deadlines as $deadline) {
            $course = Course::where('id', $deadline->course_id)->get();
            $deadline->$course = $course;
        }

        

        return view('view_deadlines', compact('userGroups','userPromotion','deadlines', 'groupSelected', 'coursesList'));

    }

    public function filter(Request $request) 
    {   

        $userId = Auth::id();
        $user = User::where('id', $userId)->first();


        $userGroups = $user->groups()->get();
        $groupSelected = $request->input('groupName');
        $userGroup = Group::where('name', $groupSelected)->first();

        $deadlines = $userGroup->deadlines()->get();

        $userPromotion = $userGroup->promotion()->get();

        $modulesList = $userPromotion[0]->modules()->get();
        $coursesList = [];
        foreach ($modulesList as $module) {
            $courses = $module->courses()->get();
            foreach ($courses as $course) {
                array_push($coursesList, $course);
            }
        }

        return view('view_deadlines', compact('userGroups','userPromotion','deadlines', 'groupSelected', 'coursesList'));
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
        $deadlineToAdd = new Deadline;

        $deadlineToAdd->name = $request->nameDeadline;
        $deadlineToAdd->description = $request->descriptionDeadline;
        $deadlineToAdd->type = $request->typeDeadline;

        $deadlineToAdd->start_date = $request->startDeadline;
        $deadlineToAdd->end_date = $request->endDeadline;


        $userId = Auth::id();
        $user = User::where('id', $userId)->first();

        $userGroups = $user->groups()->get();

        $userPromotion = $userGroups[0]->promotion()->get();

        $groupSelected = $userGroups->first()->name;

        $deadlines = $user->deadlines()->get();


        foreach ($deadlines as $deadline) {
            $course = Course::where('id', $deadline->course_id)->get();
            $deadline->$course = $course;
        }

        $modulesList = $userPromotion[0]->modules()->get();
        $coursesList = [];
        foreach ($modulesList as $module) {
            $courses = $module->courses()->get();
            foreach ($courses as $course) {
                array_push($coursesList, $course);
            }
        }
        
        $courseSelected = Course::where('name', $request->coursesList)->first();
        $deadlineToAdd->course_id = $courseSelected->id;
        
        $deadlineToAdd->group_id = $request->groups;

        $deadlineToAdd->save();

        return view('view_deadlines', compact('userGroups','userPromotion','deadlines', 'groupSelected', 'coursesList'));
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
