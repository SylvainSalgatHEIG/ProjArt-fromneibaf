<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Link;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

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
            ->select('users.lastname', 'users.firstname', 'users.email', 'promotions.name as promotionName', 'groups.name as groupName')
            ->get();

        $userInfos['fullname'] = $query[0]->firstname . ' ' . $query[0]->lastname;
        $userInfos['email'] = $query[0]->email;
        foreach ($query as $key => $group) {
            $userInfos['groups'][$key] = $group->promotionName . '-' . $group->groupName;
        }
        return $userInfos;
    }

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

    public function addLink(Request $request)
    {
        // A REVOIR
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

    public function editLink(Request $request)
    {
        $update = DB::table('links')
            ->where('id', $request->id)
            ->update(['link' => $request->link, 'name' => $request->name]);
    }

    public function deleteLink(Request $request)
    {
        $deleted = DB::table('links')->where('id', '=', $request->id)->delete();
    }
}
