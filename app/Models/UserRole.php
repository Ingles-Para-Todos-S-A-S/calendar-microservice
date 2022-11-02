<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model{

    protected $table = 'user_role';
    protected $primaryKey = 'code';


    public static function getAllRole(){
        $role = UserRole::all();
        return sizeof($role)>0 ? $role : null;
    }

    public static function getByCodeRole($code){
        $role = UserRole::where('code','=',$code)->get();
        return sizeof($role)>0 ? $role : null;
    }

}