<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeesDetails extends Model
{
    protected $table = 'fees_details';
    public $timestamps = false;

    protected $fillable = [
        'session',
        'current_year',
        'fees_header',
        'is_gov',
        'science',
        'humanities',
        'business'
    ];
}
