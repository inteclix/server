<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

use App\Axe;
use Illuminate\Support\Facades\Storage;

class AxeController extends Controller
{
	function get($id, Request $request)
	{
		try {
			$axe = Axe::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}

		return new JsonResponse([
			'message' => 'Success get',
			'data' => $axe
		]);
	}

	function getAll(Request $request)
	{
		if ($this->hasRole($request, "SMI_AXES")) {
			$axes = Axe::orderBy("id", "asc")->get()->all();
			return new JsonResponse([
				'message' => 'Success get all',
				'data' => $axes !== NULL ? $axes : []
			], Response::HTTP_OK);
		} else {
			return $this->http_unauthorized();
		}
	}

	function getAlls(Request $request)
	{
		if ($request->auth->poste === 'admin') {
			$axes = Axe::orderBy("id", "asc")->get()->all();
			return new JsonResponse([
				'message' => 'Success get all',
				'data' => $axes !== NULL ? $axes : []
			], Response::HTTP_OK);
		} else {
			return new JsonResponse([
				'message' => 'UNAUTHORIZED',
				'data' => []
			], Response::HTTP_UNAUTHORIZED);
		}
	}

	function search(Request $request)
	{
		//$offset = $request->offset | 0;
		//$limit = $request->limit | 20;
		$data = $request->get('data');
		$axes = Axe::where('name', 'like', "%{$data}%")
			->orWhere('slog', 'like', "%{$data}%")
			->take(8)
			->get();

		return new JsonResponse([
			'message' => 'Success get all',
			'data' => $axes !== NULL ? $axes : []
		], Response::HTTP_OK);
	}

	function create(Request $request)
	{
		$this->validate($request, [
			"name" => "required"
		]);
		$axe = new Axe;
		$axe->name = $request->name;
		$axe->slog = $request->slog;
		try {
			$axe->save();
		} catch (QueryException $e) {
			return $this->error("Error!");
		}
		return $this->success($axe, "Bien ajouter");
	}

	function delete($id, Request $request)
	{
		try {
			Axe::findOrFail($id);
		} catch (Exception $e) {
			return $this->error("Id don't exist");
		}
		try {
			Axe::destroy($id);
		} catch (QueryException $e) {
			return $this->error("Error !");
		}
		return $this->success([], "Bien Supprimer");
	}



	public function uploadImg1($id, Request $request)
	{
		try {
			$Axe = Axe::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}
		if ($request->hasFile('fileImg1')) {
			$imgSaved = Storage::put("public", $request->file('fileImg1'));
			if ($imgSaved) {
				$Axe->Axe_img1 = $imgSaved;
				//return $this->responseRequestSuccess($Axe);
				try {
					$Axe->save();
				} catch (QueryException $e) {
					return $this->http_bad();
				}
				return new JsonResponse([
					'message' => 'Success update Axe',
					'data' => $Axe
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
			$Axe = Axe::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}
		if ($request->hasFile('fileImg2')) {
			$imgSaved = Storage::put("public", $request->file('fileImg2'));
			if ($imgSaved) {
				$Axe->Axe_img2 = $imgSaved;
				//return $this->responseRequestSuccess($Axe);
				try {
					$Axe->save();
				} catch (QueryException $e) {
					return $this->http_bad();
				}
				return new JsonResponse([
					'message' => 'Success update Axe',
					'data' => $Axe
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
			$Axe = Axe::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}
		if ($request->hasFile('fileImg3')) {
			$imgSaved = Storage::put("public", $request->file('fileImg3'));
			if ($imgSaved) {
				$Axe->Axe_img3 = $imgSaved;
				//return $this->responseRequestSuccess($Axe);
				try {
					$Axe->save();
				} catch (QueryException $e) {
					return $this->http_bad();
				}
				return new JsonResponse([
					'message' => 'Success update Axe',
					'data' => $Axe
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
