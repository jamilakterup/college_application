<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //Eloquent Relationship
	public function assignedroles() {

		return $this->hasMany('AssignedRole');

	}

	public function permissionroles() {

		return $this->hasMany('PermissionRole');

	}
}
