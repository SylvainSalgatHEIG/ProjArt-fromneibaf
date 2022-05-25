<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function courses() {
        return $this->hasMany(Course::class);
    } 
    

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
