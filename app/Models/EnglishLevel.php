<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnglishLevel extends Model{
    protected $table = 'english_levels';
    protected $primaryKey = 'code';

    protected $hidden = ['updated_at', 'created_at'];


    public static function getByCode($code){
        $englishLevel = EnglishLevel::where('code','=',$code)
                            ->select('code','name')        
                            ->get();
        return sizeof($englishLevel)>0 ? $englishLevel : null;
    }

}