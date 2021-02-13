<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Mission;
use App\Car;

class MissionController extends Controller
{
    function checkIfCarAvailableForMission($car_id)
    {
        $data = DB::table("cars")
            ->join('missions', function ($join) {
                $join->on('cars.id', '=', 'missions.car_id')
                    ->on('missions.id', '=', DB::raw("(select max(id) from missions WHERE missions.car_id = cars.id)"));
            })
            ->select(["missions.state"])->where("cars.id", "=", $car_id)->where("missions.state", "<>", "FIN MISSION")
            ->get()->all();
        //dump($data);
        return count($data) == 0 ? true : false;
    }


    function create(Request $request)
    {
        $this->checkValidation($request, [
            "type" => "required",
            "car_id" => "required",
            "client_id" => "required",
            "driver1_id" => "required",
            "depart_id" => "required",
            "destination_id" => "required",
        ]);
        if (!$this->checkIfCarAvailableForMission($request->car_id)) {
            return $this->http_unauthorized("Le véhicule est déjà en cours de mission");
        }
        if ($request->type == "VL") {
            if ($this->hasRole($request, "AJOUTER_MISSION_VL")) {
                $mission = new Mission;
                $mission->createdby_id = $request->auth->id;
                $mission->numero = $request->numero;
                $mission->car_id = $request->car_id;
                $mission->client_id = $request->client_id;
                $mission->driver1_id = $request->driver1_id;
                $mission->driver2_id = $request->driver2_id;
                $mission->date_bon_mission = $request->date_bon_mission;
                $mission->depart_id = $request->depart_id;
                $mission->destination_id = $request->destination_id;
                $mission->date_depart_mission = $request->date_depart_mission;
                $mission->date_arrivee_mission = $request->date_arrivee_mission;
                $mission->observation = $request->observation;
                $mission->state = "EN ATTENTE DE CHARGEMENT";
                try {
                    $mission->save();
                } catch (QueryException $e) {
                    return $this->http_bad();
                }
                return $this->http_ok($mission);
            }
        }

        return $this->http_unauthorized();
    }

    function getMissions(Request $request)
    {
        $this->checkValidation($request, [
            "group" => "required",
        ]);
        $sort = $request->get("sort") === "ascend" ? "asc" : "desc";
        $sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
        $current = $request->get("current") ? $request->get("current") : 1;
        $pageSize = $request->get("pageSize") ? $request->get("pageSize") : 20;
        if ($request->group == "LEGER") {
            $missions = DB::table("missions")
                ->join("clients", "clients.id", "=", "missions.client_id")
                ->leftJoin('clients as clients_mother', 'clients.client_id', '=', 'clients_mother.id')
                ->join("cars", "cars.id", "=", "missions.car_id")
                ->leftjoin("cars as remourques", "remourques.id", "=", "missions.remourque_id")
                ->join("drivers as driver1s", "driver1s.id", "=", "missions.driver1_id")
                ->leftJoin("drivers as driver2s", "driver2s.id", "=", "missions.driver2_id")
                ->join("cities as departs", "departs.id", "=", "missions.depart_id")
                ->join("cities as destinations", "destinations.id", "=", "missions.destination_id")
                ->join("users as createdbys", "createdbys.id", "=", "missions.createdby_id")
                ->leftJoin("users as gpsbys", "createdbys.id", "=", "missions.gpsby_id")
                ->leftJoin("users as acceptedbys", "acceptedbys.id", "=", "missions.acceptedby_id")
                ->join("car_group", "car_group.car_id", "=", "cars.id")
                ->join("groups", "groups.id", "=", "car_group.group_id")
                ->select([
                    "cars.matricule as cars_matricule",
                    "cars.code_gps as cars_code_gps",
                    "remourques.matricule as remourques_matricule",
                    DB::raw("CONCAT(driver1s.firstname, ' ',driver1s.lastname) as driver1s_fullname"),
                    DB::raw("CONCAT(driver2s.firstname, ' ',driver2s.lastname) as driver2s_fullname"),
                    "clients.designation as clients_designation",
                    'clients_mother.designation as clients_mother_designation',
                    "departs.wilaya_name as departs_wilaya_name",
                    "departs.daira_name as departs_daira_name",
                    "departs.commune_name as departs_commune_name",
                    "destinations.wilaya_name as destinations_wilaya_name",
                    "destinations.daira_name as destinations_daira_name",
                    "destinations.commune_name as destinations_commune_name",
                    "createdbys.username as createdbys_username",
                    "gpsbys.username as gpsbys_username",
                    "acceptedbys.username as acceptedbys_username",
                    "missions.id as id",
                    "missions.date_depart_mission as missions_date_depart_mission",
                    "missions.date_arrivee_mission as missions_date_arrivee_mission"
                ])
                //->where("owners.id", "=", $request->auth->id)
                ->where('groups.name', '=', $request->group)
                ->where('cars.matricule', 'like', "%{$request->get("cars_matricule")}%")
                //->where('remourques.matricule', 'like', "%{$request->get("remourques_matricule")}%")
                ->where('cars.code_gps', 'like', "%{$request->get("code_gps")}%")
                ->where('clients.designation', 'like', "%{$request->get("clients_designation")}%")
                ->orderBy($sortBy, $sort)
                ->paginate(
                    $pageSize, // per page (may be get it from request)
                    ['*'], // columns to select from table (default *, means all fields)
                    'page', // page name that holds the page number in the query string
                    $current // current page, default 1
                );
            return $missions;
        }
    }
}
