<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use App\Car;
use App\Exports\CarsExport;
use App\Imports\CarsImport;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;
use App\Group;

class CarController extends Controller
{
    function get($id, Request $request)
    {
        try {
            $car = Car::find($id);
        } catch (Exception $e) {
            return $this->http_not_found();
        }
        if ($this->hasRole($request, "LISTE_VEHICULES")) {
            return $this->http_ok($car);
        }
        return $this->http_unauthorized();
    }

    function getDecharges(Request $request, $car_id)
    {
        $sort = $request->get("sort") === "ascend" ? "asc" : "desc";
        $sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
        $current = $request->get("current") ? $request->get("current") : 1;
        $pageSize = $request->get("pageSize") ? $request->get("pageSize") : 20;
        return DB::table("decharges")
            ->join("checklists", "decharges.id", "=", "checklists.decharge_id")
            ->join('clients', 'decharges.client_id', '=', 'clients.id')
            ->leftJoin('restititions', 'decharges.id', '=', "restititions.decharge_id")->join("cars", "checklists.car_id", "=", "cars.id")
            ->join("users", "checklists.createdby_id", "=", "users.id")
            ->join("drivers", "checklists.driver_id", "=", "drivers.id")
            ->select([
                'restititions.id as restititions.id',
                'decharges.id as id',
                'matricule',
                DB::raw("CONCAT(drivers.firstname, ' ',drivers.lastname) as drivers_fullname"),
                'code_gps',
                'designation as clients.designation',
                'decharges.date_decharge as date_decharge',
                'date_fin_prestation',
                'restititions.date_restitition as restititions.date_restitition',
                'username'
            ])
            ->orderBy($sortBy, $sort)
            ->where('cars.id', '=', $car_id)
            //       ->where('restititions.id', '<>', null)
            ->paginate(
                $pageSize, // per page (may be get it from request)
                ['*'], // columns to select from table (default *, means all fields)
                'page', // page name that holds the page number in the query string
                $current // current page, default 1
            );
    }

    function getClients($id, Request $request)
    {
        try {
            $car = Car::find($id);
        } catch (Exception $e) {
            return $this->http_bad();
        }
        $clients = $car->clients()
            ->withPivot(["id", "createdby_id", "date_affectation", "date_end_prestation", "date_restitition"])
            ->with("client")
            ->orderBy("id", "desc")
            ->get();

        $data = new stdClass();;
        $data->car = $car;
        $data->clients = $clients;
        return new JsonResponse([
            'message' => 'Success get all',
            'data' => $data
        ], Response::HTTP_OK);
    }

