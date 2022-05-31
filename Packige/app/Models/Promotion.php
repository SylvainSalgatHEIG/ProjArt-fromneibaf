<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
    ];

    public function orientation()
    {
        return $this->belongsTo(Orientation::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }
    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
