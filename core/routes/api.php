<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClassRoomController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('get/event/calendar/idcalendar/all/{idcalendar}',          [CalendarController::class, 'getEventCalendarByIdCalendar']);
Route::post('post/event/calendar/idcalendar/by/datetime',             [CalendarController::class, 'getEventCalendarByDate']);
Route::post('post/event/create/query',                                [CalendarController::class, 'getCreateQueryEvent']);
Route::post('post/add/event/calendar/by/daytime',                     [CalendarController::class, 'addEventCalendarByDayTime']);
Route::get('get/add/event/calendar/all/day',                          [CalendarController::class, 'addEventCalendarAllDay']);
Route::put('put/update/event/calendar/by/idevent/{idevent}',          [CalendarController::class, 'updateEventCalendarByIdEvent']);
Route::get('get/delete/event/calendar/by/idevent/{idevent}',          [CalendarController::class, 'deleteEventCalendarByIdEvent']);

///// Classrrom
Route::post('post/classroom/by/idcalendar',                           [ClassRoomController::class, 'getClassRoomByIdCalMod']);
Route::get('get/all/classroom',                                       [ClassRoomController::class, 'getAllClassRoom']);





