<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rambu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rambus';
    protected $guarded = ['id'];

    public function lokasi(): HasOne
    {
        return $this->hasOne(Lokasi::class);
    }

    public function fotos()
    {
        return $this->hasMany(Foto::class);
    }

    public function statuses()
    {
        return $this->hasMany(Status::class, 'rambu_id');
    }

    public function tiang()
    {
        return $this->hasOne(Tiang::class);
    }

    public function lensa()
    {
        return $this->hasOne(Lensa::class);
    }

    public function daun()
    {
        return $this->hasOne(Daun::class);
    }

    public function latestFoto()
    {
        return $this->hasOne(Foto::class)->latestOfMany();
    }

    public function statusRambuTerbaru()
    {
        return $this->hasOne(Status::class)->latestOfMany();
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function latestStatus()
    {
        return $this->hasOne(Status::class)->latestOfMany(); // Mengambil status terbaru
    }
}
