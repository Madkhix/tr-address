<?php

namespace TrAddress\Models;

use Illuminate\Database\Eloquent\Model;
use TrAddress\Models\District;
use TrAddress\Models\Neighborhood;

class Subdistrict extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = ['district_id', 'name'];
    public $timestamps = false;

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function neighborhoods()
    {
        return $this->hasMany(Neighborhood::class);
    }
} 