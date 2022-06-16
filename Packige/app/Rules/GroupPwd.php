<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GroupPwd implements Rule, DataAwareRule
{
    
    /**
     * All of the data under validation.
     *
     * @var array
     */
    protected $data = [];

     /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }


    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //$this->data['start_date'];
        $groupId = $this->data['groupName'];
        $groupPwd = $this->data['groupPwd'];
 
        $group = DB::table('groups')
            ->select('name', 'password')
            ->where('id', $groupId)
            ->get();
        
        
        
        if(Hash::check($groupPwd, $group[0]->password)) return true;
        
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Le code de classe est incorrect.';
    }
}
