<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;

class Controller extends BaseController
{
    // return a boolean if user has role
    function hasRole($request, $roleName)
    {
        //dd($request->auth->username === "admin");
        if ($request->auth->username === "admin") {
            return true;
        } else {
            $has_role = false;
            foreach ($request->auth->roles as $role) {
                if ($role->name == $roleName) {
                    $has_role = true;
                }
            }
            return $has_role;
        }
    }

    // return a boolean if user->username = "admin"
    function isAdmin($request)
    {
        return $request->auth->username === "admin";
    }

    // return status 200 with data and message 
    function success($data, $message = "")
    {
        return new JsonResponse([
            'message' => $message,
            'data' => $data
        ], Response::HTTP_OK);
    }

    // return status 404 with message
    function error($message = "")
    {
        return new JsonResponse([
            'message' => $message,
        ], Response::HTTP_NOT_FOUND);
    }

    //
    function http_not_found($message = "identifiant introuvable!")
    {
        return new JsonResponse([
            'message' => $message,
        ], Response::HTTP_NOT_FOUND);
    }

    function http_bad($message = "Erreur SQL!")
    {
        return new JsonResponse([
            'message' => $message,
        ], Response::HTTP_BAD_REQUEST);
    }

    function http_ok($data = [], $message = "OK")
    {
        return new JsonResponse([
            'message' => $message,
            'data' => $data
        ], Response::HTTP_OK);
    }
    //
    function http_unauthorized($message = "Vous ñ'avez pas l'autorisation")
    {
        return new JsonResponse([
            'message' => $message
        ], Response::HTTP_UNAUTHORIZED);
    }

    // check form validation
    function checkValidation($request, $validation)
    {
        try {
            $this->validate($request, $validation);
        } catch (QueryException $e) {
            return $this->error("SQL exception");
        }
    }
}
