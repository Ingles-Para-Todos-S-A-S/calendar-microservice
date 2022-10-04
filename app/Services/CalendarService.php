<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;
use Date;

class CalendarService {

    public static function getCalendarId($idcalendar){
        $startTime = Carbon::now();

        $endTime = Carbon::now()->addYear();

        $queryParameters= [
            'calendarId'=> $idcalendar,
        ];
        
         $events = Event::getEventByIdCalendar($startTime, $endTime, $queryParameters);

        $allEvents=[];

        for ($i=0; $i < sizeof($events); $i++) { 
            $startD = $events[$i]->googleEvent->start->date;
            if ($startD == null) {
                $start = $events[$i]->googleEvent->start->dateTime;
                $end = $events[$i]->googleEvent->end->dateTime;
            }else {
                $start = $events[$i]->googleEvent->start->date;
                $end = $events[$i]->googleEvent->end->date;
            }

            $event = [
                'nameCalendar'=> $events[$i]->googleEvent->organizer->displayName,
                'title'=> $events[$i]->googleEvent->summary,
                'descriptionEvent'=> $events[$i]->googleEvent->description,
                'locationEvent'=> $events[$i]->googleEvent->location,
                'dateCreation'=> $events[$i]->googleEvent->created,
                'colorEvent'=> $events[$i]->googleEvent->colorId,
                'start'=> $start,
                'end'=> $end,
            ];
            array_push($allEvents, $event);
        }

        return $allEvents;
    }

    public static function getEventByDay(Request $request){

        $startTime = Carbon::parse($request->startTime, 'America/Bogota');

        $endTime = Carbon::parse($request->endTime, 'America/Bogota');

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