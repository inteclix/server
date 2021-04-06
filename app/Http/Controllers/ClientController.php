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
use App\Exports\CollectionsExport;

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

    public function mise_disposition_vl(Request $request)
    {
        $this->checkValidation($request, [
            "date1" => "required",
            "date2" => "required",
            "format" => "required"
        ]);

        $sort = $request->get("sort") === "ascend" ? "asc" : "desc";
        $sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
        $current = $request->get("current") ? $request->get("current") : 1;
        $pageSize = $request->get("pageSize") ? $request->get("pageSize") : 20;
        $decharges =  DB::table("decharges")
            ->join('checklists', function ($join) {
                $join->on('decharges.id', '=', 'checklists.decharge_id')
                    ->on('checklists.id', '=', DB::raw("(select max(id) from checklists WHERE checklists.decharge_id = decharges.id)"));
            })
            ->join('clients', 'decharges.client_id', '=', 'clients.id')
            ->leftJoin('clients as clients_mother', 'clients.client_id', '=', 'clients_mother.id')
            ->leftJoin('restititions', 'decharges.id', '=', "restititions.decharge_id")
            ->join("cars", "checklists.car_id", "=", "cars.id")
            ->join("car_user", "cars.id", "car_user.car_id")
            ->join("drivers", "checklists.driver_id", "=", "drivers.id")
            ->join("users as creates", "checklists.createdby_id", "=", "creates.id")
            ->leftJoin("users as acceptes", "decharges.acceptedby_id", "=", "acceptes.id")
            ->join("users as owners", "car_user.user_id", "=", "owners.id");
        if ($request->format == "json") {
            $decharges = $decharges->select([
                'cars.matricule as cars_matricule',
                'cars.code_gps as cars_code_gps',
                'decharges.id as id',
                'decharges.date_decharge as decharges_date_decharge',
                'decharges.date_fin_prestation as decharges_date_fin_prestation',
                'restititions.id as restititions_id',
                'restititions.date_restitition as restititions_date_restitition',
                'clients.designation as clients_designation',
                'clients_mother.designation as clients_mother_designation',
                'creates.username as createdby_username',
                'acceptes.username as acceptedby_username',
                'owners.id as ownerId'
            ]);
        }
        if ($request->format == "excel") {
            $decharges = $decharges->select([
                'decharges.id',
                'cars.matricule as cars_matricule',
                'cars.code_gps as cars_code_gps',
                'decharges.date_decharge as decharges_date_decharge',
                'decharges.date_fin_prestation as decharges_date_fin_prestation',
                'restititions.date_restitition as restititions_date_restitition',
                'clients.designation as clients_designation',
                'clients_mother.designation as clients_mother_designation',
                'creates.username as createdby_username',
                'acceptes.username as acceptedby_username',
            ]);
        }

        $decharges = $decharges->where("decharges.date_decharge", "<=", "{$request->date2}")
            ->where(function ($query) use ($request) {
                $query->where("restititions.date_restitition", ">=", "{$request->date1}")
                    ->orWhere('restititions.date_restitition', "=", null);
            })
            ->where("owners.id", "=", $request->auth->id)
            ->where('cars.matricule', 'like', "%{$request->get("cars_matricule")}%")
            ->where('cars.code_gps', 'like', "%{$request->get("cars_code_gps")}%")
            ->where('clients.designation', 'like', "%{$request->get("clients_designation")}%")
            ->orderBy($sortBy, $sort);

        if ($request->format == "json") {
            return $decharges->paginate(
                $pageSize, // per page (may be get it from request)
                ['*'], // columns to select from table (default *, means all fields)
                'page', // page name that holds the page number in the query string
                $current // current page, default 1
            );
        }
        if ($request->format == "excel") {
            $decharges = $decharges->get();
            $decharges->prepend([
                "id" => "ID",
                "cars_matricule" => "MATRICULE",
                "cars_code_gps" => "CODE GPS",
                "decharges_date_decharge" => "DATE DECHARGE",
                "decharges_date_fin_prestation" => "DATE FIN PRESTATION",
                "restititions_date_restitition" => "DATE RESTITUTION",
                "clients_designation" => "CLIENT",
                "clients_mother_designation" => "CLIENT HEIRACHIQIE",
                "createdby_username" => "CREE PAR",
                "acceptedby_username" => "VALIDER PAR",
            ]);
            return Excel::download(new CollectionsExport($decharges), 'etat_vehicules.xlsx');
        }
    }
}
