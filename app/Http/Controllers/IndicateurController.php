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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IndicateurController extends Controller
{
	function get($id, Request $request)
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
		if ($this->hasRole($request, "SMI_INDICATEURS")) {

			$indicateurs = Indicateur::orderBy("id", "asc")->get()->all();
			$data = [];
			foreach ($indicateurs as $obj) {
				$obj->processu = $obj->processu()->get()->all()[0];
				$obj->objectif = $obj->objectif()->get()->all()[0];
				$obj->valeurs = $obj->valeurs()
					->orderByRaw("date->'$.year'", "asc")
					->orderByRaw("date->'$.value'", "asc")
					->get()->all();
				if ($this->hasRole($request, "PROCESSUS_" . $obj->processu->slog)) {
					array_push($data, $obj);
				}
			}
			return new JsonResponse([
				'message' => 'Success get all',
				'data' => $data !== NULL ? $data : []
			], Response::HTTP_OK);
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

		if ($this->hasRole($request, "AJOUTER_VALEUR_INDICATEUR")) {
			$indicateurv = new Indicateurv();
			$indicateurv->indicateur_id = $request->indicateur_id;
			$indicateurv->date = $request->date;
			$indicateurv->valeur = $request->valeur;
			$indicateurv->comment = $request->comment;
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
				Indicateur::findOrFail($id);
			} catch (Exception $e) {
				return $this->error("Id don't exist");
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
		if ($this->hasRole($request, "SUPPRIMER_VALEUR_INDICATEUR")) {
			try {
				Indicateurv::findOrFail($id);
			} catch (Exception $e) {
				return $this->error("Id don't exist");
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
