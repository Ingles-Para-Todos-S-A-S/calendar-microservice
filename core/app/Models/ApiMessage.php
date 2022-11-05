<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiMessage extends Model{

    protected $table = 'api_message';
    protected $primaryKey = 'code';

}
