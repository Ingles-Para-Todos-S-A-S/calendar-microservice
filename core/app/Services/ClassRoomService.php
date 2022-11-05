<?php

namespace App\Services;

use App\Models\ApiResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CourseModality;
use App\Models\CourseClassroom;


class ClassRoomService
{

    public static function getClassRoomByIdCalendar(Request $request)
    {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if ($userRole >= 10 || $userRole <= 19) {

            $data = CourseClassroom::ClassRoomByIdCalendar($request->id_calendar);

            $data[0]->course_modality = CourseModality::find($data[0]->course_modality)->name;

            $mesageCode = is_null($data) ? 2 : 1;

            return ApiResponse::response($mesageCode, $data);

        } else {
            
            return ApiResponse::response(5, null);
        }
    }

    public static function getClassRoomByModality(Request $request)
    {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if ($userRole >= 10 || $userRole <= 19) {

            $data = CourseClassroom::ClassRoomByModality($request->course_modality);

            $mesageCode = is_null($data) ? 2 : 1;

            return ApiResponse::response($mesageCode, $data);

        } else {
            
            return ApiResponse::response(5, null);
        }
    }

    public static function classRoomByIdCalMod(Request $request)
    {

            $data = CourseClassroom::classRoomIdCalMod($request->id_calendar, $request->course_modality);

            $data[0]->course_modality = CourseModality::find($data[0]->course_modality)->name;

            $mesageCode = is_null($data) ? 2 : 1;

            return ApiResponse::response($mesageCode, $data);
    }

    public static function allClassRoom() {
        $data = CourseClassroom::All();
        $messageCode = sizeof($data)>0 ? 1 : 2;

        return ApiResponse::response($messageCode,$data);
    }
}
