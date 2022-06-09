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
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getLinks() {   
        $userId = Auth::id();
        $userLinks = DB::table('links')
            ->where('user_id', '=', Auth::id())
            ->get();
        return $userLinks;
    }

    public function addLink(Request $request) {
        // A REVOIR
        $link = new Link;

        $link->name = $request->name;
        $link->link = $request->link;
        $link->user_id = Auth::id();
        $link->save();
        if (!$link) {
            return 'FAILED';
        }
        return 'SUCCESS';
    }

    public function editLink(Request $request)
    {
        $update = DB::table('links')
            ->where('id', $request->id)
            ->update(['link' => $request->link, 'name' => $request->name]);
    }

    public function deleteLink(Request $request){
        $deleted = DB::table('links')->where('id', '=', $request->id)->delete();
    }
}
