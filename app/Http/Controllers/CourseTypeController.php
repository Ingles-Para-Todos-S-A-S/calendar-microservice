<?php

namespace App\Http\Controllers;

use App\Models\ApiResponse;
use App\Services\CourseTypeService;
use Illuminate\Http\Request;

class CourseTypeController extends Controller {

    function getNumClassByCode($code){
            return CourseTypeService::getNumClassByCode($code);
    }
    
}
