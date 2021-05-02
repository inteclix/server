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
use App\Processu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConformiteController extends Controller
{
	function create(Request $request)
	{
		$this->validate($request, [
			"name" => "required",
			"processu_id" => "required"
		]);
		if ($this->hasRole($request, "SMI_AJOUTER_NON_CONFORMITE")) {
			$nonconformite = new Conformite;
			$nonconformite->name = $request->name;
			$nonconformite->nature = $request->nature;
			$nonconformite->nature_label = $request->nature_label;
			$nonconformite->date = $request->date;
			$nonconformite->description = $request->description;
			$nonconformite->processu_id = $request->processu_id;
			$nonconformite->domaine = $request->domaine;
			$nonconformite->indicateurv_id = $request->indicateurv_id;
			$nonconformite->createdby_id = $request->auth->id;
			try {
				$nonconformite->save();
			} catch (QueryException $e) {
				//dd($e);
				return $this->error("Error!");
			}
			return $this->success($nonconformite, "Bien ajouter");
		} else {
			return $this->http_unauthorized();
		}
	}

	function search(Request $request)
	{
		$data = $request->get('data');
		$axes = Conformite::where('name', 'like', "%{$data}%")
			->take(8)
			->get();

		return new JsonResponse([
			'message' => 'Success get all',
			'data' => $axes !== NULL ? $axes : []
		], Response::HTTP_OK);
	}

	function delete($id, Request $request)
	{
		try {
			$c = Conformite::findOrFail($id);
		} catch (Exception $e) {
			return $this->error("Id don't exist");
		}
		if ($c->createdby_id !== $request->auth->id) {
			return $this->http_unauthorized();
		}
		try {
			Conformite::destroy($id);
		} catch (QueryException $e) {
			return $this->error("Error !");
		}
		return $this->success([], "Bien Supprimer");
	}

	function getAll(Request $request)
	{
		if ($this->hasRole($request, "SMI_LISTE_CONFORMITES")) {

			$nonconformite = Conformite::orderBy("id", "asc")->with("actions")->get()->all();
			$data = [];
			foreach ($nonconformite as $obj) {
				$obj->processu = $obj->processu()->get()->all()[0];
				$obj->createdby = $obj->createdby()->get()->all()[0];
				//TODO: acceptedby
				if ($this->hasRole($request, "PROCESSUS_" . $obj->processu->slog)) {
					array_push($data, $obj);
				}
			}
			return $this->http_ok($data);
		} else {
			$this->http_unauthorized();
		}
	}

	function correcte(Request $request, $id)
	{
		$nonconformite = Conformite::find($id);
		if ($this->hasRole($request, "PROCESSUS_" . Processu::find($nonconformite->processu_id)->slog)) {
			if ($request->correctives && count($request->correctives) !== 0 && count($nonconformite->actions()->get()->all()) !== 0) {
				//Action::where("conformite_id", "=", $id)->delete();
			}
			$nonconformite->corrections = $request->corrections;
			$nonconformite->date_cloture = $request->date_cloture;
			$nonconformite->avancement = $request->avancement;
			$nonconformite->causes = $request->causes;
			if ($request->correctives) {
				$nonconformite->causes = $request->causes;
				foreach ($request->correctives as $ac) {
					$nac = new Action;
					$nac->name = $ac["action"];
					$nac->date_echeance = $ac["echeance"];
					$nac->users = json_encode($ac["users"]);
					$nac->conformite_id = $id;
					$nac->processu_id = $nonconformite->processu_id;
					$nac->createdby_id = $request->auth->id;
					try {
						$nac->save();
					} catch (QueryException $e) {
						//dd($e);
						return $this->error("Error!");
					}
					$nonconformite->avancement = 90;
					$nonconformite->date_cloture = null;
				}
			}

			try {
				$nonconformite->save();
			} catch (QueryException $e) {
				//dd($e);
				return $this->error("Error!");
			}
			return $this->http_ok($nonconformite);
		}
		return $this->http_unauthorized();
	}
}
