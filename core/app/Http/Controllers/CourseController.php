<?php

namespace App\Http\Controllers;

use App\Models\ApiResponse;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller {

    function createCourse(Request $request) {
        if(isset($request->params)) {
            return CourseService::createCourse(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }

    function editCourse(Request $request) {
        if(isset($request->params)) {
            return CourseService::editCourse(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }

    function getAllCourses(Request $request) {
        if(isset($request->params)) {
            return CourseService::getAllCourses(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }

    function getCourseByTeacher(Request $request) {
        if(isset($request->params)) {
            return CourseService::getCourseByTeacher(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }

    function getCourseFilteredByTeacher(Request $request) {
        if (isset($request->params)) {
            return CourseService::getCourseFilteredByTeacher(new Request($request->params));
        } else {
            return ApiResponse::response(3, null);
        }

    }

    function getCoursesByLevel(Request $request) {
        if(isset($request->params)) {
            return CourseService::getCoursesByLevel(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }

    function getCourseForCOD(Request $request) {
        if(isset($request->params)) {
            return CourseService::getAvailableCourseCod(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }

    function getCourseByCode(Request $request) {
        if(isset($request->params)) {
            return CourseService::getCourseByCode(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }

    function getCourseByType(Request $request){
        if(isset($request->params)) {
            return CourseService::getCourseByType(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }

    function getCourseByStartDate(Request $request){
        if(isset($request->params)) {
            return CourseService::getCourseByStartDate(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }


}
