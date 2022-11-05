<?php

namespace App\Services;

use App\Models\ApiResponse;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class CourseService {

    public static function createCourse(Request $request) {
       $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if($userRole == 10 || $userRole == 11) {
            try {
                $schedule = CourseService::setSchedule($request);
                $schedule->save();
                $course = CourseService::setCourse($request);
                $course->course_schedule = $schedule->code;
                $course->save();
                return ApiResponse::response(1, Course::prepareToPresent($course));
            } catch (\Throwable $th) {
                $schedule->delete();
                return ApiResponse::response(3, null);
            }
        } else {
           return ApiResponse::response(5, null);
        }
    }

    public static function editCourse(Request $request) {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if($userRole == 10) {
            try {
                $course = Course::find($request->course_code);
                $schedule = CourseService::setSchedule($request);
                $schedule->save();

                $course->teacher= $request->teacher;
                $course->capacity = $request->capacity;
                $course->more_days = $request->more_days;
                $course->course_schedule = $schedule->code;

                $course->save();
                return ApiResponse::response(1, Course::prepareToPresent($course));
            } catch (\Throwable $th) {
                return ApiResponse::response(3, null);
            }
        } else {
           return ApiResponse::response(5, null);
        }
    }

    public static function getAllCourses($request) {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if($userRole > 0) {
            $courses = Course::getAllCourses();
            $courses = CourseService::paginateCourses($courses, $request->record, $request->page);
            $messageCode = is_null($courses) ? 2 : 1;
            return ApiResponse::response($messageCode, $courses);
        } else {
            return ApiResponse::response(5, null);
        }
    }

    public static function getCourseByTeacher($request) {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if ($userRole > 9 && $userRole < 13) {
            try {
                if ($userRole == 10) {
                    $courses = Course::getAllCourses();
                    $courses = CourseService::paginateCourses($courses, $request->record, $request->page);
                    $messageCode = is_null($courses) ? 2 : 1;
                    return ApiResponse::response($messageCode, $courses);
                } else {
                    $courses = Course::getCourseByTeacher($request->teacher_code);
                    $courses = CourseService::paginateCourses($courses, $request->record, $request->page);
                    $messageCode = is_null($courses) ? 2 : 1;
                    return ApiResponse::response($messageCode, $courses);
                }
            } catch (\Throwable $th) {
                return ApiResponse::response(3, null);
            }
        } else {
            return ApiResponse::response(5, null);
        }
    }

    public static function getCourseFilteredByTeacher($request) {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if ($userRole > 9 && $userRole < 13) {
            try {
                $courses = Course::getCourseFilteredByTeacher($request->teacher_code, $request->filter_type, $request->filter);
                $courses = CourseService::paginateCourses($courses, $request->record, $request->page);
                $messageCode = is_null($courses) ? 2 : 1;
                return ApiResponse::response($messageCode, $courses);
            } catch (\Throwable $th) {
                return ApiResponse::response(3, null);
            }
        } else {
            return ApiResponse::response(5, null);
        }
    }

    public static function getCoursesByLevel($request) {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if($userRole > 0) {
            try {
                $courses = Course::getCourseByLevel($request->english_level);
                $courses = CourseService::paginateCourses($courses, $request->record, $request->page);
                $messageCode = is_null($courses) ? 2 : 1;
                return ApiResponse::response($messageCode, $courses);
            } catch (\Throwable $th) {
                return ApiResponse::response(3, null);
            }
        } else {
            return ApiResponse::response(5, null);
        }
    }

    public static function getCourseByCode($request) {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if($userRole> 9 && $userRole<20) {
            try {
                $course = Course::find($request->course_code);
                $course = Course::presentCourseForEdit($course);
                $messageCode = is_null($course) ? 2 : 1;
                return ApiResponse::response($messageCode, $course);
            } catch (\Throwable $th) {
                return ApiResponse::response(3, null);
            }
        } else {
            return ApiResponse::response(5, null);
        }
    }

    public static function getCourseByType($request){
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if($userRole > 0) {
            try {
                $course = Course::getCourseByType($request->course_type);

                for ($i=0; $i < sizeof($course); $i++) {
                    $course[$i] = Course::prepareToPresent($course[$i]);
                }
                $messageCode = is_null($course) ? 2 : 1;
                return ApiResponse::response($messageCode, $course);
            } catch (\Throwable $th) {
                return ApiResponse::response(3, null);
            }
        } else {
            return ApiResponse::response(5, null);
        }
    }



    private static function setCourse($request) {
        $course = new Course;

        $course->teacher         = $request->teacher;
        $course->course_type     = $request->course_type;
        $course->to_start        = $request->to_start;
        $course->start_date      = $request->start_date;
        $course->finish_date     = $request->finish_date;
        $course->capacity        = $request->capacity;
        $course->more_days       = $request->more_days;
        $course->english_level   = $request->english_level;
        $course->agreement       = $request->agreement;

        return $course;
    }

    private static function setSchedule($request) {
        $schedule = new CourseSchedule;

        $schedule->time_start_monday    = $request->time_start_monday;
        $schedule->time_end_monday      = $request->time_end_monday;
        $schedule->classroom_monday     = $request->classroom_monday;
        $schedule->time_start_tuesday   = $request->time_start_tuesday;
        $schedule->time_end_tuesday     = $request->time_end_tuesday;
        $schedule->classroom_tuesday    = $request->classroom_tuesday;
        $schedule->time_start_wednesday = $request->time_start_wednesday;
        $schedule->time_end_wednesday   = $request->time_end_wednesday;
        $schedule->classroom_wednesday  = $request->classroom_wednesday;
        $schedule->time_start_thursday  = $request->time_start_thursday;
        $schedule->time_end_thursday    = $request->time_end_thursday;
        $schedule->classroom_thursday   = $request->classroom_thursday;
        $schedule->time_start_friday    = $request->time_start_friday;
        $schedule->time_end_friday      = $request->time_end_friday;
        $schedule->classroom_friday     = $request->classroom_friday;
        $schedule->time_start_saturday  = $request->time_start_saturday;
        $schedule->time_end_saturday    = $request->time_end_saturday;
        $schedule->classroom_saturday   = $request->classroom_saturday;

        return $schedule;
    }

    private static function paginateCourses($courses, $record, $page) {
        if(!is_null($courses)) {
            $pages = ceil(sizeof($courses)/$record);
            $responseCourses = array();

            $j = ($page-1)*$record;
            $n = $record;

            while($j<sizeof($courses) && $n>0) {
                $course = Course::prepareToPresent($courses[$j]);
                array_push($responseCourses, $course);
                $n--;
                $j++;
            }

            $response = array(
                'number_of_pages' => $pages,
                'current_page' => $page,
                'total_records' => sizeof($courses),
                'records_per_page' => $record,
                'courses' => $responseCourses
            );

            return $response;
        } else {
            return null;
        }
    }

    public static function getCourseByStartDate(Request $request)
    {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if ($userRole == 10) {

            $datacourse = Course::getAllCourseStartDate($request->coursetype);

            if ($datacourse != null) {
              
                for ($i=0; $i < sizeof($datacourse); $i++) { 
                    $datacourse[$i] = Course::prepareToPresent($datacourse[$i]);
                }
            }

            $mesageCode = is_null($datacourse) ? 2 : 1;

            return ApiResponse::response($mesageCode, $datacourse);

        } else {
            
            return ApiResponse::response(5, null);
        }
    }

}

