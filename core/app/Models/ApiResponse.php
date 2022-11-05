<?php

namespace App\Models;

use App\Models\ApiMessage;
use App\Models\HttpStatusCode;
use Illuminate\Database\Eloquent\Model;

class ApiResponse extends Model{

    protected $table = 'api_response';
    protected $primaryKey = 'code';
    
    protected $hidden = ['code','updated_at','created_at'];

    public static function response($messageCode, $data){
        $response = new ApiResponse;
        $response->ip_applicant = $_SERVER['REMOTE_ADDR'];

        switch ($messageCode) {
            case 1:
                $response->status = HttpStatusCode::OK();
                break;
            case 2:
                $response->status = HttpStatusCode::NO_CONTENT();
                $data = null;
                break;
            default:
                $response->status = HttpStatusCode::BAD_REQUEST();
                break;
        }
        
        $response->message = $messageCode;
        $response->data = $data;

        return ApiResponse::serveToUser($response);
    }
    
    public static function serveToUser($response){
        $response->message = ApiMessage::find($response->message);
        return $response;
    }

    public static function getAll(){
        $responses = ApiResponse::all();

        for($i=0; $i<sizeof($responses); $i++){
            $responses[$i] = ApiResponse::serveToUser($responses[$i]);
        }

        return $responses;
    }

    public static function findById($code){
        $response = ApiResponse::find($code);
        return ApiResponse::serveToUser($response);
    }

}
