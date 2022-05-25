<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'password',
    ];

    /**
     * Permet d'encoder le mot de passe
     * @param type $password Le mot de passe
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function deadlines() {
        return $this->hasMany(Deadline::class);
    } 
}
