<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseType extends Model{

    protected $table = 'course_type';
    protected $primaryKey = 'code';
    protected $hidden = ['updated_at', 'created_at'];

    public static function prepareToPresent($courseType) {
        $courseType->academic_plan = AcademicPlan::find($courseType->academic_plan)->name;
        return $courseType;
    }

    public static function getCourseTypeByCode($code) {
        $courseType = CourseType::find($code);
        return $courseType !=null ? $courseType : null;
    }

}
