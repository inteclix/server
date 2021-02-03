<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

use App\Client;
use App\Imports\ClientsImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ClientController   extends Controller
{
    function get($id, Request $request)
    {
        try {
            $client = Client::with('client')->findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'message' => 'Success get',
            'data' => $client
        ]);
    }

    function search(Request $request)
    {
        //$offset = $request->offset | 0;
        //$limit = $request->limit | 20;
        $data = $request->get('data');
        $clients = Client::where('designation', 'like', "%{$data}%")
            ->orWhere('code', 'like', "%{$data}%")
            ->take(8)
            ->get();

        //dump($search);
        return new JsonResponse([
            'message' => 'Success get all',
            'data' => $clients !== NULL ? $clients : []
        ], Response::HTTP_OK);
    }

    function getAll(Request $request)
    {
        $sort = $request->get("sort") === "ascend" ? "asc" : "desc";
        $sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
        $current = $request->get("current") ? $request->get("current") : 1;
        $pageSize = $request->get("pageSize") ? $request->get("pageSize") : 20;
        return $this->http_ok(
            DB::table("clients")
                ->leftJoin("clients AS clients_h", "clients.client_id", "=", "clients_h.id")
                ->select([
                    "clients.id as id",
                    "clients.code as code",
                    "clients.designation as designation",
                    "clients.localite as localite",
                    "clients.tel as tel",
                    "clients_h.designation as client_h_designation"
                ])
              //  ->where('clients.code', 'like', "%{$request->get("code")}%")
                ->where('clients.designation', 'like', "%{$request->get("designation")}%")
                ->orderBy($sortBy, $sort)
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
        if ($has_role) {
            $this->validate($request, [
                'designation' => 'required',
            ]);
            $c = new Client;
            $c->code = $request->code;
            $c->designation = $request->designation;
            $c->localite = $request->localite;
            $c->tel = $request->tel;
            $c->client_id = $request->client_id;
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
            Client::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        $has_role = false;
        if ($request->auth->username === "admin") {
            $has_role = true;
        }
        if ($has_role) {
            Client::destroy($id);
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
            $c = Client::findOrFail($id);
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
                'designation' => 'required',
            ]);
            $c->code = $request->code;
            $c->designation = $request->designation;
            $c->localite = $request->localite;
            $c->tel = $request->tel;
            $c->client_id = $request->client_id;
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

    public function import(Request $request)
    {
        //dump($request);
        if ($request->hasFile('clients')) {
            Excel::import(new ClientsImport, $request->clients);
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
