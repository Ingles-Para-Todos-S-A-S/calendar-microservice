<?php

namespace App\Services;

use App\Models\ApiGateway;
use App\Models\ApiResponse;
use App\Models\PQR;
use App\Models\User;
use App\Models\UserRoleRoutes;

class UserService {

    public static function createUserFromWeb($request){
        if(UserService::validateRequest($request, 'web')) {
            $user = new User();

            $user->name              = $request->params['name'];
            $user->last_name         = $request->params['last_name'];
            $user->email             = $request->params['email'];
            $user->phone_indicative  = $request->params['phone_indicative'];
            $user->phone_number      = $request->params['phone_number'];
            $user->gender            = 1;
            $user->city              = 1;
            $user->id_card_type      = $request->params['id_card_type'];
            $user->id_card           = $request->params['id_card'];
            $user->password          = md5($request->params['password']);
            $user->role              = 15;

            do {
                $token = User::generateToken();
                $uniqueToken = User::getUserByToken($token);
            } while($uniqueToken!= null);

            $user->token = $token;

            if(UserService::isUniqueUser($request)) {
                $user->save();
                $messageCode = 1;
            } else {
                $messageCode = 4;
            }
        }else{
            $messageCode = 3;
        }

        $user = $messageCode == 1 ? $user : null;
        return ApiResponse::response($messageCode,$user);
    }

    public static function createUserFromIndex($request){
        if(UserService::validateRequest($request, 'index')) {
            $user = new User();

            $user->name              = $request->params['name'];
            $user->last_name         = $request->params['last_name'];
            $user->email             = $request->params['email'];
            $user->phone_indicative  = $request->params['phone_indicative'];
            $user->phone_number      = $request->params['phone_number'];
            $user->gender            = 1;
            $user->city              = $request->params['city'];
            $user->id_card_type      = $request->params['id_card_type'];
            $user->id_card           = $request->params['id_card'];
            $user->password          = md5($request->params['id_card']);
            $user->role              = 15;
            $user->birthdate         = $request->params['birthdate'];

            do {
                $token = User::generateToken();
                $uniqueToken = User::getUserByToken($token);
            } while($uniqueToken!= null);

            $user->token = $token;

            if(UserService::isUniqueUser($request)) {
                $user->save();
                $pqr = new PQR();

                $pqr->user = $user->code;
                $pqr->pqr_type = 1;
                $pqr->comment = $request->params['comment'];

                if(isset($request->params['comment'])) {
                    $pqr->save();
                }

                $messageCode = 1;
            } else {
                $messageCode = 4;
            }
        }else{
            $messageCode = 3;
        }

        $user = $messageCode == 1 ? $user : null;
        return ApiResponse::response($messageCode,$user);
    }

    public static function createUserFromDashboard($request){
        if(UserService::validateRequest($request, 'dashboard')) {
            $user = new User();

            $user->name              = $request->params['name'];
            $user->last_name         = $request->params['last_name'];
            $user->email             = $request->params['email'];
            $user->phone_indicative  = $request->params['phone_indicative'];
            $user->phone_number      = $request->params['phone_number'];
            $user->gender            = 1;
            $user->city              = $request->params['city'];
            $user->id_card_type      = $request->params['id_card_type'];
            $user->id_card           = $request->params['id_card'];
            $user->password          = md5($request->params['id_card']);
            $user->role              = $request->params['role'];
            $user->service_hour      = $request->params['service_hour'];

            do {
                $token = User::generateToken();
                $uniqueToken = User::getUserByToken($token);
            } while($uniqueToken!= null);

            $user->token = $token;

            if(UserService::isUniqueUser($request)) {
                $user->save();
                $messageCode = 1;
            } else {
                $messageCode = 4;
            }
        }else{
            $messageCode = 3;
        }

        $user = $messageCode == 1 ? $user : null;
        return ApiResponse::response($messageCode,$user);
    }

    public static function loginUser($request) {
        if(isset($request->params['user']) && isset($request->params['password'])){
            $user = User::getUserLogin($request->params['user'], md5($request->params['password']));
            $messageCode = $user == null ? 2 : 1;
        }else{
            $messageCode = 3;
            $user = null;
        }

        return ApiResponse::response($messageCode,$user);
    }

    public static function getAvailableTeachers($request) {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if($userRole == 10 || $userRole == 11) {
            $teachers = User::getTeachers();
            for ($i=0; $i < sizeof($teachers); $i++) {
                $teachers[$i] = User::serveToUser($teachers[$i]);
            }
            $messageCode = is_null($teachers) ? 2 : 1;
            return ApiResponse::response($messageCode, $teachers);
        } else {
            return ApiResponse::response(5, null);
        }
    }

    public static function getStudents($request) {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if($userRole == 10 || $userRole == 11) {
            $students = User::getStudents();
            for ($i=0; $i < sizeof($students); $i++) {
                $students[$i] = User::serveToUser($students[$i]);
            }
            $messageCode = is_null($students) ? 2 : 1;
            return ApiResponse::response($messageCode, $students);
        } else {
            return ApiResponse::response(5, null);
        }
    }

    public static function getStudent($request) {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if($userRole == 10 || $userRole == 11 || $userRole == 31 || $userRole == 30) {
            $users = User::getStudent($request->param);
            $students = array();

            for ($i=0; $i < sizeof($users); $i++) {
                if ($users[$i]->role == 13) {
                    array_push($students, User::serveToUser($users[$i]));
                }
            }
            $messageCode = is_null($students) ? 2 : 1;
            return ApiResponse::response($messageCode, $students);
        } else {
            return ApiResponse::response(5, null);
        }
    }

