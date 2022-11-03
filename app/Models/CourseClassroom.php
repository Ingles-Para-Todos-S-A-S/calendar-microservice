<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseClassroom extends Model{

    protected $table = 'course_classroom';
    protected $primaryKey = 'code';


    public static function ClassRoomByIdCalendar($idCalendar){

        $classroom = CourseClassroom::where('id_calendar','=',$idCalendar)->get();

        return sizeof($classroom)>0 ? $classroom : null;
    }

    public static function ClassRoomByModality($codModality){

        $classroom = CourseClassroom::where('course_modality','=', $codModality)->get();

        return sizeof($classroom)>0 ? $classroom : null;
    }

    public static function classRoomIdCalMod($idCalendar, $codModality){


        $classroom = CourseClassroom::where('id_calendar','=', $idCalendar)
                                    ->where('course_modality','=', $codModality)
                                    ->get();

        return sizeof($classroom)>0 ? $classroom : null;
    }

    public static function ClassRoomByModId($codModality){

        $classroom = CourseClassroom::where('id_calendar','!=', "")
                                    ->where('course_modality','=', $codModality)
                                    ->get();

        return sizeof($classroom)>0 ? $classroom : null;
    }

    

}
