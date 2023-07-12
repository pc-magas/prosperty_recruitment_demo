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

        $this->attributes['agency']=$value;
    }

}
