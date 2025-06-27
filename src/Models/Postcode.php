<?php

namespace TrAddress\Models;

use Illuminate\Database\Eloquent\Model;
use TrAddress\Models\Neighborhood;

class Postcode extends Model
{
    protected $fillable = ['neighborhood_id', 'code'];
    public $timestamps = false;

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }
} 