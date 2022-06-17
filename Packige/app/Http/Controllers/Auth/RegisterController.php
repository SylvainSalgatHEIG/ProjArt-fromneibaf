<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RegisterRequest;
use App\Models\Group;
use App\Rules\GroupPwd;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => ['required', 'string', 'max:30'],
            'lastname'  => ['required', 'string', 'max:25'],
            'email'     => ['required', 'string', 'email', 'max:30', 'unique:users', 'regex:/^[\w\-\.]+@heig-vd.ch$/i'],
            'groupName' => ['required', 'numeric', 'exists:App\Models\Group,id'],
            'groupPwd'  => ['required', 'numeric', new GroupPwd],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        // $result = Validator::make($request->all(), [
        //     'firstname' => ['required', 'string', 'max:30'],
        //     'lastname'  => ['required', 'string', 'max:25'],
        //     'email'     => ['required', 'string', 'email', 'max:30', 'unique:users', 'regex:/^[\w\-\.]+@heig-vd.ch$/i'],
        //     'groupName' => ['required', 'numeric', 'exists:App\Models\Group,id'],
        //     'groupPwd'  => ['required', 'numeric', new GroupPwd],
        //     'password'  => ['required', 'string', 'min:8', 'confirmed'],
        // ]);
        
        // if ($result->fails()) {
        //     return $result->errors();
        // }
        // return "success";
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        
        $groupUser = DB::table('group_user')->insert([
            'group_id' => $data['groupName'],
            'user_id' => $user['id'],
        ]);

        $deadlinesGroupId = DB::table('deadlines')
            ->join('groups', 'groups.id', '=', 'deadlines.group_id')
            ->join('group_user', 'group_user.group_id', '=', 'groups.id')
            ->join('users', 'users.id', '=', 'group_user.user_id')
            ->select('deadlines.id')
            ->where('users.id', '=', $user['id'])
            ->get();
        // dd($usersGroup);
        foreach($deadlinesGroupId as $deadlineId) {
            $user->deadlines()->attach($deadlineId, ['isChecked' => false]);
        }
        if ($groupUser) {
            return $user;
        }else {
            return "Une erreur s'est produite lors de l'ajout de la classe à l'utilisateur";
        }
    }

    public function showRegistrationForm()
    {
        $groups = Group::with('promotion')->get();
        return view('auth.register')->with('groups', $groups);
    }
}
