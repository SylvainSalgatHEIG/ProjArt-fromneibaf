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
     * Get all deadlines based on logged user
     *
     * @return array Deadlines
     */
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


    /**
     * Add a new deadline to the database for the connected user
     *
     * @param Request $request data given by the add form (name, course, description, type, date, start_time, end_time)
     * @return int added deadline id
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

        foreach($usersGroupId as $userId) {
            $deadlineToAdd->users()->attach($userId, ['isChecked' => false]);
        }

        return $deadlineToAdd->id;
    }


     /**
     * Edit a deadline from the connected user
     *
     * @param Request $request data given by the edit form (name, course, description, type, date, start_time, end_time)
     * @return string id of the deadline
     */
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

    /**
     * Delete a deadline from the database for the connected user
     *
     * @param Request $request data given by the form (id)
     * @return string id of the deadline
     */
    public function deleteDeadline(Request $request)
    {
        $deleted = DB::table('deadline_user')
            ->where('deadline_id', '=', $request->id)
            ->delete();

        $deleted = DB::table('deadlines')->where('id', '=', $request->id)->delete();

        return $request->id;
    }

    /**
     * Check or uncheck a deadline for the connected user
     *
     * @param int $deadlineId id of the deadline to check/uncheck
     * @param string $action action to do (check or uncheck)
     * @return string success or fail
     */
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
     * Filter the deadlines based on the class
     *
     * @param Request $request data given by the form (groupName)
     * @return view view with content
     */
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
