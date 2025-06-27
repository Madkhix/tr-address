<?php

namespace TrAddress\Models;

use Illuminate\Database\Eloquent\Model;
use TrAddress\Models\City;
use TrAddress\Models\Neighborhood;

class District extends Model
{
    protected $fillable = ['city_id', 'name'];
    public $timestamps = false;

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function neighborhoods()
    {
        return $this->hasMany(Neighborhood::class);
    }
} 