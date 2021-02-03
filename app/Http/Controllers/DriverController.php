<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

use App\Driver;
use App\Exports\DriversExport;
use App\Imports\DriversImport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class DriverController extends Controller
{
    function get($id, Request $request)
    {
        try {
            $c = Driver::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'message' => 'Success get',
            'data' => $c
        ]);
    }

    function search(Request $request)
    {
        //$offset = $request->offset | 0;
        //$limit = $request->limit | 20;
        $data = $request->get('data');
        $drivers = Driver::where('firstname', 'like', "%{$data}%")
            ->orWhere('lastname', 'like', "%{$data}%")
            ->take(8)
            ->get();

        //dump($search);
        return new JsonResponse([
            'message' => 'Success get all',
            'data' => $drivers !== NULL ? $drivers : []
        ], Response::HTTP_OK);
    }

    function getAll(Request $request)
    {
        $sort = $request->get("sort") === "ascend" ? "asc" : "desc";
        $sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
        $current = $request->get("current") ? $request->get("current") : 1;
        $pageSize = $request->get("pageSize") ? $request->get("pageSize") : 20;
        return $this->http_ok(
            Driver::orderBy($sortBy, $sort)
                ->where('firstname', 'like', "%{$request->get("firstname")}%")
                ->where('lastname', 'like', "%{$request->get("lastname")}%")
              //  ->where('code_paie', 'like', "%{$request->get("code_paie")}%")
                ->paginate(
                    $pageSize, // per page (may be get it from request)
                    ['*'], // columns to select from table (default *, means all fields)
                    'page', // page name that holds the page number in the query string
                    $current // current page, default 1
                )
        );
    }

    function create(Request $request)
    {
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_DRIVERS") {
                $has_role = true;
            }
        }
        if ($has_role) {
            $this->validate($request, [
                'firstname' => 'required',
                'lastname' => 'required',
            ]);
            $c = new Driver;
            $c->firstname = $request->firstname;
            $c->lastname = $request->lastname;
            $c->tel = $request->tel;
            $c->code_paie = $request->code_paie;
            $c->type = $request->type;
            $c->createdby_id = $request->auth->id;
            try {
                $c->save();
            } catch (QueryException $e) {
                return $this->http_bad();
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
            Driver::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "DELETE_DRIVERS") {
                $has_role = true;
            }
        }
        if ($has_role) {

            Driver::destroy($id);
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
            $c = Driver::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_DRIVERS") {
                $has_role = true;
            }
        }
        if ($has_role) {
            $this->validate($request, [
                'firstname' => 'required',
                'lastname' => 'required',
            ]);
            $c->firstname = $request->firstname;
            $c->lastname = $request->lastname;
            $c->tel = $request->tel;
            $c->code_paie = $request->code_paie;
            $c->type = $request->type;
            $c->createdby_id = $request->auth->id;
            try {
                $c->save();
            } catch (QueryException $e) {
                return $this->http_bad();
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
    public function export(Request $request)
    {
        return Excel::download(new DriversExport, 'drivers.xlsx');
    }
    public function import(Request $request)
    {
        //dump($request);
        if ($request->hasFile('drivers')) {
            Excel::import(new DriversImport, $request->drivers);
            return new JsonResponse([
                'message' => 'Success',
                'data' => []
            ], Response::HTTP_CREATED);
        }
        return new JsonResponse([
            'message' => 'Fichier non trouvé'
        ], Response::HTTP_BAD_REQUEST);
    }
}
