<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_Event;
use DateTime;
use DatePeriod;
use DateInterval;
use Date;
use App\Models\CourseClassroom;


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

            $event = CalendarService::presenToPresent($events[$i]);

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


    public static function addEventByDayTime(Request $request){

        $event = new Event;
        // $event->name = 'A new event';
        // $event->description = 'Event description';
        // $event->startDateTime = Carbon::now();
        // $event->endDateTime = Carbon::now()->addHour();

        $bgColor = $request->color;
        switch ($bgColor) {
            case '#C80006':
                $googleColor = 11;
                break;
            case '#DF6560':
                $googleColor = 4;
                break;
            case '#EE3918':
                $googleColor = 6;
                break;
            case '#F4D03F':
                $googleColor = 5;
                break;
            case '#2DAA66':
                $googleColor = 2;
                break;
            case '#126F33':
                $googleColor = 10;
                break;
            case '#1288DE':
                $googleColor = 7;
                break;
            case '#313AA6':
                $googleColor = 9;
                break;
            case '#6670BF':
                $googleColor = 1;
                break;
            case '#7A0099':
                $googleColor = 3;
                break;
            default:
                $googleColor = '#4E4E4E';
                break;
        }

        $event->calendarId = $request->calendarId;
        $event->googleEvent->anyoneCanAddSelf = $request->anyoneCanAddSelf;
        $event->googleEvent->attendees = $request->attendees;
        $event->googleEvent->attendeesOmitted = $request->attendeesOmitted;
        $event->googleEvent->colorId = $request->color;
        //$event->googleEvent->conferenceData->conferenceSolution->name = $request->conferenceDataName;
        $event->googleEvent->created = $request->created;
        // $event->googleEvent->creator->id = $request->creatorId;
        // $event->googleEvent->creator->displayName = $request->creatorDisplayName;
        // $event->googleEvent->creator->email = $request->creatorEmail;
        $event->googleEvent->description = $request->descriptionEvent;
        // $event->googleEvent->end->date = null;
        $event->endDate = $request->end;
        // $event->googleEvent->end->timeZone = "America/Bogota";
        // $event->googleEvent->etag = $request->etag;
        $event->googleEvent->eventType = $request->eventType;
        $event->googleEvent->guestsCanInviteOthers = $request->guestsCanInviteOthers;
        $event->googleEvent->guestsCanModify = $request->guestsCanModify;
        $event->googleEvent->guestsCanSeeOtherGuests = $request->guestsCanSeeOtherGuests;
        $event->googleEvent->hangoutLink = $request->hangoutLink;
        $event->googleEvent->htmlLink = $request->htmlLink;
        // $event->googleEvent->iCalUID = $request->iCalUID;
        // $event->googleEvent->id = $request->id;
        $event->googleEvent->location = $request->locationEvent;
        $event->googleEvent->locked = $request->locked;
        // $event->organizer->id = $request->organizerId;
        // $event->organizer->self = $request->organizerSelf;
        // $event->organizer->displayName = $request->organizerDisplayName;
        // $event->organizer->email = $request->organizerEmail;
        // $event->googleEvent->hangoutLink = $request->originalStartTime;
        // $event->googleEvent->privateCopy = $request->privateCopy;
        // $event->googleEvent->recurrence = $request->recurrence;
        // $event->googleEvent->recurringEventId = $request->recurringEventId;
        // $event->googleEvent->reminders->useDefault = $request->remindersUseDefault;
        $event->dateTime = $request->start;
        // $event->googleEvent->start->timneZome = "America/Bogota";
        // $event->googleEvent->status = $request->status;
        $event->googleEvent->summary = $request->title;
        // $event->googleEvent->transparency = $request->transparency;
        $event->googleEvent->updated = $request->updated;
        $event->googleEvent->visibility = $request->visibility;

        return $event->save();
    }


    public static function addEventSatic($request){

        Event::create([
            'name' => 'A new event Static',
            'startDateTime' => Carbon::now(),
            'endDateTime' => new Carbon($request->end),
        ],"nietojr1@gmail.com",[]);
    }

    public static function addEventAllDay(){
        $event = new Event;

        $event->calendarId = 'nietojr1@gmail.com';
        $event->name = 'Agregando nuevo evento todo el dia';
        $event->description = 'Event description';
        $event->startDate = Carbon::now();
        $event->endDate = Carbon::now()->addDay();

        $event->save();
    }


    public static function addEventPrueba($request){

        //Traer todos los salones de acuerdo a la modalidad y si tienen correo o idCalendar
        $classRoom = CourseClassroom::ClassRoomByModId($request->course_modality);

        //Traer todos los teacher si tienen correo o idCalendar
        $allTeachers = User::getTeachersVal();

        //Cuantos dias hay en un margen de fechas sin feriados 
       return $diasHabiles =  CalendarService::daysWeek($request->startDate, $request->numClass, $request->weekDays);
       
        $teachersAvailable = CalendarService::searchAvailability($allTeachers, $allTeachers->email_ipt, $diasHabiles, $request->startTime, $request->endTime);

       return $classRoomAvailable = CalendarService::searchAvailability($classRoom, "id_calendar", $diasHabiles, $request->startTime, $request->endTime);
    //    $dataSearch, $idCalendar, $availableDays, $startTime, $endTime


        //Filtrar Disponibilidad de (Teacher y classroom )
        $roomsAvailable[]=[];
        if ($classRoom==null) {
            return "No hay salas Creadas";
        } else {
            
            $k=0;
            for ($i=0; $i < sizeof($classRoom); $i++) { 
                $aux=false;
                for ($j=0; $j < sizeof($diasHabiles['dates']); $j++) { 
                    $newrequest=['idCalendar'=>$classRoom[$i]->id_calendar, 'startTime'=>$diasHabiles['dates'][$j].$request->startTime,'endTime'=>$diasHabiles['dates'][$j].$request->endTime];
                    $events = CalendarService::getEventByDay(new Request($newrequest));
                   if(sizeof($events)==0){
                        $aux=true;
                    }else{
                        $aux=false;
                        break;
                    }
                }
                if($aux){
                    $roomsAvailable[$k]=$classRoom[$i]->name;
                    $k++;
                }
            }
        }
        return $roomsAvailable;
        
        $event = new Event;
        // for ($i=0; $i < sizeof($request->attendees) ; $i++) {
            //     $event->addAttendee($request->attendees[$i]);
            // }
        // return  $event->googleEvent->getAttendees();
        $event->calendarId = $request->calendarId;
        $event->googleEvent->setAttendeesOmitted($request->attendeesOmitted);
        $event->googleEvent->setColorId($request->color);
        $event->googleEvent->setDescription($request->descriptionEvent);
        $event->endDateTime = new Carbon($request->end);
        $event->googleEvent->setHangoutLink($request->hangoutLink);
        $event->googleEvent->setLocation($request->locationEvent);
        $event->googleEvent->setSummary($request->title);
        $event->startDateTime = new Carbon($request->start);
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

    public static function presenToPresent($events){

        $startD = $events->googleEvent->start->date;
        if ($startD == null) {
            $start = $events->googleEvent->start->dateTime;
            $end = $events->googleEvent->end->dateTime;
        }else {
            $start = $events->googleEvent->start->date;
            $end = $events->googleEvent->end->date;
        }

        $bgColor = $events->googleEvent->colorId;

        switch ($bgColor) {
            case 11:
                $googleColor = '#C80006';
                break;
            case 4:
                $googleColor = '#DF6560';
                break;
            case 6:
                $googleColor = '#EE3918';
                break;
            case 5:
                $googleColor = '#F4D03F';
                break;
            case 2:
                $googleColor = '#2DAA66';
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
        'calendarId'=> $events->calendarId,
        'anyoneCanAddSelf'=> $events->googleEvent->anyoneCanAddSelf,
        'attendees'=> $events->googleEvent->attendees,
        'attendeesOmitted'=>$events->googleEvent->attendeesOmitted,
        'color'=> $googleColor,
        'conferenceData'=>$events->googleEvent->conferenceData,
        'created'=>$events->googleEvent->created,
        'creator'=>$events->googleEvent->creator,
        'descriptionEvent'=> $events->googleEvent->description,
        'end'=> $end,
        'etag'=> $events->googleEvent->etag,
        'eventType'=> $events->googleEvent->eventType,
        'guestsCanInviteOthers'=> $events->googleEvent->guestsCanInviteOthers,
        'guestsCanModify'=> $events->googleEvent->guestsCanModify,
        'guestsCanSeeOtherGuests'=> $events->googleEvent->guestsCanSeeOtherGuests,
        'hangoutLink'=> $events->googleEvent->hangoutLink,
        'htmlLink'=> $events->googleEvent->htmlLink,
        'iCalUID'=> $events->googleEvent->iCalUID,
        'id'=> $events->googleEvent->id,
        'locationEvent'=> $events->googleEvent->location,
        'locked'=> $events->googleEvent->locked,
        'organizer'=> $events->googleEvent->organizer,
        'originalStartTime'=> $events->googleEvent->hangoutLink,
        'privateCopy'=> $events->googleEvent->privateCopy,
        'recurrence'=> $events->googleEvent->recurrence,
        'recurringEventId'=> $events->googleEvent->recurringEventId,
        'reminders'=> $events->googleEvent->reminders,
        'sequence'=> $events->googleEvent->sequence,
        'start'=> $start,
        'status'=> $events->googleEvent->status,
        'title'=> $events->googleEvent->summary,
        'transparency'=> $events->googleEvent->transparency,
        'updated'=> $events->googleEvent->updated,
        'visibility'=> $events->googleEvent->visibility,
        ];

        return $event;
    }

    public static function daysWeek($startDate, $numClass, $weekDays){
       
        $start = new DateTime($startDate);

        $period = new DatePeriod($start, new DateInterval('P1D'), 365);

        $holidays = array('2022-11-07');

        $daySelect[]=[];

        $i=0;

        $dataCalendar;
        
        foreach($period as $dt) {
            $time = time();
            $curr = $dt->format('D');
            if(in_array($curr, $weekDays) && sizeof($daySelect)<$numClass) {
                if (!in_array($dt->format('Y-m-d'), $holidays)) {
                    $daySelect[$i]=($dt)->format('Y-m-d');
                    $i++;
                }
            }
        }
        return $dataCalendar=['dates'=>$daySelect, 'lastDate'=>end($daySelect)];
    }

    public static function searchAvailability($dataSearch, $idCalendar, $availableDays, $startTime, $endTime){
        return $idCalendar;
        $available[]=[];
        if ($dataSearch==null) {
            return "No hay salas Creadas";
        } else {
            
            $k=0;
            for ($i=0; $i < sizeof($dataSearch); $i++) { 
                $aux=false;
                for ($j=0; $j < sizeof($availableDays['dates']); $j++) { 
                    $newRequest=['idCalendar'=>$dataSearch[$i]->$idCalendar, 'startTime'=>$availableDays['dates'][$j].$startTime, 'endTime'=>$availableDays['dates'][$j].$endTime];
                    $events = CalendarService::getEventByDay(new Request($newRequest));
                   if(sizeof($events)==0){
                        $aux=true;
                    }else{
                        $aux=false;
                        break;
                    }
                }
                if($aux){
                    $available[$k]=$dataSearch[$i]->name;
                    $k++;
                }
            }
        }
        return $available;
    }


    public static function numberWeeks($startDate, $endDate){
        $firstWeek=(int) date('W',strtotime($startDate));
        $lastWeek=(int) date('W',strtotime($endDate));
        $nSena = [];
        while ($firstWeek <=  $lastWeek) {
            $nSena[] = $firstWeek;
            $firstWeek++;
        }
        return json_encode($nSena);
       
    }


}
