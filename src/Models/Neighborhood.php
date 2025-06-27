<?php

namespace TrAddress\Models;

use Illuminate\Database\Eloquent\Model;
use TrAddress\Models\District;
use TrAddress\Models\Postcode;

class Neighborhood extends Model
{
    protected $fillable = ['district_id', 'name'];
    public $timestamps = false;

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function postcodes()
    {
        return $this->hasMany(Postcode::class);
    }
} 