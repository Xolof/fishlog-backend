<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishCatch extends Model
{
    use HasFactory;
    protected $fillable = [
        'species', 'length', 'weight', 'date', 'location', 'imageurl'
    ];
}