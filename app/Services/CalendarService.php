<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;
use Date;

class CalendarService {

    public static function getCalendarId($idcalendar){
        return Event::getEventByIdCalendar($idcalendar);
    }

    public static function getEventByDay(Request $request){

        $startTime = Carbon::parse($request->startTime, 'America/Bogota');

        $endTime = Carbon::parse($request->endTime, 'America/Bogota');
        // $endTime =(clone $startTime)->addHour(4);
        $queryParameters= [
            'calendarId'=> $request->idCalendar,
        ];

        return $events = Event::getEventByDateTime($startTime, $endTime, $queryParameters);
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