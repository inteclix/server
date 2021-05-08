<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

use App\Indicateur;
use App\Indicateurv;
use App\Processu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IndicateurController extends Controller
{
	function get($id, Request $request)
	{
		$sort = $request->get("sort") === "ascend" ? "asc" : "desc";
		$sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
		$current = $request->get("current") ? $request->get("current") : 1;
		$pageSize = $request->get("pageSize") ? $request->get("pageSize") : 20;
		$indicateur = Indicateur::findOrFail($id);
		if ($this->hasRole($request, "SMI_INDICATEURS")) {
			$data = DB::table("indicateurvs")
				->join("indicateurs", "indicateurs.id", "=", "indicateurvs.indicateur_id")
				->join("users as createdbys", "indicateurvs.createdby_id", "=", "createdbys.id")
				->select([
					"indicateurvs.id as id",
					"valeur",
					"date",
					"comment",
					"name",
					"seuil",
					"createdbys.username as createdbys_username"
				])
				->where("indicateurs.id", "=", $id)
				->orderBy("date->year", "asc")
				->orderBy("date->value", "asc")
				//->orderBy("date", "asc")
				->paginate(
					$pageSize, // per page (may be get it from request)
					['*'], // columns to select from table (default *, means all fields)
					'page', // page name that holds the page number in the query string
					$current // current page, default 1
				);
			return $this->http_ok($data);
		} else {
			$this->http_unauthorized();
		}
	}

	function getb($id, Request $request)
	{
		try {
			$indicateur = Indicateur::findOrFail($id);
			$indicateur->processu = $indicateur->processu()->get()->all()[0];
			$indicateur->objectif = $indicateur->objectif()->get()->all()[0];
			$indicateur->valeurs = $indicateur->valeurs()
				->orderByRaw("date->'$.year'", "asc")
				->orderByRaw("date->'$.value'", "asc")
				->take(8)->get()->all();
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}

		return new JsonResponse([
			'message' => 'Success get',
			'data' => $indicateur
		]);
	}

	function search(Request $request)
	{
		//$offset = $request->offset | 0;
		//$limit = $request->limit | 20;
		$data = $request->get('data');
		$indicateurs = Indicateur::where('name', 'like', "%{$data}%")
			->take(8)
			->get();

		return new JsonResponse([
			'message' => 'Success get all',
			'data' => $indicateurs !== NULL ? $indicateurs : []
		], Response::HTTP_OK);
	}

	function getAll(Request $request)
	{

		$sort = $request->get("sort") === "ascend" ? "asc" : "desc";
		$sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
		$current = $request->get("current") ? $request->get("current") : 1;
		$pageSize = $request->get("pageSize") ? $request->get("pageSize") : 20;
		if ($this->hasRole($request, "SMI_INDICATEURS")) {
			$indicateurs = Indicateur::orderBy("id", "asc")
				->where("processu_id", "=", $request->processu_id)
				->where('name', 'like', "%{$request->get("name")}%")
				->get()->all();
			$data = [];
			foreach ($indicateurs as $obj) {
				$obj->processu = $obj->processu()->get()->all()[0];
				$obj->objectif = $obj->objectif()->get()->all()[0];
				$obj->valeurs = $obj->valeurs()
					->orderByRaw("date->'$.year'", "asc")
					->orderByRaw("date->'$.value'", "asc")
					->take(8)
					->get();
				array_push($data, $obj);
			}
			return $this->http_ok($data);
		} else {
			$this->http_unauthorized();
		}
	}


	function create(Request $request)
	{
		$this->validate($request, [
			"name" => "required",
			"objectif_id" => "required",
			"processu_id" => "required"
		]);
		if ($this->hasRole($request, "SUPPRIMER_INDICATEUR")) {
			$indicateur = new Indicateur;
			$indicateur->name = $request->name;
			$indicateur->processu_id = $request->processu_id;
			$indicateur->objectif_id = $request->objectif_id;
			$indicateur->domaine = $request->domaine;
			$indicateur->description = $request->description;
			$indicateur->mesure = $request->mesure;
			$indicateur->seuil = $request->seuil;
			$indicateur->indicateur_sueil = $request->indicateur_sueil;
			$indicateur->methode_calcul = $request->methode_calcul;
			$indicateur->frequence = $request->frequence;
			$indicateur->mode_calcul = $request->mode_calcul;
			$indicateur->type = $request->type;
			try {
				$indicateur->save();
			} catch (QueryException $e) {
				dd($e);
				return $this->error("Error!");
			}
			return $this->success($indicateur, "Bien ajouter");
		} else {
			return $this->http_unauthorized();
		}
	}

	function createValeur(Request $request)
	{
		$this->validate($request, [
			"date" => "required",
			"valeur" => "required"
		]);
		$date = $request->date;
		$exist = Indicateurv::where("indicateur_id", "=", $request->indicateur_id)
			->when($date, function ($query, $date) {
				$date = json_decode($date);
				$query->where("date->type", "=", $date->type)
					->where("date->year", "=", $date->year)
					->where("date->value", "=", $date->value);
			})
			->get()->all();
		if (count($exist) > 0) {
			return $this->http_unauthorized("La valeur de cette date est deja existe");
		}
		if ($this->hasRole($request, "AJOUTER_VALEUR_INDICATEUR") && $this->hasRole($request, "PROCESSUS_" . Processu::find(Indicateur::find($request->indicateur_id)->processu_id)->slog)) {
			$indicateurv = new Indicateurv();
			$indicateurv->indicateur_id = $request->indicateur_id;

			$indicateurv->date = $request->date;
			$indicateurv->valeur = $request->valeur;
			$indicateurv->comment = $request->comment;
			$indicateurv->createdby_id = $request->auth->id;
			try {
				$indicateurv->save();
			} catch (QueryException $e) {
				//dd($e);
				return $this->error("Error!");
			}
			return $this->success($indicateurv, "Bien ajouter");
		} else {
			return $this->http_unauthorized();
		}
	}

	function delete($id, Request $request)
	{
		if ($this->hasRole($request, "SUPPRIMER_INDICATEUR")) {
			try {
				$indicateur = Indicateur::findOrFail($id);
			} catch (Exception $e) {
				return $this->error("Id don't exist");
			}
			if (!$this->hasRole($request, "PROCESSUS_" . Processu::find($indicateur->processu_id)->slog)) {
				return $this->http_unauthorized();
			}
			try {
				Indicateur::destroy($id);
			} catch (QueryException $e) {
				return $this->error("Error !");
			}
			return $this->success([], "Bien Supprimer");
		} else {
			return $this->http_unauthorized();
		}
	}
	function deleteValeur($id, Request $request)
	{
		if ($this->hasRole($request, "SUPPRIMER_INDICATEURV")) {
			try {
				$indicateurv = Indicateurv::findOrFail($id);
			} catch (Exception $e) {
				return $this->error("Id don't exist");
			}
			$processus = Processu::find(Indicateur::find($indicateurv->indicateur_id)->processu_id);
			if (!$this->hasRole($request, "PROCESSUS_" . $processus->slog)) {
				return $this->http_unauthorized();
			}
			try {
				Indicateurv::destroy($id);
			} catch (QueryException $e) {
				return $this->error("Error !");
			}
			return $this->success([], "Bien Supprimer");
		} else {
			return $this->http_unauthorized();
		}
	}

	function updateValeur($id, Request $request)
	{
		$indicateurv = Indicateurv::find($id);
		if ($this->hasRole($request, "AJOUTER_VALEUR_INDICATEUR") && $this->hasRole($request, "PROCESSUS_" . Processu::find(Indicateur::find($indicateurv->indicateur_id)->processu_id)->slog)) {
			$indicateurv->valeur = $request->valeur;
			$indicateurv->createdby_id = $request->auth->id;
			try {
				$indicateurv->save();
			} catch (QueryException $e) {
				return $this->error("Error !");
			}
			return $this->success($indicateurv);
		} else {
			return $this->http_unauthorized();
		}
	}



	public function uploadImg1($id, Request $request)
	{
		try {
			$Indicateur = Indicateur::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}
		if ($request->hasFile('fileImg1')) {
			$imgSaved = Storage::put("public", $request->file('fileImg1'));
			if ($imgSaved) {
				$Indicateur->Objectif_img1 = $imgSaved;
				//return $this->responseRequestSuccess($Indicateur);
				try {
					$Indicateur->save();
				} catch (QueryException $e) {
					return $this->http_bad();
				}
				return new JsonResponse([
					'message' => 'Success update Indicateur',
					'data' => $Indicateur
				], Response::HTTP_CREATED);
			} else {
				return new JsonResponse([
					'message' => 'Cannot upload file'
				], Response::HTTP_BAD_REQUEST);
			}
		} else {
			return new JsonResponse([
				'message' => 'File not found'
			], Response::HTTP_BAD_REQUEST);
		}
	}

	public function uploadImg2($id, Request $request)
	{
		try {
			$Indicateur = Indicateur::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}
		if ($request->hasFile('fileImg2')) {
			$imgSaved = Storage::put("public", $request->file('fileImg2'));
			if ($imgSaved) {
				$Indicateur->Objectif_img2 = $imgSaved;
				//return $this->responseRequestSuccess($Indicateur);
				try {
					$Indicateur->save();
				} catch (QueryException $e) {
					return $this->http_bad();
				}
				return new JsonResponse([
					'message' => 'Success update Indicateur',
					'data' => $Indicateur
				], Response::HTTP_CREATED);
			} else {
				return new JsonResponse([
					'message' => 'Cannot upload file'
				], Response::HTTP_BAD_REQUEST);
			}
		} else {
			return new JsonResponse([
				'message' => 'File not found'
			], Response::HTTP_BAD_REQUEST);
		}
	}

	public function uploadImg3($id, Request $request)
	{
		try {
			$Indicateur = Indicateur::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}
		if ($request->hasFile('fileImg3')) {
			$imgSaved = Storage::put("public", $request->file('fileImg3'));
			if ($imgSaved) {
				$Indicateur->Objectif_img3 = $imgSaved;
				//return $this->responseRequestSuccess($Indicateur);
				try {
					$Indicateur->save();
				} catch (QueryException $e) {
					return $this->http_bad();
				}
				return new JsonResponse([
					'message' => 'Success update Indicateur',
					'data' => $Indicateur
				], Response::HTTP_CREATED);
			} else {
				return new JsonResponse([
					'message' => 'Cannot upload file'
				], Response::HTTP_BAD_REQUEST);
			}
		} else {
			return new JsonResponse([
				'message' => 'File not found'
			], Response::HTTP_BAD_REQUEST);
		}
	}
}
