<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

use App\Structure;
use Illuminate\Support\Facades\Storage;

class StructureController  extends Controller
{
    function get($id, Request $request)
    {
        try {
            $structure = Structure::with('client')->findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'message' => 'Success get',
            'data' => $structure
        ]);
    }

    function search(Request $request)
    {
        //$offset = $request->offset | 0;
        //$limit = $request->limit | 20;
        $data = $request->get('data');
        $structures = Structure::where('designation', 'like', "%{$data}%")
            ->orWhere('code', 'like', "%{$data}%")
            ->take(8)
            ->get();

        //dump($search);
        return new JsonResponse([
            'message' => 'Success get all',
            'data' => $structures !== NULL ? $structures : []
        ], Response::HTTP_OK);
    }

    function getAll(Request $request)
    {
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_CLIENTS") {
                $has_role = true;
            }
        }
        if ($has_role) {
            $all = Structure::with('client')->get();
            return new JsonResponse([
                'message' => 'Success get all',
                'data' => $all !== NULL ? $all : []
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
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_CLIENTS") {
                $has_role = true;
            }
        }
        if ($has_role) {
            $this->validate($request, [
                'code' => 'required',
                'designation' => 'required',
                'client_id' => 'required',
            ]);
            $c = new Structure;
            $c->code = $request->code;
            $c->designation = $request->designation;
            $c->client_id = $request->client_id;
            $c->createdby_id = $request->auth->id;
            try {
                $c->save();
            } catch (QueryException $e) {
                return new JsonResponse([
                    'message' => $e
                ], Response::HTTP_BAD_REQUEST);
            }
            return new JsonResponse([
                'message' => 'Success',
                'data' => $c
            ], Response::HTTP_CREATED);
        }
        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }

    function delete($id, Request $request)
    {
        try {
            Structure::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "DELETE_CLIENTS") {
                $has_role = true;
            }
        }
        if ($has_role) {

            Structure::destroy($id);
            return new JsonResponse([
                'message' => 'Deleted'
            ]);
        }

        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }

    function update($id, Request $request)
    {
        try {
            $c = Structure::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_CLIENTS") {
                $has_role = true;
            }
        }
        if ($has_role) {
            $this->validate($request, [
                'code' => 'required',
                'designation' => 'required',
                'client_id' => 'required',
            ]);
            $c->code = $request->code;
            $c->designation = $request->designation;
            $c->client_id = $request->client_id;
            $c->createdby_id = $request->auth->id;
            try {
                $c->save();
            } catch (QueryException $e) {
                return new JsonResponse([
                    'message' => $e
                ], Response::HTTP_BAD_REQUEST);
            }
            return new JsonResponse([
                'message' => 'Success',
                'data' => $c
            ], Response::HTTP_CREATED);
        }
        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }
}
