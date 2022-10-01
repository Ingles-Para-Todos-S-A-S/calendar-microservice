<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;


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

Route::get('get/event/calendar/idcalendar/{idcalendar}',        [CalendarController::class, 'getEventCalendarByIdCalendar']);
Route::get('get/event/calendar/all',                            [CalendarController::class, 'getEventCalendarAllEvent']);
Route::post('post/add/event/calendar/by/daytime',               [CalendarController::class, 'addEventCalendarDyDayTime']);
Route::get('get/add/event/calendar/all/day',                  [CalendarController::class, 'addEventCalendarAllDay']);
Route::put('put/update/event/calendar/by/idevent/{idevent}',    [CalendarController::class, 'updateEventCalendarByIdEvent']);
Route::get('get/delete/event/calendar/by/idevent/{idevent}',    [CalendarController::class, 'deleteEventCalendarByIdEvent']);




