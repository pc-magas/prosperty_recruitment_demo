<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * A spy records in the database
 * 
 * @property int $id Primary Key
 * @property string $name Spy's name 
 * @property string $surname Spy's surname
 * @property string $agencty Spy's agency
 * @property string $birth_date Spy's Birth Date (Format YYYY-mm-dd)
 * @property string $death_date Spy's Date that has passed away (Format YYYY-mm-dd)
 * @property string $country_of_operation Country that spy currently is Operating
 * 
 */
class Spy extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'spies';

    /**
     * Columsn that would not be Serialized
     * @var array
     */
    protected $hidden = ['created_at','updated_at'];

    public function setAgencyAttribute($value){
        $value = $value??'NO-AGENCY';
        $value = trim($value);
        $value = strtoupper($value);
        

        $agencies = \Illuminate\Support\Facades\Config::get('agencies');

        if($value == 'NO-AGENCY' || in_array($value,$agencies)){
            $this->attributes['agency']=$value;
            return;
        }
        
        throw new \InvalidArgumentException('Agency Is Invalid');
    }

    public function setDeathDateAttribute($value){
        
        if($value === null || empty($this->attributes['birth_date'])){
            $this->attributes['death_date']=$value;
            return;
        }


        $value = new Carbon($value);
        $birth_date = new Carbon($this->attributes['birth_date']);

        if($value->lessThanOrEqualTo($birth_date)){
            throw new \InvalidArgumentException('Death date must be greater that birth date');
        }

        $this->attributes['death_date']=$value->format('Y-m-d');
    }

    public function setBirthDateAttribute($value){
        
        if($value === null || empty($this->attributes['death_date'])){
            $this->attributes['birth_date']=$value;
            return;
        }


        $value = new Carbon($value);
        $death_date = new Carbon($this->attributes['death_date']);

        if($value->greaterThanOrEqualTo($death_date)){
            throw new \InvalidArgumentException('Death date must be greater that birth date');
        }

        $this->attributes['birth_date']=$value->format('Y-m-d');
    }
}
