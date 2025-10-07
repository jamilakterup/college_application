<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Validator;
use Spatie\Permission\Traits\HasRoles;
use DB;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'email', 'password', 'full_name', 'status', 'user_type'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Create User Rules And Validation
     */
    public static $rules = ['username'              => 'required|min:4|unique:users',       
                            'email'                 => 'required|unique:users',
                            'password'              => 'required|min:6|max:12|confirmed',
                            'password_confirmation' => 'required|min:6|max:12',
                            'full_name'             => 'required|min:4',
                            'status'                => 'boolean',
                            'user_type'             => 'integer'];

    public static function validate($data) {

        return Validator::make($data, self::$rules);

    }



    /**
     * Update User Rules And Validation
     */
    public static function updateValidate($data) {

        $id = $data['id'];

        $rules = ["username"                => "required|min:4|unique:users,username,$id",      
                  "email"                   => "required|unique:users,email,$id",
                  "full_name"               => "required|min:4"];

        return Validator::make($data, $rules);

    }



    /**
     * Reset User Password Rules And Validation
     */
    public static $reset_rules = ['password'                => 'required|min:6|max:12|confirmed',
                                  'password_confirmation'   => 'required|min:6|max:12'];    

    public static function resetValidate($data) {

        return Validator::make($data, self::$reset_rules);

    }   



    /**
     * Login User Rules And Validation
     */
    public static $login_rules = ['email' => 'required',
                                  'password' => 'required|min:6|max:12'];

    public static function loginValidate($data) {

        return Validator::make($data, self::$login_rules);

    }                             


    /**
     * Eloquent Relationship
     */
    public function assignedrole() {

        return $this->hasOne('App\Models\AssignedRole');

    }

    public static function getpermissionParentGroups()
    {
        $permission_parent_groups = DB::table('permissions')
            ->select('parent_group_name as parent_group_name')
            ->groupBy('parent_group_name')
            ->get();
        return $permission_parent_groups;
    }

    public static function getpermissionGroups($parent_group_name)
    {
        $permission_groups = DB::table('permissions')
            ->select('group_name as name')
            ->where('parent_group_name', $parent_group_name)
            ->groupBy('group_name')
            ->get();
        return $permission_groups;
    }

    public static function getpermissionsByGroupName($group_name)
    {
        $permissions = DB::table('permissions')
            ->select('name', 'id')
            ->where('group_name', $group_name)
            ->get();
        return $permissions;
    }

    public static function roleHasPermissions($role, $permissions)
    {
        $hasPermission = true;
        foreach ($permissions as $permission) {
            if (!$role->hasPermissionTo($permission->name)) {
                $hasPermission = false;
                return $hasPermission;
            }
        }
        return $hasPermission;
    }

    public function user_permissions(){
        return $this->hasMany('App\Models\UserPermissionAssign', 'user_id');
    }
}
