<?php

namespace App\Http\Controllers;

use App\Services\ClassRoomService;
use Illuminate\Http\Request;
use App\Models\ApiResponse;

class ClassRoomController extends Controller {

    public function getClassRoomByIdCalendar(Request $request) {
        if(isset($request->params)) {
            return ClassRoomService::getClassRoomByIdCalendar(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }

    public function getClassRoomByModality(Request $request) {
        if(isset($request->params)) {
            return ClassRoomService::getClassRoomByModality(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }

    public function getClassRoomByIdCalMod(Request $request) {
  
        if(isset($request->params)) {
            return ClassRoomService::classRoomByIdCalMod(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }

    function getAllClassRoom() {
        return ClassRoomService::allClassRoom();
    }


}
