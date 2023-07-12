<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spy extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'spies';


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
}
