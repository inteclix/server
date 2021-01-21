<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class Controller extends BaseController
{
    /*
    private $modelName = "";
    function __construct($modelName )
    {
        $this->modelName = $modelName;
    }
*/
    function getAll2()
    {
    }

    function isAdmin($request)
    {
        return $request->auth->username === "admin";
    }
    function success($data, $message = "")
    {
        return new JsonResponse([
            'message' => $message,
            'data' => $data
        ], Response::HTTP_OK);
    }
    function error($message = "")
    {
        return new JsonResponse([
            'message' => $message,
        ], Response::HTTP_NOT_FOUND);
    }
}
