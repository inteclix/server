<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

use App\Role;
use Illuminate\Support\Facades\Storage;

class RoleController extends Controller
{
    function get($id, Request $request)
    {
        try {
            $Role = Role::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }


        return new JsonResponse([
            'message' => 'Success get',
            'data' => $Role
        ]);
    }

    function getAll(Request $request)
    {
        if ($request->auth->poste === 'admin') {
            $roles = Role::orderBy("id", "desc")->get()->all();
            return new JsonResponse([
                'message' => 'Success get all',
                'data' => $roles !== NULL ? $roles : []
            ], Response::HTTP_OK);
        } else {
            return new JsonResponse([
                'message' => 'UNAUTHORIZED',
                'data' => []
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    function create(Request $request)
    {
        $this->validate($request, [
            "name" => "required"
        ]);
        $role = new Role;
        $role->name = $request->name;
        try {
            $role->save();
        } catch (QueryException $e) {
            return $this->error("Error!");
        }
        return $this->success($role, "Bien ajouter");
    }

    function delete($id, Request $request)
    {
        try {
            Role::findOrFail($id);
        } catch (Exception $e) {
            return $this->error("Id don't exist");
        }
        try {
            Role::destroy($id);
        } catch (QueryException $e) {
            return $this->error("Error !");
        }
        return $this->success([], "Bien Supprimer");
    }

    

    public function uploadImg1($id, Request $request)
    {
        try {
            $Role = Role::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        if ($request->hasFile('fileImg1')) {
            $imgSaved = Storage::put("public", $request->file('fileImg1'));
            if ($imgSaved) {
                $Role->Role_img1 = $imgSaved;
                //return $this->responseRequestSuccess($Role);
                try {
                    $Role->save();
                } catch (QueryException $e) {
                    return $this->http_bad();
                }
                return new JsonResponse([
                    'message' => 'Success update Role',
                    'data' => $Role
                ], Response::HTTP_CREATED);
            } else {
                return new JsonResponse([
                    'message' => 'Cannot upload file'
                ], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return new JsonResponse([
                'message' => 'File not found'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function uploadImg2($id, Request $request)
    {
        try {
            $Role = Role::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        if ($request->hasFile('fileImg2')) {
            $imgSaved = Storage::put("public", $request->file('fileImg2'));
            if ($imgSaved) {
                $Role->Role_img2 = $imgSaved;
                //return $this->responseRequestSuccess($Role);
                try {
                    $Role->save();
                } catch (QueryException $e) {
                    return $this->http_bad();
                }
                return new JsonResponse([
                    'message' => 'Success update Role',
                    'data' => $Role
                ], Response::HTTP_CREATED);
            } else {
                return new JsonResponse([
                    'message' => 'Cannot upload file'
                ], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return new JsonResponse([
                'message' => 'File not found'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function uploadImg3($id, Request $request)
    {
        try {
            $Role = Role::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        if ($request->hasFile('fileImg3')) {
            $imgSaved = Storage::put("public", $request->file('fileImg3'));
            if ($imgSaved) {
                $Role->Role_img3 = $imgSaved;
                //return $this->responseRequestSuccess($Role);
                try {
                    $Role->save();
                } catch (QueryException $e) {
                    return $this->http_bad();
                }
                return new JsonResponse([
                    'message' => 'Success update Role',
                    'data' => $Role
                ], Response::HTTP_CREATED);
            } else {
                return new JsonResponse([
                    'message' => 'Cannot upload file'
                ], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return new JsonResponse([
                'message' => 'File not found'
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
