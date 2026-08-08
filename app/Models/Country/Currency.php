<?php

namespace App\Models\Country;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'country_currencies';

    protected $fillable = ['country_id', 'code', 'name', 'symbol'];
}
