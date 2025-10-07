<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class IdRoll extends Model
{
    protected $table = 'id_roll';
    public $timestamps = false;

    protected function castAttribute($key, $value)
    {
        if (! is_null($value)) {
            return parent::castAttribute($key, $value);
        }

        switch ($this->getCastType($key)) {
            case 'int':
            case 'integer':
                return (int) 0;
            case 'real':
            case 'float':
            case 'double':
                return (float) 0;
            case 'string':
                return '';
            case 'bool':
            case 'boolean':
                return false;
            case 'object':
            case 'array':
            case 'json':
                return [];
            case 'collection':
                return new BaseCollection();
            case 'date':
                return $this->asDate('0000-00-00');
            case 'datetime':
                return $this->asDateTime('0000-00-00');
            case 'timestamp':
                return $this->asTimestamp('0000-00-00');
            default:
                return $value;
        }
    }


    //Create Admission Rules and Validation
	public static $rules = ['session'		=> 'required'];

	public static function validate($data) {

		return Validator::make($data, self::$rules);

	}
}
