<?php

namespace App\Models;

class HttpStatusCode {
   
    public static function SWITCHING_PROTOCOLS(){
        return 101;
    } 
    public static function OK(){
        return 200;
    } 
    public static function CREATED(){
        return 201;
    } 
    public static function ACCEPTED(){
        return 202;
    } 
    public static function NONAUTHORITATIVE_INFORMATION(){
        return 203;
    } 
    public static function NO_CONTENT(){
        return 204;
    } 
    public static function RESET_CONTENT(){
        return 205;
    } 
    public static function PARTIAL_CONTENT(){
        return 206;
    } 
    public static function MULTIPLE_CHOICES(){
        return 300;
    } 
    public static function MOVED_PERMANENTLY(){
        return 301;
    } 
    public static function MOVED_TEMPORARILY(){
        return 302;
    } 
    public static function SEE_OTHER(){
        return 303;
    } 
    public static function NOT_MODIFIED(){
        return 304;
    } 
    public static function USE_PROXY(){
        return 305;
    } 
    public static function BAD_REQUEST(){
        return 400;
    } 
    public static function UNAUTHORIZED(){
        return 401;
    } 
    public static function PAYMENT_REQUIRED(){
        return 402;
    } 
    public static function FORBIDDEN(){
        return 403;
    } 
    public static function NOT_FOUND(){
        return 404;
    } 
    public static function METHOD_NOT_ALLOWED(){
        return 405;
    } 
    public static function NOT_ACCEPTABLE(){
        return 406;
    } 
    public static function PROXY_AUTHENTICATION_REQUIRED(){
        return 407;
    } 
    public static function REQUEST_TIMEOUT(){
        return 408;
    } 
    public static function CONFLICT(){
        return 408;
    } 
    public static function GONE(){
        return 410;
    } 
    public static function LENGTH_REQUIRED(){
        return 411;
    } 
    public static function PRECONDITION_FAILED(){
        return 412;
    } 
    public static function REQUEST_ENTITY_TOO_LARGE(){
        return 413;
    } 
    public static function REQUESTURI_TOO_LARGE(){
        return 414;
    } 
    public static function UNSUPPORTED_MEDIA_TYPE(){
        return 415;
    } 
    public static function REQUESTED_RANGE_NOT_SATISFIABLE(){
        return 416;
    } 
    public static function EXPECTATION_FAILED(){
        return 417;
    } 
    public static function IM_A_TEAPOT(){
        return 418;
    } 
    public static function INTERNAL_SERVER_ERROR(){
        return 500;
    } 
    public static function NOT_IMPLEMENTED(){
        return 501;
    } 
    public static function BAD_GATEWAY(){
        return 502;
    } 
    public static function SERVICE_UNAVAILABLE(){
        return 503;
    } 
    public static function GATEWAY_TIMEOUT(){
        return 504;
    } 
    public static function HTTP_VERSION_NOT_SUPPORTED(){
        return 505;
    } 
}