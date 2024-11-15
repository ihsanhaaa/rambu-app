<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Daun extends Model
{
    use HasFactory;

    protected $table = 'dauns';
    protected $guarded = ['id'];
}
