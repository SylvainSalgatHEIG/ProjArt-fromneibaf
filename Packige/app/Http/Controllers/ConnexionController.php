<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Link;

class ConnexionController extends Controller
{
/**
 * Check if an user is currently connected
 *
 * @return boolean
 */
    public function getConnexionStatus(){
        if(Auth::id() == null){
            return 'false';
        }
        return 'true';
    }
}
