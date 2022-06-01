<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Group;
use App\Rules\GroupPwd;

class GroupController extends Controller
{
    public function getGroups() {
        $groups = Group::with('promotion')->get();
        return $groups;
    }
}
