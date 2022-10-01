<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;

class CalendarService {

    public static function getCalendarId($idcalendar){
        return Event::get2($idcalendar);
    }

    public static function getAllEvent(){
        return $events = Event::get('03890b528576b8dd8c1ea873042149496f92272364efa16c3b4a61f226c3fe18@group.calendar.google.com');
    }

    public static function getEventFirst(){
        $eventId = Event::get()->first()->id;
    }


    public static function addEventByDayTime(){
        $event = new Event;

        $event->name = 'A new event';
        $event->description = 'Event description';
        $event->startDateTime = Carbon::now();
        $event->endDateTime = Carbon::now()->addHour();
        $event->addAttendee([
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'comment' => 'Lorum ipsum',
        ]);
        $event->addAttendee(['email' => 'anotherEmail@gmail.com']);

        $event->save();
    }


    public static function addEventSatic(){
        Event::create([
            'name' => 'A new event Static',
            'startDateTime' => Carbon::now(),
            'endDateTime' => Carbon::now()->addHour(),
         ]);
    }

    public static function addEventAllDay(){
        $event = new Event;
        
        $event->calendarId = 'nietojr1@gmail.com';
        $event->name = 'A new full day event';
        $event->description = 'Event description';
        $event->startDate = Carbon::now();
        $event->endDate = Carbon::now()->addDay();

        $event->save();
    }

    public static function deleteEventByCode($idEvent){
        $event = Event::find($idEvent);

        $event->delete();
    }


    public static function updateEvent(){
        $firstEvent = $events->first();
        $firstEvent->name = 'updated name';
        $firstEvent->save();

        $firstEvent->update(['name' => 'updated again']);
    }

    public static function updateEventByCode($idEvent){
        $event = Event::find($idEvent);

        $event->name = 'My updated title';
        $event->save();

    }

}