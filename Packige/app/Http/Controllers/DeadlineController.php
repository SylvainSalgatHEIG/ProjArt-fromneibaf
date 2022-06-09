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

        $deadlineArray = $user->deadlines()
            ->orderBy('deadlines.start_date', 'asc')
            ->get();


        foreach ($deadlineArray as $deadline) {
            $course = Course::where('id', $deadline->course_id)->get();
            $deadline['course'] = $course;
        }

        foreach ($deadlineArray as $deadline) {
            $deadlineUser = DB::table('deadline_user')
                ->join('users', 'users.id', '=', 'deadline_user.user_id')
                ->join('deadlines', 'deadlines.id', '=', 'deadline_user.deadline_id')
                ->where('deadline_user.user_id', '=', $userId)
                ->where('deadline_user.deadline_id', '=', $deadline->id)
                ->get();

            $deadline['check'] = $deadlineUser;
        }

        return $deadlineArray;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addDeadline(Request $request)
    {
        $deadlineToAdd = new Deadline;

        $deadlineToAdd->name = $request->name;
        $deadlineToAdd->description = $request->description;
        $deadlineToAdd->type = $request->type;

        $date = $request->date;
        $startTime = $request->startTime;
        $endTime = $request->endTime;
        $startDate = date('Y-m-d H:i:s', strtotime("$date $startTime"));
        $endDate = date('Y-m-d H:i:s', strtotime("$date $endTime"));

        $deadlineToAdd->start_date = $startDate;
        $deadlineToAdd->end_date = $endDate;

        $courseId = DB::table('courses')
            ->join('modules', 'courses.module_id', '=', 'modules.id')
            ->join('promotions', 'promotions.id', '=', 'modules.promotion_id')
            ->join('groups', 'groups.promotion_id', '=', 'promotions.id')
            ->join('group_user', 'group_user.group_id', '=', 'groups.id')
            ->join('users', 'users.id', '=', 'group_user.user_id')
            ->select('courses.id')
            ->where('users.id', '=', Auth::id())
            ->where('modules.semester', '=', 4)
            ->where('courses.shortname', '=', $request->course)
            ->first();

        $deadlineToAdd->course_id = $courseId->id;
        $deadlineToAdd->group_id = $request->groupId;

        $deadlineToAdd->save();

        $deadlineToAdd->users()->attach(Auth::id(), ['isChecked' => false]);

        return $deadlineToAdd;
    }

    public function checkDeadline($deadlineId, $action)
    {
        if ($action == "check") {
            DB::table('deadline_user')
                ->where('deadline_id', '=', $deadlineId)
                ->limit(1)
                ->update(array('isChecked' => 1));
        } else if ($action == "uncheck") {
            DB::table('deadline_user')
                ->where('deadline_id', '=', $deadlineId)
                ->limit(1)
                ->update(array('isChecked' => 0));
        } else {
            return ["fail"];
        }

        return ["success"];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $userId = Auth::id();
    //     $user = User::where('id', $userId)->first();

    //     $userGroups = $user->groups()->get();

    //     $userPromotion = $userGroups[0]->promotion()->get();

    //     $modulesList = $userPromotion[0]->modules()->get();
    //     $coursesList = [];
    //     foreach ($modulesList as $module) {
    //         $courses = $module->courses()->get();
    //         foreach ($courses as $course) {
    //             array_push($coursesList, $course);
    //         }
    //     }

    //     $groupSelected = $userGroups->first()->name;

    //     $deadlines = $user->deadlines()->get();



    //     foreach ($deadlines as $deadline) {
    //         $course = Course::where('id', $deadline->course_id)->get();
    //         $deadline->$course = $course;
    //     }



    //     return view('view_deadlines', compact('userGroups', 'userPromotion', 'deadlines', 'groupSelected', 'coursesList'));
    // }

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

        return view('view_deadlines', compact('userGroups', 'userPromotion', 'deadlines', 'groupSelected', 'coursesList'));
    }
}
