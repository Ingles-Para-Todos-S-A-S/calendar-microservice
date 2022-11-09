<?php

namespace App\Http\Controllers;

use App\Services\CalendarService;
use Illuminate\Http\Request;
use App\Models\ApiResponse;


class CalendarController extends Controller {

    function getEventCalendarByIdCalendar($idcalendar) {
        return 'hello word';
        return CalendarService::getCalendarId($idcalendar);
    }

    public function getEventCalendarByDate(Request $request) {
        if(isset($request->params)) {
            return CalendarService::getEventByDay(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }

    function addEventCalendarAllDay() {
        return CalendarService::addEventAllDay();
    }

    function addEventCalendarByDayTime(Request $request) {
        if(isset($request->params)) {
            // return CalendarService::addEventByDayTime(new Request($request->params));
            return CalendarService::addEventPrueba(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }

    function getCreateQueryEvent(Request $request) {
        if(isset($request->params)) {
            return CalendarService::createQueryEvent(new Request($request->params));
        }else {
            return ApiResponse::response(3, null);
        }
    }

    function updateEventCalendarByIdEvent($idEvent) {
        return CalendarService::updateEventByCode($idEvent);
    }

    function deleteEventCalendarByIdEvent($idEvent) {
        return CalendarService::deleteEventByCode($idEvent);
    }


}
