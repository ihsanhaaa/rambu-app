<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasis';
    protected $guarded = ['id'];

    public function rambu(): BelongsTo
    {
        return $this->belongsTo(Rambu::class);
    }
}
