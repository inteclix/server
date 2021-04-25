<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Missionvl;
use App\Car;
use GrahamCampbell\ResultType\Result;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CollectionsExport;

class MissionvlController extends Controller
{
	function checkIfCarAvailableForMission($car_id)
	{
		$data = DB::table("cars")
			->join('missionvls', function ($join) {
				$join->on('cars.id', '=', 'missionvls.car_id')
					->on('missionvls.id', '=', DB::raw("(select max(id) from missionvls WHERE missionvls.car_id = cars.id)"));
			})
			->select(["missionvls.state"])->where("cars.id", "=", $car_id)->where("missionvls.state", "<>", "FIN MISSION")
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
		]);
		if (!$this->checkIfCarAvailableForMission($request->car_id)) {
			return $this->http_unauthorized("Le véhicule est déjà en cours de missionvl");
		}
		if ($request->type == "VL") {
			if ($this->hasRole($request, "AJOUTER_MISSION_VL")) {
				$missionvl = new Missionvl;
				$missionvl->createdby_id = $request->auth->id;
				$missionvl->numero = $request->numero;
				$missionvl->car_id = $request->car_id;
				$missionvl->client_id = $request->client_id;
				$missionvl->driver1_id = $request->driver1_id;
				$missionvl->driver2_id = $request->driver2_id;
				$missionvl->date_bon_mission = $request->date_bon_mission;
				$missionvl->depart_id = $request->depart_id;
				$missionvl->destinations = $request->destinations;
				$missionvl->date_depart_mission = $request->date_depart_mission;
				$missionvl->date_arrivee_mission = $request->date_arrivee_mission;
				$missionvl->observation = $request->observation;
				$missionvl->state = "EN ATTENTE";
				try {
					$missionvl->save();
				} catch (QueryException $e) {
					return $this->http_bad();
				}
				return $this->http_ok($missionvl);
			}
		}

		return $this->http_unauthorized();
	}

	function update(Request $request, $id)
	{
		$this->checkValidation($request, [
			"type" => "required",
			"car_id" => "required",
			"client_id" => "required",
			"driver1_id" => "required",
			"depart_id" => "required",
		]);

		if ($request->type == "VL") {
			try {
				$missionvl = Missionvl::find($id);
			} catch (QueryException $e) {
				return $this->http_not_found();
			}
			if ($this->hasRole($request, "MODIFIER_MISSION_VL")) {
				$missionvl->numero = $request->numero;
				$missionvl->car_id = $request->car_id;
				$missionvl->client_id = $request->client_id;
				$missionvl->driver1_id = $request->driver1_id;
				$missionvl->driver2_id = $request->driver2_id;
				$missionvl->date_bon_mission = $request->date_bon_mission;
				$missionvl->depart_id = $request->depart_id;
				$missionvl->destinations = $request->destinations;
				$missionvl->date_depart_mission = $request->date_depart_mission;
				$missionvl->date_arrivee_mission = $request->date_arrivee_mission;
				$missionvl->observation = $request->observation;
				try {
					$missionvl->save();
				} catch (QueryException $e) {
					return $this->http_bad();
				}
				return $this->http_ok($missionvl);
			}
		}

		return $this->http_unauthorized();
	}


	function deleteMission(Request $request, $id)
	{
		if ($this->hasRole($request, "SUPPRIMER_MISSION_VL")) {
			try {
				Missionvl::find($id);
			} catch (QueryException $e) {
				return $this->http_not_found();
			}
			Missionvl::destroy($id);
			return $this->http_ok();
		}
		return $this->http_unauthorized();
	}

	function changeStateMission(Request $request, $id)
	{
		$this->validate($request, [
			"state" => "required",
		]);
		if ($this->hasRole($request, "CHANGE_STATE_MISSION_VL")) {
			try {
				$mission = Missionvl::find($id);
			} catch (QueryException $e) {
				return $this->http_not_found();
			}
			$mission->state = $request->state;
			try {
				$mission->save();
			} catch (QueryException $e) {
				return $this->http_bad();
			}
			return $this->http_ok();
		}
		return $this->http_unauthorized();
	}

	function accepteMission(Request $request, $id)
	{
		if ($this->hasRole($request, "VALIDER_MISSION_VL")) {
			try {
				$mission = Missionvl::find($id);
			} catch (QueryException $e) {
				return $this->http_not_found();
			}
			$mission->acceptedby_id = $request->auth->id;
			try {
				$mission->save();
			} catch (QueryException $e) {
				return $this->http_bad();
			}
			return $this->http_ok();
		}
		return $this->http_unauthorized();
	}


	function getMission(Request $request, $id)
	{
		try {
			$missionvl = Missionvl::find($id);
		} catch (QueryException $e) {
			return $this->http_bad();
		}

		if ($missionvl->car_id) {
			$missionvl->car = $missionvl->car()->get()->all()[0];
		}
		if ($missionvl->remourque_id) {
			$missionvl->remourque = $missionvl->remourque()->get()->all()[0];
		}
		if ($missionvl->driver1_id) {
			$missionvl->driver1 = $missionvl->driver1()->get()->all()[0];
		}
		if ($missionvl->driver2_id) {
			$missionvl->driver2 = $missionvl->driver2()->get()->all()[0];
		}
		if ($missionvl->client_id) {
			$missionvl->client = $missionvl->client()->get()->all()[0];
		}
		if ($missionvl->depart_id) {
			$missionvl->depart = $missionvl->depart()->get()->all()[0];
		}
		if ($missionvl->destination_id) {
			$missionvl->destination = $missionvl->destination()->get()->all()[0];
		}


		return $this->http_ok($missionvl);
	}

	function getMissions(Request $request)
	{
		$sort = $request->get("sort") === "ascend" ? "asc" : "desc";
		$sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
		$current = $request->get("current") ? $request->get("current") : 1;
		$pageSize = $request->get("pageSize") ? $request->get("pageSize") : 20;
		$missionvls = DB::table("missionvls")
			->join("clients", "clients.id", "=", "missionvls.client_id")
			->leftJoin('clients as clients_mother', 'clients.client_id', '=', 'clients_mother.id')
			->join("cars", "cars.id", "=", "missionvls.car_id")
			->leftjoin("cars as remourques", "remourques.id", "=", "missionvls.remourque_id")
			->join("drivers as driver1s", "driver1s.id", "=", "missionvls.driver1_id")
			->leftJoin("drivers as driver2s", "driver2s.id", "=", "missionvls.driver2_id")
			->join("cities as departs", "departs.id", "=", "missionvls.depart_id")
			->join("users as createdbys", "createdbys.id", "=", "missionvls.createdby_id")
			->leftJoin("users as gpsbys", "createdbys.id", "=", "missionvls.gpsby_id")
			->leftJoin("users as acceptedbys", "acceptedbys.id", "=", "missionvls.acceptedby_id")
			->join("car_group", "car_group.car_id", "=", "cars.id")
			->join("groups", "groups.id", "=", "car_group.group_id");

		if ($request->format == "excel") {
			$missionvls = $missionvls->select([
				"missionvls.id as id",
				"cars.matricule as cars_matricule",
				"cars.code_gps as cars_code_gps",
				"clients.designation as clients_designation",
				DB::raw("CONCAT(driver1s.firstname, ' ',driver1s.lastname) as driver1s_fullname"),
				DB::raw("CONCAT(departs.wilaya_name, ', ',departs.daira_name, ', ', departs.commune_name) as depart"),
				"missionvls.destinations as destinations",
				//"missionvls.date_depart_mission as missions_date_depart_mission",
				//"missionvls.date_arrivee_mission as missions_date_arrivee_mission",
				DB::raw("DATE_FORMAT(missionvls.date_depart_mission, '%d/%m/%Y') as missions_date_depart_mission"),
				DB::raw("DATE_FORMAT(missionvls.date_arrivee_mission, '%d/%m/%Y') as missions_date_arrivee_mission"),
				"missionvls.state as missionvls_state",
				"missionvls.state as missionvls_state",
				"createdbys.username as createdby_username",
				//"gpsbys.username as gpsby_username",
				"acceptedbys.username as acceptedby_username",
			]);
		} else {
			$missionvls = $missionvls->select([
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
				"missionvls.destinations as destinations",
				"createdbys.username as createdby_username",
				"gpsbys.username as gpsby_username",
				"acceptedbys.username as acceptedby_username",
				"missionvls.id as id",
				"missionvls.date_depart_mission as missions_date_depart_mission",
				"missionvls.date_arrivee_mission as missions_date_arrivee_mission",
				"missionvls.state as missionvls_state"
			]);
		}

		$missionvls = $missionvls
			->where("missionvls.date_depart_mission", ">=", "{$request->date1}")
			->where("missionvls.date_depart_mission", "<=", "{$request->date2}")
			->where('cars.matricule', 'like', "%{$request->get("cars_matricule")}%")
			//->where('remourques.matricule', 'like', "%{$request->get("remourques_matricule")}%")
			->where('cars.code_gps', 'like', "%{$request->get("code_gps")}%")
			->where('clients.designation', 'like', "%{$request->get("clients_designation")}%")
			->orderBy($sortBy, $sort);
		//->where("owners.id", "=", $request->auth->id)
		//->where('groups.name', '=', $request->group)
		if ($request->format == "excel") {
			$missionvls = $missionvls->get();
			$missionvls->prepend([
				"id" => "ID",
				"cars_matricule" => "MATRICULE",
				"cars_code_gps" => "CODE GPS",
				"clients_designation" => "CLIENT",
				"driver1s_fullname" => "CONDUCTEUR",
				"depart" => "DEPART",
				"destination" => "ARRIVEE",
				"missionvls.date_depart_mission" => "DATE DEPART",
				"missionvls.date_arrivee_mission" => "DATE ARRIVEE",
				"missionvls.state" => "STATUS",
				"createdby_username" => "CREE PAR",
				//"gpsby_username" => "GPS PAR",
				"acceptedby_username" => "VALIDER PAR",
			]);
			return Excel::download(new CollectionsExport($missionvls), 'etat_missions_vl.xlsx');
		}
		return $missionvls
			->paginate(
				$pageSize, // per page (may be get it from request)
				['*'], // columns to select from table (default *, means all fields)
				'page', // page name that holds the page number in the query string
				$current // current page, default 1
			);
		return $missionvls;
	}
}
