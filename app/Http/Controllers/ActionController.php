<?php

namespace App\Http\Controllers;

use App\Action;
use App\Conformite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

use App\Indicateur;
use App\Indicateurv;
use App\Objectif;
use App\Processu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ActionController extends Controller
{
	function create(Request $request)
	{
		if ($this->hasRole($request, "SMI_AJOUTER_ACTION")) {
			$nac = new Action;
			$nac->name = $request["action"];
			$nac->date_echeance = $request["echeance"];
			$nac->users = json_encode($request["users"]);
			$nac->createdby_id = $request->auth->id;
			$nac->conformite_id = $request->conformite_id;
			$nac->processu_id  = $request->processu_id ;
			if ($request->conformite_id) {
				$c = Conformite::find($request->conformite_id);
				if ($c->createdby_id !== $nac->createdby_id) {
					return $this->http_unauthorized();
				}
				$c->avancement = 90;
				$c->date_cloture = null;
				try {
					$c->save();
				} catch (QueryException $e) {
					//dd($e);
					return $this->error("Error!");
				}
			}
			try {
				$nac->save();
			} catch (QueryException $e) {
				//dd($e);
				return $this->error("Error!");
			}
			return $this->success($nac, "Bien ajouter");
		} else {
			return $this->http_unauthorized();
		}
	}

	function getAll(Request $request)
	{
		$sort = $request->get("sort") === "ascend" ? "asc" : "desc";
		$sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
		$current = $request->get("current") ? $request->get("current") : 1;
		$pageSize = $request->get("pageSize") ? $request->get("pageSize") : 20;
		if ($this->hasRole($request, "SMI_LISTE_ACTIONS")) {
			$actions = DB::table("actions")
				->join("users as createdbys", "createdbys.id", "=", "actions.createdby_id")
				->leftJoin("users as acceptedbys", "acceptedbys.id", "=", "actions.acceptedby_id")
				->leftJoin("conformites", "conformites.id", "=", "actions.conformite_id")
				->join("processus", "processus.id", "=", "actions.processu_id")
				->select([
					"actions.id as id",
					"actions.name as actions_name",
					"actions.date_echeance as actions_date_echeance",
					"actions.avancement as actions_avancement",
					"actions.date_cloture as actions_date_cloture",
					"actions.efficace as actions_efficace",
					"actions.observation as actions_observation",
					"actions.users as actions_users",
					"actions.state as actions_state",
					"actions.users as actions_users",
					"conformites.name as conformites_name",
					"processus.slog as processus_slog",
					"createdbys.username as createdbys_username",
					"acceptedbys.username as acceptedbys_username"
				])
				->where("actions.name", "like", "%{$request->get("actions_name")}%")
				->orderBy($sortBy, $sort)
				->paginate(
					$pageSize, // per page (may be get it from request)
					['*'], // columns to select from table (default *, means all fields)
					'page', // page name that holds the page number in the query string
					$current // current page, default 1
				);
			return $actions;
		} else {
			$this->http_unauthorized();
		}
	}

	function correcte(Request $request, $id)
	{
		$action = Action::find($id);
		if ($action->createdby_id === $request->auth->id) {
			$action->observation = $request->observation;
			$action->date_cloture = $request->date_cloture;
			$action->avancement = $request->avancement;
			try {
				$action->save();
			} catch (QueryException $e) {
				dd($e);
				return $this->error("Error!");
			}
			return $this->http_ok($action);
		}
		return $this->http_unauthorized();
	}

	function accepte(Request $request, $id)
	{
		$nonconformite = Conformite::find($id);
		if ($nonconformite->createdby_id === $request->auth->id) {
			if ($request->correctives && count($request->correctives) !== 0 && count($nonconformite->actions()->get()->all()) !== 0) {
				//Action::where("conformite_id", "=", $id)->delete();
			}
			$nonconformite->corrections = $request->corrections;
			$nonconformite->date_cloture = $request->date_cloture;
			$nonconformite->avancement = $request->avancement;
			if ($request->correctives) {
				$nonconformite->causes = $request->causes;
				foreach ($request->correctives as $ac) {
					$nac = new Action;
					$nac->name = $ac["action"];
					$nac->date_echeance = $ac["echeance"];
					$nac->users = json_encode($ac["users"]);
					$nac->conformite_id = $id;
					try {
						$nac->save();
					} catch (QueryException $e) {
						dd($e);
						return $this->error("Error!");
					}
				}
			}

			try {
				$nonconformite->save();
			} catch (QueryException $e) {
				dd($e);
				return $this->error("Error!");
			}
			return $this->http_ok($nonconformite);
		}
		return $this->http_unauthorized();
	}

	function delete(Request $request, $id)
	{
		if ($this->hasRole($request, "SUPPRIMER_ACTION")) {
			try {
				Action::destroy($id);
			} catch (QueryException $e) {
				dd($e);
				return $this->error("Error!");
			}
			return $this->http_ok();
		}

		return $this->http_unauthorized();
	}
}