    public static function getPossibleStudents ($request) {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if ($userRole >= 0) {
            $possibleStudents = User::getPossibleStudents();
            $messageCode = is_null($possibleStudents) ? 2 : 1;
            $possibleStudents = UserService::paginateUsers($possibleStudents, $request->record, $request->page);
            return ApiResponse::response($messageCode, $possibleStudents);
        } else {
            return ApiResponse::response(5, null);
        }
    }

    public static function getPlacementTestStudents($request) {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if ($userRole >= 0) {
            $url = "https://test.inglesparatodos.edu.co/api/get_placement";
            $params = [
                'token' => 'token_for_access_ipt_search_old_PARAMS',
            ];

            $placement = json_decode(ApiGateway::postRequest($url, $params));

            $messageCode = $placement->response_code != 'B' ? 2 : 1;
            $placement = UserService::paginateUsers($placement->data, $request->record, $request->page);
            return ApiResponse::response($messageCode, $placement);
        } else {
            return ApiResponse::response(5, null);
        }
    }

    public static function getRoutesForRole($request) {
        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

        if ($userRole >= 0) {
            $data = UserRoleRoutes::getRoutesForRole($userRole);
            return ApiResponse::response(1, $data);
        } else {
            return ApiResponse::response(5, null);
        }

    }

    private static function paginateUsers($placement, $record, $page) {
        if(!is_null($placement)) {
            $pages = ceil(sizeof($placement)/$record);
            $responseUsers = array();

            $j = ($page-1)*$record;
            $n = $record;

            while($j<sizeof($placement) && $n>0) {
                array_push($responseUsers, $placement[$j]);
                $n--;
                $j++;
            }

            $response = array(
                'number_of_pages' => $pages,
                'current_page' => $page,
                'total_records' => sizeof($placement),
                'records_per_page' => $record,
                'users' => $responseUsers
            );

            return $response;
        } else {
            return null;
        }
    }

    private static function isUniqueUser($request) {
        return User::isUniqueUser($request->params['id_card_type'],
                                    $request->params['id_card'],
                                    $request->params['email'],
                                    $request->params['phone_indicative'],
                                    $request->params['phone_number'],
                                    NULL);
    }

    private static function validateRequest($request, $type){
        $validator = isset($request->params['name']) && is_string($request->params['name']) && strlen($request->params['name'])>3
            && isset($request->params['last_name']) && is_string($request->params['last_name']) && strlen($request->params['last_name'])>3
            && isset($request->params['email']) && is_string($request->params['email']) && strlen($request->params['email'])>10
            && isset($request->params['phone_number']) && isset($request->params['phone_indicative']) && is_numeric($request->params['phone_indicative'])
            && isset($request->params['id_card_type']) && is_numeric($request->params['id_card_type'])
            && isset($request->params['id_card']);

        switch ($type) {
            case 'index':
                $validator = $validator && isset($request->params['birthdate']) && isset($request->params['city']) && is_numeric($request->params['city']);
                break;
            case 'web':
                $validator = $validator && isset($request->params['password']) && strlen($request->params['password'])>=4;
                break;
            case 'dashboard':
                $validator = $validator && isset($request->params['role']) && is_numeric($request->params['role'])
                    && isset($request->params['service_hour']) && is_numeric($request->params['service_hour'])
                    && isset($request->params['password']) && strlen($request->params['password'])>=4
                    && isset($request->params['city']) && is_numeric($request->params['city']);
                break;
            default:
                break;
        }

        return $validator;
    }

    public static function getAllUserCommer($request) {

        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

         if ($userRole == 40 || $userRole == 41 || $userRole == 42) {
            $adviser = User::getAllAdviser();
            for ($i=0; $i < sizeof($adviser); $i++) {
                $adviser[$i] = User::getToUser($adviser[$i]);
            }
            $messageCode = is_null($adviser) ? 2 : 1;
            return ApiResponse::response($messageCode, $adviser);
        } else {
            return ApiResponse::response(5, null);
        }
    }

    public static function getByRoleUser($code) {
        if(isset($code) && is_numeric($code)) {
            $data = User::getByRoleUser($code);
            for ($i=0; $i < sizeof($data); $i++) {
                $data[$i] = User::getToUser($data[$i]);
            }
            $messageCode = $data == null ? 2 : 1;
        } else {
            $messageCode = 3;
            $data = null;
        }

        return ApiResponse::response($messageCode, $data);
    }

    public static function getAllUserAdministrative($request) {

        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

         if ($userRole == 30 || $userRole == 31 || $userRole == 32) {
            $admistrative = User::getAllAdministrative();
            for ($i=0; $i < sizeof($admistrative); $i++) {
                $admistrative[$i] = User::getToUser($admistrative[$i]);
            }
            $messageCode = is_null($admistrative) ? 2 : 1;
            return ApiResponse::response($messageCode, $admistrative);
        } else {
            return ApiResponse::response(5, null);
        }
    }

    public static function getStudentByCode($request) {

        $userRole = isset($request->token) ? User::getRoleByToken($request->token) : -1;

         if ($userRole == 30 || $userRole == 31 ) {
            $students = User::getByCodeStudent($request->code);
            if ( !($students === null) ) {
                $students[0] = User::getToUser($students[0]);
                $messageCode = is_null($students) ? 2 : 1;
                return ApiResponse::response($messageCode, $students);
            }else {
                return ApiResponse::response(2, null);
            }

        } else {
            return ApiResponse::response(5, null);
        }
    }


}
