<?php

namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;

class FeesApplication extends Model
{
    protected $table = 'fees_applications';

    protected $guarded  = [];


    public function invoice()
    {
        return $this->morphOne(Invoice::class, 'refference','reference_model');
    }
}
