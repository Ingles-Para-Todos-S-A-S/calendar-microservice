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
            $bgColor = $events[$i]->googleEvent->colorId;

            switch ($bgColor) {
                case 11:
                    $googleColor = '#C80006';
                    break;
                case 4:
                    $googleColor = '##DF6560';
                    break;
                case 6:
                    $googleColor = '##EE3918';
                    break;
                case 5:
                    $googleColor = '#F4D03F';
                    break;
                case 2:
                    $googleColor = '##2DAA66';
                    break;
                case 10:
                    $googleColor = '#126F33';
                    break;
                case 7:
                    $googleColor = '#1288DE';
                    break;
                case 9:
                    $googleColor = '#313AA6';
                    break;
                case 1:
                    $googleColor = '#6670BF';
                    break;
                case 3:
                    $googleColor = '#7A0099';
                    break;
                default:
                    $googleColor = '#4E4E4E';
                    break;
            }

            


            $event = [
                'calendarId'=> $events[$i]->calendarId,
                'anyoneCanAddSelf'=> $events[$i]->googleEvent->anyoneCanAddSelf,
                'attendees'=> $events[$i]->googleEvent->attendees,
                'attendeesOmitted'=>$events[$i]->googleEvent->attendeesOmitted,
                'color'=> $googleColor,
                'textColor'=> $googleColor,
                'conferenceData'=>$events[$i]->googleEvent->conferenceData,
                'created'=>$events[$i]->googleEvent->created,
                'creator'=>$events[$i]->googleEvent->creator,
                'descriptionEvent'=> $events[$i]->googleEvent->description,
                'end'=> $end,
                'etag'=> $events[$i]->googleEvent->etag,
                'eventType'=> $events[$i]->googleEvent->eventType,
                'guestsCanInviteOthers'=> $events[$i]->googleEvent->guestsCanInviteOthers,
                'guestsCanModify'=> $events[$i]->googleEvent->guestsCanModify,
                'guestsCanSeeOtherGuests'=> $events[$i]->googleEvent->guestsCanSeeOtherGuests,
                'hangoutLink'=> $events[$i]->googleEvent->hangoutLink,
                'htmlLink'=> $events[$i]->googleEvent->htmlLink,
                'iCalUID'=> $events[$i]->googleEvent->iCalUID,
                'id'=> $events[$i]->googleEvent->id,
                'locationEvent'=> $events[$i]->googleEvent->location,
                'locked'=> $events[$i]->googleEvent->locked,
                'organizer'=> $events[$i]->googleEvent->organizer,
                'originalStartTime'=> $events[$i]->googleEvent->hangoutLink,
                'privateCopy'=> $events[$i]->googleEvent->privateCopy,
                'recurrence'=> $events[$i]->googleEvent->recurrence,
                'recurringEventId'=> $events[$i]->googleEvent->recurringEventId,
                'reminders'=> $events[$i]->googleEvent->reminders,
                'sequence'=> $events[$i]->googleEvent->sequence,
                'start'=> $start,
                'status'=> $events[$i]->googleEvent->status,
                'title'=> $events[$i]->googleEvent->summary,
                'transparency'=> $events[$i]->googleEvent->transparency,
                'updated'=> $events[$i]->googleEvent->updated,
                'visibility'=> $events[$i]->googleEvent->visibility,
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

        $events = Event::getEventByDateTime($startTime, $endTime, $queryParameters);

        $eventsByDate=[];

        for ($i=0; $i < sizeof($events); $i++) { 
            $startD = $events[$i]->googleEvent->start->date;
            if ($startD == null) {
                $start = $events[$i]->googleEvent->start->dateTime;
                $end = $events[$i]->googleEvent->end->dateTime;
            }else if ($startD != null){
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
            array_push($eventsByDate, $event);
        }

        return $eventsByDate;

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