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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class CarController extends Controller
{
    function get($id, Request $request)
    {
        try {
            $car = Car::findOrFail($id);
        } catch (Exception $e) {
            return $this->error('Id d\'ont exist');
        }
        return $this->success($car);
    }

    function getDechargeHistory(Request $request, $car_id)
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
            $car = Car::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
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
        $rows = DB::table("cars")
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
            ->get();

        return $this->success($rows, "all cars");
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

    function getAll(Request $request)
    {
        if ($request->auth->username !== "admin") {
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
                ])
                ->where('matricule', 'like', "%{$request->get("matricule")}%");
            if ($request->auth->username !== "admin") {
                $data = $data->where("user_id", "=", "{$request->auth->id}");
            }
            $data = $data->orderBy($sortBy, $sort)
                ->paginate(
                    $pageSize, // per page (may be get it from request)
                    ['*'], // columns to select from table (default *, means all fields)
                    'page', // page name that holds the page number in the query string
                    $current // current page, default 1
                );
            return $data;
        } else {
            $sort = $request->get("sort") === "ascend" ? "asc" : "desc";
            $sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
            $current = $request->get("current") ? $request->get("current") : 1;
            $pageSize = $request->get("pageSize") ? $request->get("pageSize") : 20;
            return DB::table("cars")
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
                ->leftJoin("car_group", "car_group.car_id", "=", "cars.id")
                ->leftJoin("groups", "car_group.group_id", "=", "groups.id")
                ->select([
                    "groups.name as groupName",
                    "clients.designation as client",
                    "checklists.date_checklist",
                    "cars.id as id",
                    "decharges.id as decharges.id",
                    "cars.matricule",
                    "cars.genre",
                    "cars.marque",
                    "cars.code_gps",
                    "car_states.name as state",
                    "car_states.state_date",
                    "date_restitition",
                    DB::raw("CONCAT(drivers.firstname, ' ',drivers.lastname) as drivers_fullname"),
                ])
                ->where('matricule', 'like', "%{$request->get("matricule")}%")
                ->orderBy($sortBy, $sort)
                ->paginate(
                    $pageSize, // per page (may be get it from request)
                    ['*'], // columns to select from table (default *, means all fields)
                    'page', // page name that holds the page number in the query string
                    $current // current page, default 1
                );
        }
    }

    function create(Request $request)
    {
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_CARS") {
                $has_role = true;
            }
        }
        if ($has_role) {
            $this->validate($request, [
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
                return new JsonResponse([
                    'message' => $e
                ], Response::HTTP_BAD_REQUEST);
            }
            return new JsonResponse([
                'message' => 'Success',
                'data' => $car
            ], Response::HTTP_CREATED);
        }
        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }

    function delete($id, Request $request)
    {
        try {
            Car::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        Car::destroy($id);
        return new JsonResponse([
            'message' => 'Deleted'
        ]);
    }

    function update($id, Request $request)
    {
        try {
            $car = Car::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_CARS") {
                $has_role = true;
            }
        }
        if ($has_role) {
            $this->validate($request, [
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
                return new JsonResponse([
                    'message' => $e
                ], Response::HTTP_BAD_REQUEST);
            }
            return new JsonResponse([
                'message' => 'Success',
                'data' => $car
            ], Response::HTTP_CREATED);
        }
        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }
    public function export(Request $request)
    {
        return Excel::download(new CarsExport, 'cars.xlsx');
    }
    public function import(Request $request)
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
