<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'statuses';
    protected $guarded = ['id'];

    public function rambu()
    {
        return $this->belongsTo(Rambu::class);
    }
}
