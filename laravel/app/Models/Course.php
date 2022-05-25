<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'shortname',
        'weighting',
    ];

    public function deadlines()
    {
        return $this->hasMany(Deadline::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
