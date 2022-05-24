<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade',
        'coefficient',
    ];

    public function cours()
    {
        return $this->belongsTo(Course::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
