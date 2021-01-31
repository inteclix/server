<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use App\CarState;
use App\Car;
use Illuminate\Support\Facades\DB;
use App\Notification;

class CarStateController extends Controller
{
    function get($id, Request $request)
    {
        if ($this->hasRole($request, "STATUS_VEHICULES")) {

            try {
                $carState = CarState::find($id)->with("car")->get()->first();
            } catch (Exception $e) {
                return $this->http_not_found();
            }
            return $this->http_ok($carState);
        }
        return $this->http_unauthorized();
    }

    function search(Request $request)
    {
        $data = $request->get('data');
        $carStates = CarState::where('designation', 'like', "%{$data}%")
            ->orWhere('code', 'like', "%{$data}%")
            ->take(8)
            ->get();
        return $this->http_ok($carStates ? $carStates : []);
    }

    function getAll(Request $request)
    {
        if ($this->hasRole($request, "STATUS_VEHICULES")) {
            $sort = $request->get("sort") === "ascend" ? "asc" : "desc";
            $sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
            $current = $request->get("current") ? $request->get("current") : 1;
            $pageSize = $request->get("pageSize") ? $request->get("pageSize") : 20;
            $auth_id = $request->auth->id;
            return DB::table("cars")
                ->join('car_states', 'car_states.car_id', '=', 'cars.id')
                ->join('users', 'users.id', '=', 'car_states.createdby_id')
                ->join('car_user', 'cars.id', '=', 'car_user.car_id')
                ->select([
                    "users.username as createdby",
                    "car_states.id as id",
                    "cars.matricule",
                    "cars.code_gps",
                    "car_states.name",
                    "car_states.state_date",
                    "car_user.user_id as car_user.user_id"
                ])
                ->orderBy($sortBy, $sort)
                ->where('cars.matricule', 'like', "%{$request->get("matricule")}%")
                ->where('car_user.user_id', '=', $auth_id)
                ->paginate(
                    $pageSize, // per page (may be get it from request)
                    ['*'], // columns to select from table (default *, means all fields)
                    'page', // page name that holds the page number in the query string
                    $current // current page, default 1
                );
        }
        return $this->http_unauthorized();
    }

    function create(Request $request)
    {
        $this->checkValidation($request, [
            'car_id' => 'required',
            'name' => 'required',
            'state_date' => 'required',
        ]);
        if ($this->hasRole($request, "MODIFIER_STATUS_VEHICULE")) {
            $latestState = Car::find($request->car_id)->latestState();
            if ($latestState && $latestState->name === $request->name) {
                return new JsonResponse([
                    'message' => "Status deja changé"
                ], Response::HTTP_BAD_REQUEST);
            }
            $c = new CarState;
            $c->car_id = $request->car_id;
            $c->name = $request->name;
            $c->state_date = $request->state_date;
            $c->observation = $request->observation;
            $c->createdby_id = $request->auth->id;
            try {
                $c->save();
            } catch (QueryException $e) {
                return $this->error("Error");
            }
            $car = Car::find($request->car_id);
            $users = $car->users()->get();
            foreach ($users as $user) {
                if ($user->id === $request->auth->id) {
                    continue;
                }
                $n = new Notification;
                $n->title = "Statu de vehicule: " . $car->code_gps . " | " . $car->matricule;
                $n->sub_title = $c->name . " ,par " . $request->auth->username;
                $n->url = "/cars_state/";
                $n->from_id = $request->auth->id;
                $n->to_id = $user->id;
                $n->type = "car_state";
                $n->type_id = $c->id;
                try {
                    $n->save();
                } catch (QueryException $e) {
                }
            }
            return $this->success($c, "success");
        }
        return $this->http_unauthorized();
    }

    function delete($id, Request $request)
    {
        try {
            $c = CarState::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        if ($request->auth->username === "admin" ||  $request->auth->id === $c->createdby_id) {
            $c->delete();
            $ns = Notification::where("type", "=", "car_state")->where("type_id", "=", $id)->get()->all();
            foreach ($ns as $n) {
                $n->delete();
            }
            return $this->success([], "deleted");
        }
        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }

    function update($id, Request $request)
    {
        try {
            $c = CarState::findOrFail($id);
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
            ]);
            $c->code = $request->code;
            $c->designation = $request->designation;
            $c->carState_id = $request->carState_id;
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
