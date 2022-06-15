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
        if (Auth::id() == null) {
            return [];
        }
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
            ->where('courses.shortname', '=', $request->course)
            ->first();

        $deadlineToAdd->course_id = $courseId->id;
        $deadlineToAdd->group_id = $request->groupId;

        $deadlineToAdd->save();

        $usersGroupId = DB::table('users')
            ->join('group_user', 'group_user.user_id', '=', 'users.id')
            ->join('groups', 'groups.id', '=', 'group_user.group_id')
            ->select('users.id')
            ->where('groups.id', '=', $request->groupId)
            ->get();
        // dd($usersGroup);
        foreach($usersGroupId as $userId) {
            $deadlineToAdd->users()->attach($userId, ['isChecked' => false]);
        }

        return $deadlineToAdd->id;
    }

    public function editDeadline(Request $request)
    {

        $date = $request->date;
        $startTime = $request->startTime;
        $endTime = $request->endTime;
        $startDate = date('Y-m-d H:i:s', strtotime("$date $startTime"));
        $endDate = date('Y-m-d H:i:s', strtotime("$date $endTime"));

        $update = DB::table('deadlines')
            ->where('id', $request->deadlineId)
            ->update([
                'name' => $request->name,
                'description' => $request->description,
                'description' => $request->description,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'group_id' => $request->groupId,
            ]);

        return $request->deadlineId;
    }

    public function deleteDeadline(Request $request)
    {
        $deleted = DB::table('deadline_user')
            ->where('deadline_id', '=', $request->id)
            ->where('user_id', '=', Auth::id())
            ->delete();

        $deleted = DB::table('deadlines')->where('id', '=', $request->id)->delete();

        return $request->id;
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
