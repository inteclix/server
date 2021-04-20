<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

use App\Objectif;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ObjectifController extends Controller
{
	function get($id, Request $request)
	{
		try {
			$objectif = Objectif::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}

		return new JsonResponse([
			'message' => 'Success get',
			'data' => $objectif
		]);
	}

	function search(Request $request)
	{
		//$offset = $request->offset | 0;
		//$limit = $request->limit | 20;
		$data = $request->get('data');
		$objectifs = Objectif::where('name', 'like', "%{$data}%")
			->take(8)
			->get();

		return new JsonResponse([
			'message' => 'Success get all',
			'data' => $objectifs !== NULL ? $objectifs : []
		], Response::HTTP_OK);
	}

	function getAll(Request $request)
	{
		if ($this->hasRole($request, "SMI_OBJECTIFS_OP")) {
			$objectifs = Objectif::orderBy("id", "asc")->get()->all();
			foreach ($objectifs as $obj) {
				$obj->processus = $obj->processus()->get();
				$obj->axe = $obj->axe()->get()->all()[0];
			}
			return new JsonResponse([
				'message' => 'Success get all',
				'data' => $objectifs !== NULL ? $objectifs : []
			], Response::HTTP_OK);
		} else {
			return new JsonResponse([
				'message' => 'UNAUTHORIZED',
				'data' => []
			], Response::HTTP_UNAUTHORIZED);
		}
	}

	function create(Request $request)
	{
		$this->validate($request, [
			"name" => "required",
			"axe_id" => "required",
			"processu_ids" => "required"
		]);
		if ($this->hasRole($request, "AJOUTER_OBJECTIF_OP")) {
			$objectif = new Objectif;
			$objectif->name = $request->name;
			$objectif->axe_id = $request->axe_id;

			try {
				$objectif->save();
				$objectif->processus()->sync($request->processu_ids);
				$objectif->save();
			} catch (QueryException $e) {
				return $this->error("Error!");
			}
			return $this->success($objectif, "Bien ajouter");
		} else {
			return $this->http_unauthorized();
		}
	}

	function delete($id, Request $request)
	{
		try {
			Objectif::findOrFail($id);
		} catch (Exception $e) {
			return $this->error("Id don't exist");
		}
		if ($this->hasRole($request, "SUPPRIMER_OBJECTIF_OP")) {
			try {
				Objectif::destroy($id);
			} catch (QueryException $e) {
				dd($e);
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
			$Objectif = Objectif::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}
		if ($request->hasFile('fileImg1')) {
			$imgSaved = Storage::put("public", $request->file('fileImg1'));
			if ($imgSaved) {
				$Objectif->Objectif_img1 = $imgSaved;
				//return $this->responseRequestSuccess($Objectif);
				try {
					$Objectif->save();
				} catch (QueryException $e) {
					return $this->http_bad();
				}
				return new JsonResponse([
					'message' => 'Success update Objectif',
					'data' => $Objectif
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
			$Objectif = Objectif::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}
		if ($request->hasFile('fileImg2')) {
			$imgSaved = Storage::put("public", $request->file('fileImg2'));
			if ($imgSaved) {
				$Objectif->Objectif_img2 = $imgSaved;
				//return $this->responseRequestSuccess($Objectif);
				try {
					$Objectif->save();
				} catch (QueryException $e) {
					return $this->http_bad();
				}
				return new JsonResponse([
					'message' => 'Success update Objectif',
					'data' => $Objectif
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
			$Objectif = Objectif::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}
		if ($request->hasFile('fileImg3')) {
			$imgSaved = Storage::put("public", $request->file('fileImg3'));
			if ($imgSaved) {
				$Objectif->Objectif_img3 = $imgSaved;
				//return $this->responseRequestSuccess($Objectif);
				try {
					$Objectif->save();
				} catch (QueryException $e) {
					return $this->http_bad();
				}
				return new JsonResponse([
					'message' => 'Success update Objectif',
					'data' => $Objectif
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
