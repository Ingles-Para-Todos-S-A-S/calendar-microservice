<?php

namespace App\Http\Controllers;

use App\Services\CalendarService;
use Illuminate\Http\Request;

class CalendarController extends Controller {

    function getEventCalendarByIdCalendar($idcalendar) {
        return CalendarService::getCalendarId($idcalendar);
    }

    function getEventCalendarAllEvent() {
        return CalendarService::getAllEvent();
    }

    function addEventCalendarDyDayTime() {
        return CalendarService::addEventByDayTime();
    }

    function addEventCalendarAllDay() {
        return CalendarService::addEventAllDay();
    }

    function updateEventCalendarByIdEvent($idEvent) {
        return CalendarService::updateEventByCode($idEvent);
    }

    function deleteEventCalendarByIdEvent($idEvent) {
        return CalendarService::deleteEventByCode($idEvent);
    }


}