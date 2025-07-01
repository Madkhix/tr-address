<?php

namespace TrAddress\Models;

use Illuminate\Database\Eloquent\Model;
use TrAddress\Models\District;
use TrAddress\Models\Postcode;
use TrAddress\Models\Subdistrict;

class Neighborhood extends Model
{
    protected $fillable = ['district_id', 'subdistrict_id', 'name'];
    public $timestamps = false;

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function postcodes()
    {
        return $this->hasMany(Postcode::class);
    }

    public function subdistrict()
    {
        return $this->belongsTo(Subdistrict::class);
    }
} 