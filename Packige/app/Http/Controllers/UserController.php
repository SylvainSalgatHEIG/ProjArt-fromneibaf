<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Link;

class UserController extends Controller
{
    /**
     * Get currently connected user from the database
     *
     * @return array [fullname, email, groups[name, id], schedule_type]
     */
    public function getUser()
    {
        if (Auth::id() == null) {
            return [];
        }
        $query = DB::table('users')
            ->join('group_user', 'group_user.user_id', 'users.id')
            ->join('groups', 'group_user.group_id', 'groups.id')
            ->join('promotions', 'promotions.id', 'groups.promotion_id')
            ->where('users.id', '=', Auth::id())
            ->select('users.lastname', 'users.firstname', 'users.email', 'users.schedule_type', 'promotions.name as promotionName', 'groups.name as groupName', 'groups.id as groupId')
            ->get();

        $userInfos['fullname'] = $query[0]->firstname . ' ' . $query[0]->lastname;
        $userInfos['email'] = $query[0]->email;
        foreach ($query as $key => $group) {
            $userInfos['groups'][$key]['name'] = $group->promotionName . '-' . $group->groupName;
            $userInfos['groups'][$key]['id'] = $group->groupId;
        }
        $userInfos['schedule_type'] = $query[0]->schedule_type;
        return $userInfos;
    }

    /**
     * Get users own custom links from database
     *
     * @return array
     */
    public function getLinks()
    {
        if (Auth::id() == null) {
            return [];
        }
        $userId = Auth::id();
        $userLinks = DB::table('links')
            ->where('user_id', '=', Auth::id())
            ->get();
        return $userLinks;
    }

    /**
     * Add a new custom link for the connected user
     *
     * @param Request $request data given by the form (url, name)
     * @return int id of the new link
     */
    public function addLink(Request $request)
    {
        $link = new Link;

        $link->name = $request->name;
        $link->link = $request->link;
        $link->user_id = Auth::id();
        $link->save();
        if (!$link) {
            return 'FAILED';
        }
        return $link->id;
    }

    /**
     * Update the prefered display for the schedule
     *
     * @param Request $request data given by the form (type)
     * @return Objet new schedule_type value
     */
    public function changeScheduleView(Request $request){
        $update = DB::table('users')
        ->where('id', Auth::id())
        ->update(['schedule_type' => $request->type]);

        return $update;
    }
}
