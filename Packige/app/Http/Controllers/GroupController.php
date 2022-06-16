<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Group;
use App\Rules\GroupPwd;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Get all groups
     *
     * @return array Groups
     */
    public function getGroups()
    {
        if (Auth::id() == null) {
            return [];
        }
        $groups = Group::with('promotion')->get();
        return $groups;
    }

    /**
     * Get all groups in which logged user belong
     *
     * @return array User's Groups
     */
    public function getUserGroups()
    {
        if (Auth::id() == null) {
            return [];
        }
        $userGroups = [];

        $userGroupsIds = DB::table('groups')
            ->join('group_user', 'group_user.group_id', '=', 'groups.id')
            ->join('users', 'users.id', '=', 'group_user.user_id')
            ->where('users.id', '=', Auth::id())
            ->select('groups.id')
            ->get();
        foreach ($userGroupsIds as $groupId) {
            array_push($userGroups, Group::with('promotion')->where('groups.id', $groupId->id)->get());
        }

        return $userGroups;
    }
}
