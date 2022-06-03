<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Group;
use App\Rules\GroupPwd;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function getGroups() {
        $groups = Group::with('promotion')->get();
        return $groups;
    }

    public function getUserGroups() {
        $userGroups = [];

        $userGroupsIds = DB::table('groups')
            ->join('group_user', 'group_user.group_id', '=', 'groups.id')
            ->join('users', 'users.id', '=', 'group_user.user_id')
            ->where('users.id', '=', Auth::id())
            ->select('groups.id')
            ->get();
        foreach ($userGroupsIds as $groupId) {
            array_push($userGroups, Group::with('promotion')->where('groups.id',$groupId->id)->get());
        }
        
        return $userGroups;
    }
}
