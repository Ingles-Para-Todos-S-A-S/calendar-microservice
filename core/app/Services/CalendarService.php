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
use App\Services\Colombia;
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
        $event->googleEvent->location = $request->locationEvent;
        $event->googleEvent->locked = $request->locked;
        // $event->organizer->self = $request->organizerSelf;
        // $event->organizer->displayName = $request->organizerDisplayName;
        // $event->organizer->email = $request->organizerEmail;
        // $event->googleEvent->hangoutLink = $request->originalStartTime;
        // $event->googleEvent->privateCopy = $request->privateCopy;
        // $event->googleEvent->recurrence = $request->recurrence;
        // $event->googleEvent->recurringEventId = $request->recurringEventId;
        // $event->googleEvent->reminders->useDefault = $request->remindersUseDefault;
        $event->dateTime = $request->start;
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

    public static function createQueryEvent($request){
       
        $yearConsult = Carbon::parse($request->startDate)->format('Y');

        $holidaysFull = CalendarService::dayHolidays($yearConsult);

        $classRoom = CourseClassroom::ClassRoomByModId($request->course_modality);

        $allTeachers = User::getTeachersVal();

        $availableDays =  CalendarService::daysWeek($request->startDate, $request->numClass, $request->weekDays, $holidaysFull);

        $newAvailableDays = $availableDays['dates'];

            foreach ($newAvailableDays as &$day) {
                $day = Carbon::parse($day);
                $day1 = $day->format('l');
                $day2 = $day->format('j');
                $moth = $day->format('F');
                $year = $day->format('Y');
                $day = ['shortDate'=>$day->format('Y-m-d'), 'longDate'=>$day1.', '.$day2.' '.$moth.' '.$year];
            }
            unset($day);
            $newAvailableDays;

        $numberWeeks=  CalendarService::numberWeeks($request->startDate, ($availableDays['lastDate']));

        $teachersAvailable = CalendarService::searchAvailability($allTeachers, 'email_ipt', $availableDays, $request->startTime, $request->endTime);

        $classRoomAvailable = CalendarService::searchAvailability($classRoom, "id_calendar", $availableDays, $request->startTime, $request->endTime);

        return $result = ['Teachers'=> $teachersAvailable, 'ClassRoom'=>$classRoomAvailable, 'schoolDays'=>$newAvailableDays, 'weeksClass'=>$numberWeeks, 'completionClass'=>$availableDays['lastDate']];
    }

    public static function addEventPrueba($request){
        $event = new Event;
        for ($i=0; $i < sizeof($request->attendees) ; $i++) {
                $event->addAttendee($request->attendees[$i]);
            }
        return  $event->googleEvent->getAttendees();
        $event->calendarId = $request->calendarId;
        $event->googleEvent->setAttendeesOmitted($request->attendeesOmitted);
        $event->googleEvent->setColorId($request->color);
        $event->googleEvent->setDescription($request->descriptionEvent);
        $event->startDateTime = new Carbon($request->start);
        $event->endDateTime = new Carbon($request->end);
        $event->googleEvent->setHangoutLink($request->hangoutLink);
        $event->googleEvent->setLocation($request->locationEvent);
        $event->googleEvent->setSummary($request->title);
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

    public static function daysWeek($startDate, $numClass, $weekDays, $dataHolidays){

        $start = new DateTime($startDate);
        $period = new DatePeriod($start, new DateInterval('P1D'), 365);
        $holidays = $dataHolidays;
        $daySelect[]=[];
        $i=0;
        $dataCalendar;

        foreach($period as $dt) {
            $time = time();
            $curr = $dt->format('l');
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
                        $available[$k]=['code'=>$dataSearch[$i]->code, 'name'=>$dataSearch[$i]->name, 'idCalendar'=>$dataSearch[$i]->$idCalendar];
                        // $available[$k]=$dataSearch[$i];
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
        json_encode($nSena);
        return sizeof($nSena);
    }

    public static function dayHolidays($yearReceived){
        $holidayColombia = [
            '2020' => [
                '2020-01-01', '2020-01-05', '2020-01-12', '2020-01-13', '2020-01-19', '2020-01-26', '2020-02-02', '2020-02-09', '2020-02-16', '2020-02-23', '2020-03-01', '2020-03-08', '2020-03-15', '2020-03-22', '2020-03-23', '2020-03-29', '2020-04-05', '2020-04-09', '2020-04-10', '2020-04-12', '2020-04-19', '2020-04-26', '2020-05-01', '2020-05-03', '2020-05-10', '2020-05-17', '2020-05-24', '2020-05-25', '2020-05-31', '2020-06-07', '2020-06-14', '2020-06-15', '2020-06-21', '2020-06-28', '2020-07-05', '2020-07-06', '2020-07-12', '2020-07-19', '2020-07-20', '2020-07-26', '2020-08-02', '2020-08-07', '2020-08-09', '2020-08-15', '2020-08-16', '2020-08-23', '2020-08-30', '2020-09-06', '2020-09-13', '2020-09-20', '2020-09-27', '2020-10-04', '2020-10-11', '2020-10-18', '2020-10-19', '2020-10-25', '2020-11-01', '2020-11-08', '2020-11-15', '2020-11-16', '2020-11-22', '2020-11-29', '2020-12-06', '2020-12-08', '2020-12-13', '2020-12-20', '2020-12-25', '2020-12-27'
            ],
            '2021' => [
                '2021-01-01', '2021-01-03', '2021-01-10', '2021-01-11', '2021-01-17', '2021-01-24', '2021-01-31', '2021-02-07', '2021-02-14', '2021-02-21', '2021-02-28', '2021-03-07', '2021-03-14', '2021-03-21', '2021-03-22', '2021-03-28', '2021-04-01', '2021-04-02', '2021-04-04', '2021-04-11', '2021-04-18', '2021-04-25', '2021-05-01', '2021-05-02', '2021-05-09', '2021-05-16', '2021-05-17', '2021-05-23', '2021-05-30', '2021-06-06', '2021-06-07', '2021-06-13', '2021-06-14', '2021-06-20', '2021-06-27', '2021-07-04', '2021-07-05', '2021-07-11', '2021-07-18', '2021-07-20', '2021-07-25', '2021-08-01', '2021-08-07', '2021-08-08', '2021-08-15', '2021-08-16', '2021-08-22', '2021-08-29', '2021-09-05', '2021-09-12', '2021-09-19', '2021-09-26', '2021-10-03', '2021-10-10', '2021-10-17', '2021-10-18', '2021-10-24', '2021-10-31', '2021-11-01', '2021-11-07', '2021-11-08', '2021-11-14', '2021-11-15', '2021-11-21', '2021-11-28', '2021-12-05', '2021-12-08', '2021-12-12', '2021-12-19', '2021-12-25', '2021-12-26'
            ],
            '2022' => [
                '2022-01-01', '2022-01-02', '2022-01-09', '2022-01-10', '2022-01-15', '2022-01-16', '2022-01-23', '2022-01-30', '2022-02-06', '2022-02-13', '2022-02-20', '2022-02-27', '2022-03-06', '2022-03-13', '2022-03-20', '2022-03-21', '2022-03-27', '2022-04-03', '2022-04-10', '2022-04-14', '2022-04-15', '2022-04-17', '2022-04-24', '2022-05-01', '2022-05-08', '2022-05-15', '2022-05-22', '2022-05-29', '2022-05-30', '2022-06-04', '2022-06-05', '2022-06-12', '2022-06-17', '2022-06-19', '2022-06-20', '2022-06-26', '2022-06-27', '2022-07-03', '2022-07-04', '2022-07-10', '2022-07-17', '2022-07-20', '2022-07-24', '2022-07-31', '2022-08-07', '2022-08-14', '2022-08-15', '2022-08-21', '2022-08-28', '2022-09-04', '2022-09-11', '2022-09-18', '2022-09-25', '2022-10-02', '2022-10-09', '2022-10-16', '2022-10-17', '2022-10-23', '2022-10-30', '2022-11-06', '2022-11-07', '2022-11-13', '2022-11-14', '2022-11-20', '2022-11-27', '2022-12-04', '2022-12-08', '2022-12-11', '2022-12-18', '2022-12-25'
            ],
            '2023' => [
                '2023-01-01', '2023-01-08', '2023-01-09', '2023-01-15', '2023-01-22', '2023-01-29', '2023-02-05', '2023-02-12', '2023-02-19', '2023-02-26', '2023-03-05', '2023-03-12', '2023-03-19', '2023-03-20', '2023-03-26', '2023-04-02', '2023-04-06', '2023-04-07', '2023-04-09', '2023-04-16', '2023-04-23', '2023-04-30', '2023-05-01', '2023-05-07', '2023-05-14', '2023-05-21', '2023-05-22', '2023-05-28', '2023-06-04', '2023-06-11', '2023-06-12', '2023-06-18', '2023-06-19', '2023-06-25', '2023-07-02', '2023-07-03', '2023-07-09', '2023-07-16', '2023-07-20', '2023-07-23', '2023-07-30', '2023-08-06', '2023-08-07', '2023-08-13', '2023-08-20', '2023-08-21', '2023-08-27', '2023-09-03', '2023-09-10', '2023-09-17', '2023-09-24', '2023-10-01', '2023-10-08', '2023-10-15', '2023-10-16', '2023-10-22', '2023-10-29', '2023-11-05', '2023-11-06', '2023-11-12', '2023-11-13', '2023-11-19', '2023-11-26', '2023-12-03', '2023-12-08', '2023-12-10', '2023-12-17', '2023-12-24', '2023-12-25', '2023-12-31'
            ],
            '2024' => [
                '2024-01-01', '2024-01-07', '2024-01-08', '2024-01-14', '2024-01-21', '2024-01-28', '2024-02-04', '2024-02-11', '2024-02-18', '2024-02-25', '2024-03-03', '2024-03-10', '2024-03-17', '2024-03-24', '2024-03-25', '2024-03-28', '2024-03-29', '2024-03-31', '2024-04-07', '2024-04-14', '2024-04-21', '2024-04-28', '2024-05-01', '2024-05-05', '2024-05-12', '2024-05-13', '2024-05-19', '2024-05-26', '2024-06-02', '2024-06-03', '2024-06-09', '2024-06-10', '2024-06-16', '2024-06-23', '2024-06-30', '2024-07-01', '2024-07-07', '2024-07-14', '2024-07-21', '2024-07-28', '2024-08-04', '2024-08-07', '2024-08-11', '2024-08-18', '2024-08-19', '2024-08-25', '2024-09-01', '2024-09-08', '2024-09-15', '2024-09-22', '2024-09-29', '2024-10-06', '2024-10-13', '2024-10-14', '2024-10-20', '2024-10-27', '2024-11-03', '2024-11-04', '2024-11-10', '2024-11-11', '2024-11-17', '2024-11-24', '2024-12-01', '2024-12-08', '2024-12-15', '2024-12-22', '2024-12-25', '2024-12-29'
            ],
            '2025' => [
                '2025-01-01', '2025-01-05', '2025-01-06', '2025-01-12', '2025-01-19', '2025-01-26', '2025-02-02', '2025-02-09', '2025-02-16', '2025-02-23', '2025-03-02', '2025-03-09', '2025-03-16', '2025-03-23', '2025-03-24', '2025-03-30', '2025-04-06', '2025-04-13', '2025-04-17', '2025-04-18', '2025-04-20', '2025-04-27', '2025-05-01', '2025-05-04', '2025-05-11', '2025-05-18', '2025-05-25', '2025-06-01', '2025-06-02', '2025-06-08', '2025-06-15', '2025-06-22', '2025-06-23', '2025-06-29', '2025-06-30', '2025-07-06', '2025-07-13', '2025-07-20', '2025-07-27', '2025-08-03', '2025-08-07', '2025-08-10', '2025-08-17', '2025-08-18', '2025-08-24', '2025-08-31', '2025-09-07', '2025-09-14', '2025-09-21', '2025-09-28', '2025-10-05', '2025-10-12', '2025-10-13', '2025-10-19', '2025-10-26', '2025-11-02', '2025-11-03', '2025-11-09', '2025-11-16', '2025-11-17', '2025-11-23', '2025-11-30', '2025-12-07', '2025-12-08', '2025-12-14', '2025-12-21', '2025-12-25', '2025-12-28'
            ],
            '2026' => [
                '2026-01-01', '2026-01-04', '2026-01-11', '2026-01-12', '2026-01-18', '2026-01-25', '2026-02-01', '2026-02-08', '2026-02-15', '2026-02-22', '2026-03-01', '2026-03-08', '2026-03-15', '2026-03-22', '2026-03-23', '2026-03-29', '2026-04-02', '2026-04-03', '2026-04-05', '2026-04-12', '2026-04-19', '2026-04-26', '2026-05-01', '2026-05-03', '2026-05-10', '2026-05-17', '2026-05-18', '2026-05-24', '2026-05-31', '2026-06-07', '2026-06-08', '2026-06-14', '2026-06-15', '2026-06-21', '2026-06-28', '2026-06-29', '2026-07-05', '2026-07-12', '2026-07-19', '2026-07-20', '2026-07-26', '2026-08-02', '2026-08-07', '2026-08-09', '2026-08-16', '2026-08-17', '2026-08-23', '2026-08-30', '2026-09-06', '2026-09-13', '2026-09-20', '2026-09-27', '2026-10-04', '2026-10-11', '2026-10-12', '2026-10-18', '2026-10-25', '2026-11-01', '2026-11-02', '2026-11-08', '2026-11-15', '2026-11-16', '2026-11-22', '2026-11-29', '2026-12-06', '2026-12-08', '2026-12-13', '2026-12-20', '2026-12-25', '2026-12-27'
            ],
            '2027' => [
                '2027-01-01', '2027-01-03', '2027-01-10', '2027-01-11', '2027-01-17', '2027-01-24', '2027-01-31', '2027-02-07', '2027-02-14', '2027-02-21', '2027-02-28', '2027-03-07', '2027-03-14', '2027-03-21', '2027-03-22', '2027-03-25', '2027-03-26', '2027-03-28', '2027-04-04', '2027-04-11', '2027-04-18', '2027-04-25', '2027-05-02', '2027-05-09', '2027-05-10', '2027-05-16', '2027-05-23', '2027-05-30', '2027-05-31', '2027-06-06', '2027-06-07', '2027-06-13', '2027-06-20', '2027-06-27', '2027-07-04', '2027-07-05', '2027-07-11', '2027-07-18', '2027-07-20', '2027-07-25', '2027-08-01', '2027-08-08', '2027-08-15', '2027-08-16', '2027-08-22', '2027-08-29', '2027-09-05', '2027-09-12', '2027-09-19', '2027-09-26', '2027-10-03', '2027-10-10', '2027-10-17', '2027-10-18', '2027-10-24', '2027-10-31', '2027-11-01', '2027-11-07', '2027-11-14', '2027-11-15', '2027-11-21', '2027-11-28', '2027-12-05', '2027-12-08', '2027-12-12', '2027-12-19', '2027-12-26'
            ],
            '2028' => [
                '2028-01-02', '2028-01-09', '2028-01-10', '2028-01-16', '2028-01-23', '2028-01-30', '2028-02-06', '2028-02-13', '2028-02-20', '2028-02-27', '2028-03-05', '2028-03-12', '2028-03-19', '2028-03-20', '2028-03-26', '2028-04-02', '2028-04-09', '2028-04-13', '2028-04-14', '2028-04-16', '2028-04-23', '2028-04-30', '2028-05-01', '2028-05-07', '2028-05-14', '2028-05-21', '2028-05-28', '2028-05-29', '2028-06-04', '2028-06-11', '2028-06-18', '2028-06-19', '2028-06-25', '2028-06-26', '2028-07-02', '2028-07-03', '2028-07-09', '2028-07-16', '2028-07-20', '2028-07-23', '2028-07-30', '2028-08-06', '2028-08-07', '2028-08-13', '2028-08-20', '2028-08-21', '2028-08-27', '2028-09-03', '2028-09-10', '2028-09-17', '2028-09-24', '2028-10-01', '2028-10-08', '2028-10-15', '2028-10-16', '2028-10-22', '2028-10-29', '2028-11-05', '2028-11-06', '2028-11-12', '2028-11-13', '2028-11-19', '2028-11-26', '2028-12-03', '2028-12-08', '2028-12-10', '2028-12-17', '2028-12-24', '2028-12-25', '2028-12-31'
            ],
            '2029' => [
                '2029-01-01', '2029-01-07', '2029-01-08', '2029-01-14', '2029-01-21', '2029-01-28', '2029-02-04', '2029-02-11', '2029-02-18', '2029-02-25', '2029-03-04', '2029-03-11', '2029-03-18', '2029-03-19', '2029-03-25', '2029-03-29', '2029-03-30', '2029-04-01', '2029-04-08', '2029-04-15', '2029-04-22', '2029-04-29', '2029-05-01', '2029-05-06', '2029-05-13', '2029-05-14', '2029-05-20', '2029-05-27', '2029-06-03', '2029-06-04', '2029-06-10', '2029-06-11', '2029-06-17', '2029-06-24', '2029-07-01', '2029-07-02', '2029-07-08', '2029-07-15', '2029-07-20', '2029-07-22', '2029-07-29', '2029-08-05', '2029-08-07', '2029-08-12', '2029-08-19', '2029-08-20', '2029-08-26', '2029-09-02', '2029-09-09', '2029-09-16', '2029-09-23', '2029-09-30', '2029-10-07', '2029-10-14', '2029-10-15', '2029-10-21', '2029-10-28', '2029-11-04', '2029-11-05', '2029-11-11', '2029-11-12', '2029-11-18', '2029-11-25', '2029-12-02', '2029-12-09', '2029-12-16', '2029-12-23', '2029-12-25', '2029-12-30'
            ],
            '2030' => [
                '2030-01-01', '2030-01-06', '2030-01-07', '2030-01-13', '2030-01-20', '2030-01-27', '2030-02-03', '2030-02-10', '2030-02-17', '2030-02-24', '2030-03-03', '2030-03-10', '2030-03-17', '2030-03-24', '2030-03-25', '2030-03-31', '2030-04-07', '2030-04-14', '2030-04-18', '2030-04-19', '2030-04-21', '2030-04-28', '2030-05-01', '2030-05-05', '2030-05-12', '2030-05-19', '2030-05-26', '2030-06-02', '2030-06-03', '2030-06-09', '2030-06-16', '2030-06-23', '2030-06-24', '2030-06-30', '2030-07-01', '2030-07-07', '2030-07-14', '2030-07-21', '2030-07-28', '2030-08-04', '2030-08-07', '2030-08-11', '2030-08-18', '2030-08-19', '2030-08-25', '2030-09-01', '2030-09-08', '2030-09-15', '2030-09-22', '2030-09-29', '2030-10-06', '2030-10-13', '2030-10-14', '2030-10-20', '2030-10-27', '2030-11-03', '2030-11-04', '2030-11-10', '2030-11-11', '2030-11-17', '2030-11-24', '2030-12-01', '2030-12-08', '2030-12-15', '2030-12-22', '2030-12-25', '2030-12-29'
            ],

        ];
        return $holidayColombia[$yearReceived];
    }


}
