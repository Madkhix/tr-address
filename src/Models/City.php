<?php

namespace TrAddress\Models;

use Illuminate\Database\Eloquent\Model;
use TrAddress\Models\District;

class City extends Model
{
    protected $fillable = ['name'];
    public $timestamps = false;

    public function districts()
    {
        return $this->hasMany(District::class);
    }
} 