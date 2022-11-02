<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Course extends Model{

    protected $table = 'course';
    protected $primaryKey = 'code';

    protected $hidden = ['updated_at', 'created_at'];

    public static function prepareToPresent($course) {
        $presentCourse = $course;
        $teacher = User::find($course->teacher);

        $presentCourse->teacher = $teacher->name.' '.$teacher->last_name;
        $presentCourse->course_type = CourseType::prepareToPresent(CourseType::find($course->course_type));
        $presentCourse->english_level = EnglishLevel::find($course->english_level)->name;
        $presentCourse->agreement = Agreement::find($course->agreement)->name;
        $presentCourse->course_schedule = CourseSchedule::presentSchedule($presentCourse->course_schedule);

        return $presentCourse;
    }

    public static function getCourseByTeacher($teacherCode) {
        $courses = Course::where('teacher','=', $teacherCode)->get();
        return sizeof($courses) > 0 ? $courses : null;
    }

    public static function getCourseFilteredByTeacher($teacherCode, $filterType, $filter) {
        switch ($filterType) {
            case 1:
                $courses = Course::where('teacher','=', $teacherCode)
                                 ->where('english_level','=',$filter)
                                 ->get();
                return sizeof($courses) > 0 ? $courses : null;
                break;

            case 2:
                $courses = Course::where('teacher','=', $teacherCode)
                                 ->where('course_type','=',$filter)
                                 ->get();
                return sizeof($courses) > 0 ? $courses : null;
                break;

            case 3:
                $courses = Course::where('teacher','=', $teacherCode)
                                 ->where('agreement','=',$filter)
                                 ->get();
                return sizeof($courses) > 0 ? $courses : null;
                break;

            default:
                return null;
                break;
        }
    }

    public static function getCourseByLevel($level) {
        $courses = Course::where('english_level','=', $level)->get();
        return sizeof($courses)>0 ? $courses : null;
    }

    public static function getCourseByType($type) {
        $day = Carbon::now()-> format('Y-m-d');


        $courses = Course::where('course_type','=', $type)
                            ->whereDate('finish_date','<',$day)->get();
        return sizeof($courses)>0 ? $courses : null;
    }

    public static function getAllCourses() {
        $courses = Course::all();
        return sizeof($courses)>0 ? $courses : null;
    }

    public static function presentCourseForEdit($course) {
        $presentCourse = $course;
        $teacher = User::find($course->teacher);

        $presentCourse->teacher = $teacher->name;
        $presentCourse->course_type = CourseType::prepareToPresent(CourseType::find($course->course_type));
        $presentCourse->english_level = EnglishLevel::find($course->english_level)->name;
        $presentCourse->agreement = Agreement::find($course->agreement)->name;
        $presentCourse->course_schedule = CourseSchedule::presentScheduleEdit($presentCourse->course_schedule);

        return $presentCourse;
    }

    public static function getCourseForCOD($level, $agreement, $type){
        $response = array();

        $courses = Course::where('agreement', '=', $agreement)
                            ->where('course_type', '=', $type)
                            ->where('english_level', '=', $level)->get();

        array_push($response, array(
            'code' => '0',
            'name' => 'Sin asignar'
        ));

        for($i=0; $i < sizeof($courses); $i++) {
            $presentCourse = Course::find($courses[$i]->code);

            $teacher = User::find($presentCourse->teacher);
            $teacher = $teacher->name.' '.$teacher->last_name;

            $english_level = EnglishLevel::find($level)->name;

            $schedules = CourseSchedule::presentSchedule($presentCourse->course_schedule);
            $schedule = '';

            for($j=0; $j<sizeof($schedules); $j++) {
                $schedule = $schedule.' '.$schedules[$j];
            }

            $courseName = $english_level.' - '. $teacher.' - '.$schedule;
            $courseResponse = array(
                'code' => $presentCourse->code,
                'name' => $courseName
            );

            array_push($response, $courseResponse);
        }

        return $response;
    }

    public static function getAllCourseStartDate($codeCoruse) {
        $dayActuality = Carbon::now()-> format('Y-m-d');

            $courses = Course::whereDate('to_start','>=', $dayActuality)->
                                where('course_type', '=', $codeCoruse )->
                                get();
        return sizeof($courses)>0 ? $courses : null;
    }

}
