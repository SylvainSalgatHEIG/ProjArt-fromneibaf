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

        $groupSelected = $userGroups->first()->name;

        $deadlines = $user->deadlines()->get();

        foreach ($deadlines as $deadline) {
            $course = Course::where('id', $deadline->course_id);
            $deadline->course = $course;
        }

        return view('view_deadlines', compact('userGroups','userPromotion','deadlines', 'groupSelected'));

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

        //return dd($userGroup);
        return view('view_deadlines', compact('userGroups','userPromotion','deadlines', 'groupSelected'));
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
