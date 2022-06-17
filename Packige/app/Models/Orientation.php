<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orientation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }
}
