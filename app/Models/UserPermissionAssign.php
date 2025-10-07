<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermissionAssign extends Model
{
    protected $table = 'user_permission_assign';
    public $timestamps = false;
    protected $fillable = ['value','type','user_id'];

    public function user(){
        return $this->belongsTo('App\User', 'id', 'user_id');
    }
}
