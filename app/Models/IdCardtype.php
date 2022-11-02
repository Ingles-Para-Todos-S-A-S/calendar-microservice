<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdCardtype extends Model{

    protected $table = 'type_id_card';
    protected $primaryKey = 'code';

    protected $hidden = ['updated_at','created_at'];


}