    function getDrivers($id, Request $request)
    {
        try {
            $car = Car::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        $drivers = $car->drivers()
            ->withPivot(["id", "createdby_id", "date_affectation_driver", "date_restitition_driver"])
            ->orderBy("id", "desc")
            ->get();

        $data = new stdClass();;
        $data->car = $car;
        $data->drivers = $drivers;
        return new JsonResponse([
            'message' => 'Success get all',
            'data' => $data
        ], Response::HTTP_OK);
    }

    function search(Request $request)
    {
        $cars = DB::table("cars")
            ->join("car_user", "cars.id", "car_user.car_id")
            ->join("users", "users.id", "car_user.user_id")
            ->select([
                "cars.id as id",
                "matricule",
                "marque"
            ])
            ->where("users.id", "=", $request->auth->id)
            ->where("matricule", "like", "%{$request->data}%")
            ->take(8)
            ->get()->all();

        return $this->http_ok($cars);
    }

    function getAllCars(Request $request)
    {
        $cars = DB::table("cars")
            ->select([
                "id",
                "matricule",
                "code_gps"
            ])
            ->get()->all();
        return $this->success($cars);
    }

    function getAllUserCars(Request $request)
    {
        if ($this->hasRole($request, "LISTE_VEHICULES")) {
            $sort = $request->get("sort") === "ascend" ? "asc" : "desc";
            $sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
            $current = $request->get("current") ? $request->get("current") : 1;
            $pageSize = $request->get("pageSize") ? $request->get("pageSize") : 20;
            $data = DB::table("cars")
                ->leftJoin('car_states', function ($join) {
                    $join->on('cars.id', '=', 'car_states.car_id')
                        ->on('car_states.id', '=', DB::raw("(select max(id) from car_states WHERE car_states.car_id = cars.id)"));
                })
                ->leftJoin('checklists', function ($join) {
                    $join->on('cars.id', '=', 'checklists.car_id')
                        ->on('checklists.id', '=', DB::raw("(select max(id) from checklists WHERE checklists.car_id = cars.id)"));
                })
                ->leftJoin("restititions", "checklists.id", "=", "restititions.checklist_id")
                ->leftJoin("decharges", "decharges.id", "=", "checklists.decharge_id")
                ->leftJoin("drivers", "drivers.id", "=", "checklists.driver_id")
                ->leftJoin("clients", "clients.id", "=", "decharges.client_id")
                ->leftJoin("car_user", "car_user.car_id", "=", "cars.id")
                ->leftJoin("car_group", "car_group.car_id", "=", "cars.id")
                ->leftJoin("groups", "car_group.group_id", "=", "groups.id")
                ->select([
                    "groups.name as groupName",
                    "car_user.user_id",
                    "clients.designation as client",
                    "clients.id as clientId",
                    "checklists.date_checklist",
                    "cars.id as id",
                    "decharges.id as decharges.id",
                    "cars.matricule",
                    "cars.genre",
                    "cars.marque",
                    "cars.code_gps",
                    "car_states.name as state",
                    "car_states.state_date",
                    DB::raw("CONCAT(drivers.firstname, ' ',drivers.lastname) as drivers_fullname"),
                    "drivers.id as driversId"
                ])
                ->where('matricule', 'like', "%{$request->get("matricule")}%")
                ->where('car_user.user_id', '=', $request->auth->id)
                ->orderBy($sortBy, $sort)
                ->paginate(
                    $pageSize, // per page (may be get it from request)
                    ['*'], // columns to select from table (default *, means all fields)
                    'page', // page name that holds the page number in the query string
                    $current // current page, default 1
                );
            return $data;
        }

        return $this->http_unauthorized();
    }

    function create(Request $request)
{
    if ($this->hasRole($request, "AJOUTER_VEHICULE")) {
        $this->checkValidation($request, [
            'matricule' => 'required',
        ]);
        $car = new Car;
        $car->matricule = $request->matricule;
        $car->prop = $request->prop;
        $car->old_matricule = $request->old_matricule;
        $car->color = $request->color;
        $car->code_gps = $request->code_gps;
        $car->genre = $request->genre;
        $car->marque = $request->marque;
        $car->type =  $request->type;
        $car->puissance =  $request->puissance;
        $car->energie = $request->energie;
        $car->carrosserie = $request->carrosserie;
        $car->createdby_id = $request->auth->id;
        try {
            $car->save();
        } catch (QueryException $e) {
            return $this->http_bad();
        }
        return $this->http_ok($car);
    }
    return $this->http_unauthorized();
}

    function delete($id, Request $request)
    {
        if ($this->hasRole($request, "SUPPRIMER_VEHICULE")) {
            try {
                Car::find($id);
            } catch (Exception $e) {
                return $this->http_not_found();
            }
            Car::destroy($id);
            return $this->http_ok();
        }
        return $this->http_unauthorized();
    }

    function update($id, Request $request)
    {
        try {
            $car = Car::find($id);
        } catch (Exception $e) {
            return $this->http_not_found();
        }
        if ($this->hasRole($request, "MODIFIER_VEHICULE")) {
            $this->checkValidation($request, [
                'matricule' => 'required',
            ]);
            $car->matricule = $request->matricule;
            $car->prop = $request->prop;
            $car->old_matricule = $request->old_matricule;
            $car->color = $request->color;
            $car->code_gps = $request->code_gps;
            $car->genre = $request->genre;
            $car->marque = $request->marque;
            $car->type =  $request->type;
            $car->puissance =  $request->puissance;
            $car->energie = $request->energie;
            $car->carrosserie = $request->carrosserie;
            $car->createdby_id = $request->auth->id;
            try {
                $car->save();
            } catch (QueryException $e) {
                return $this->http_bad();
            }
            return $this->http_ok($car);
        }
        return $this->http_unauthorized();
    }


    function dashboard_vl(Request $request)
    {
        $capacity_logistics_vl = DB::table("cars")
            ->join("car_group", "cars.id", "=", "car_group.car_id")
            ->join("groups", "groups.id", "=", "car_group.group_id")
            ->select([
                "groups.name"
            ])
            ->where("groups.name", "=", "LEGER")
            ->count();

        $en_panne_vl = DB::table("cars")
            ->join("car_group", "cars.id", "=", "car_group.car_id")
            ->join("groups", "groups.id", "=", "car_group.group_id")
            ->join('car_states', function ($join) {
                $join->on('cars.id', '=', 'car_states.car_id')
                    ->on('car_states.id', '=', DB::raw("(select max(id) from car_states WHERE car_states.car_id = cars.id)"));
            })
            ->select([
                "groups.name"
            ])
            ->where("groups.name", "=", "LEGER")
            ->whereIn("car_states.name", ["EN PANNE ATELIER", "EN PANNE PARC"])
            ->count();

        $accedente_v =  DB::table("cars")
            ->join("car_group", "cars.id", "=", "car_group.car_id")
            ->join("groups", "groups.id", "=", "car_group.group_id")
            ->join('car_states', function ($join) {
                $join->on('cars.id', '=', 'car_states.car_id')
                    ->on('car_states.id', '=', DB::raw("(select max(id) from car_states WHERE car_states.car_id = cars.id)"));
            })
            ->select([
                "groups.name"
            ])
            ->where("groups.name", "=", "LEGER")
            ->whereIn("car_states.name", ["ACCIDENTE"])
            ->count();


        $affecte_vl = DB::table("cars")
            ->join("car_group", "cars.id", "=", "car_group.car_id")
            ->join("groups", "groups.id", "=", "car_group.group_id")
            ->join('checklists', function ($join) {
                $join->on('cars.id', '=', 'checklists.car_id')
                    ->on('checklists.id', '=', DB::raw("(select max(id) from checklists WHERE checklists.car_id = cars.id)"));
            })
            ->join("decharges", "decharges.id", "=", "checklists.decharge_id")
            ->leftJoin('restititions', 'decharges.id', '=', "restititions.decharge_id")
            ->select([
                "groups.name"
            ])
            ->where("groups.name", "=", "LEGER")
            ->count();

        $operationel_vl = $capacity_logistics_vl - $accedente_v - $en_panne_vl;

        $non_exploite_vl = $operationel_vl - $affecte_vl;

        return $this->http_ok([
            "capacity_logistics_vl" => $capacity_logistics_vl,
            "en_panne_vl" => $en_panne_vl,
            "accedente_v" => $accedente_v,
            "affecte_vl" => $affecte_vl,
            "operationel_vl" => $operationel_vl,
            "non_exploite_vl" => $non_exploite_vl,
        ]);
    }

    function export(Request $request)
    {
        return Excel::download(new CarsExport, 'etat_vehicules.xlsx');
    }

    function import(Request $request)
    {
        //dump($request);
        if ($request->hasFile('cars')) {
            Excel::import(new CarsImport, $request->cars);
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