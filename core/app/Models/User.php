<?php

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable{
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'code';
    protected $hidden = ['password', 'token' ,'updated_at', 'created_at'];

    public static function serveToUser($user) {
        $userToPresent = $user;

        $userToPresent->city = City::find($user->city)->name;

        return $userToPresent;
    }

    public static function getRoleByToken($token){
        $user = User::where('token','=',$token)->get();
        return sizeof($user)>0 ? $user[0]->role : -1;
    }

    public static function getUserByToken($token){
        $user = User::where('token','=',$token)->get();
        return sizeof($user)>0 ? $user[0] : null;
    }

    public static function generateToken() {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXZ';
        $input_length = strlen($permitted_chars);
        $random_string = '';
        for($i = 0; $i < 200; $i++) {
            $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    public static function getUserLogin($user, $password) {
        $user = User::where('email','=', $user)
                        ->orWhere('email_ipt','=', $user)->get();

        return sizeof($user)>0 ? ($user[0]->password == $password ? $user[0] : null) : null;
    }

    public static function getUserByPhone($phone){
        $user = User::where('phone_number','=', $phone)->get();
        return sizeof($user)>0 ? $user[0] : null;
    }

    public static function isUniqueUser($type, $idCard, $email, $indicative, $phone, $nickname) {
        $userIdCard = User::where('id_card_type','=',$type)->where('id_card','=',$idCard)->get();
        $userPhone = User::where('phone_indicative','=',$indicative)->where('phone_number','=',$phone)->get();
        $userEmail = User::where('email','=',$email)->orWhere('email_ipt','=',$email)->get();
        if( !is_null($nickname) ) {
            $countUser = sizeof(User::where('nick_name','=',$nickname)->get());
        } else {
            $countUser = 0;
        }

        $counting = sizeof($userIdCard) + sizeof($userPhone) + sizeof($userEmail) + $countUser;

        return ($counting==0);
    }

    public static function getTeachers() {
        $teachers = User::where('role','=','10')
                            ->orWhere('role','=','11')
                            ->orWhere('role','=','12')
                            ->get();

        return sizeof($teachers)>0 ? $teachers : null;
    }

    public static function getStudents() {
        $students = User::where('role','=','13')->get();

        return sizeof($students)>0 ? $students : null;
    }

    public static function getStudent($param) {
        $student = User::where(DB::raw("CONCAT(`name`,' ',`last_name`)"),'like','%'.$param.'%')
                        ->orwhere('id_card','like','%'.$param.'%')
                        ->orwhere('phone_number','like','%'.$param.'%')
                        ->get();

        return sizeof($student)>0 ? $student : null;
    }

    public static function getPossibleStudents(){
        $possibleStudents = User::where('role','=','15')
                            ->get(array('code', 'name','last_name', 'id_card', 'birthdate', 'email', 'phone_number', 'city'));

        return sizeof($possibleStudents)>0 ? $possibleStudents : null;
    }

    public static function getAllAdviser(){

               $adviser = User::where('role','=','40')
                            ->orWhere('role','=','41')
                            ->orWhere('role','=','42')
                            ->get(array('code', 'id_card', 'name', 'last_name', 'nick_name', 'email', 'phone_number', 'gender', 'city', 'birthdate', 'role', 'service_hour', 'email_ipt', 'updated_at', 'id_card_type'));

        return sizeof($adviser)>0 ? $adviser : null;
    }

    public static function getByRoleUser($code) {
        $dataAdvisor = User::where('role','=',$code)
                            ->get(array('code', 'id_card', 'name', 'last_name', 'nick_name', 'email', 'phone_number', 'gender', 'city', 'birthdate', 'role', 'service_hour', 'email_ipt', 'updated_at', 'id_card_type'));
        return sizeof($dataAdvisor)>0 ? $dataAdvisor : null;
    }

    public static function getToUser($user) {
        $userToPresent = $user;

        $userToPresent->city = City::find($user->city)->name;
        $userToPresent->role = UserRole::find($user->role)->role;
        $userToPresent->id_card_type = IdCardtype::find($user->id_card_type)->acronym;


        return $userToPresent;
    }

    public static function getAllAdministrative(){

        $administrative = User::where('role','=','30')
                            ->orWhere('role','=','31')
                            ->orWhere('role','=','32')
                            ->get(array('code', 'id_card', 'name', 'last_name', 'nick_name', 'email', 'phone_number', 'gender', 'city', 'birthdate', 'role', 'service_hour', 'email_ipt', 'updated_at', 'id_card_type'));

        return sizeof($administrative)>0 ? $administrative : null;
    }

    public static function getByCodeStudent($code) {
        $student = User::where('code','=',$code)
                         ->where ('role','=', '13')
                         ->get();

        return sizeof($student)>0 ? $student : null;
    }

    public static function getTeachersVal() {
        $teachers = User::where('email_ipt','!=', "")
                            ->where('role','>=','10')
                            ->Where('role','<=','12')
                            ->get();

        return sizeof($teachers)>0 ? $teachers : null;
    }

}
