<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

use App\Processu;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class ProcessuController extends Controller
{
	function get($id, Request $request)
	{
		try {
			$processu = Processu::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}

		return new JsonResponse([
			'message' => 'Success get',
			'data' => $processu
		]);
	}

	function getAll(Request $request)
	{
		if ($this->hasRole($request, "SMI_PROCESSUS")) {
			$processus = Processu::orderBy("id", "asc")->get()->all();
			foreach($processus as $processu){
				$processu->indicateurs = $processu->indicateurs()->get()->all();
			}
			return new JsonResponse([
				'message' => 'Success get all',
				'data' => $processus !== NULL ? $processus : []
			], Response::HTTP_OK);
		} else {
			$this->http_unauthorized();
		}
	}

	function getAlls(Request $request)
	{
		if ($request->auth->poste === 'admin') {
			$processus = Processu::orderBy("id", "asc")->get()->all();
			return new JsonResponse([
				'message' => 'Success get all',
				'data' => $processus !== NULL ? $processus : []
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
		$processus = Processu::where('name', 'like', "%{$data}%")
			->orWhere('slog', 'like', "%{$data}%")
			->take(8)
			->get();

		return new JsonResponse([
			'message' => 'Success get all',
			'data' => $processus !== NULL ? $processus : []
		], Response::HTTP_OK);
	}

	function create(Request $request)
	{
		$this->validate($request, [
			"name" => "required"
		]);
		$processu = new Processu;
		$processu->name = $request->name;
		$processu->slog = $request->slog;
		try {
			$processu->save();
		} catch (QueryException $e) {
			return $this->error("Error!");
		}
		return $this->success($processu, "Bien ajouter");
	}

	function delete($id, Request $request)
	{
		try {
			Processu::findOrFail($id);
		} catch (Exception $e) {
			return $this->error("Id don't exist");
		}
		try {
			Processu::destroy($id);
		} catch (QueryException $e) {
			return $this->error("Error !");
		}
		return $this->success([], "Bien Supprimer");
	}



	public function uploadImg1($id, Request $request)
	{
		try {
			$Processu = Processu::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}
		if ($request->hasFile('fileImg1')) {
			$imgSaved = Storage::put("public", $request->file('fileImg1'));
			if ($imgSaved) {
				$Processu->Processu_img1 = $imgSaved;
				//return $this->responseRequestSuccess($Processu);
				try {
					$Processu->save();
				} catch (QueryException $e) {
					return $this->http_bad();
				}
				return new JsonResponse([
					'message' => 'Success update Processu',
					'data' => $Processu
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
			$Processu = Processu::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}
		if ($request->hasFile('fileImg2')) {
			$imgSaved = Storage::put("public", $request->file('fileImg2'));
			if ($imgSaved) {
				$Processu->Processu_img2 = $imgSaved;
				//return $this->responseRequestSuccess($Processu);
				try {
					$Processu->save();
				} catch (QueryException $e) {
					return $this->http_bad();
				}
				return new JsonResponse([
					'message' => 'Success update Processu',
					'data' => $Processu
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
			$Processu = Processu::findOrFail($id);
		} catch (Exception $e) {
			return new JsonResponse([
				'message' => 'Id d\'ont exist',
			], Response::HTTP_BAD_REQUEST);
		}
		if ($request->hasFile('fileImg3')) {
			$imgSaved = Storage::put("public", $request->file('fileImg3'));
			if ($imgSaved) {
				$Processu->Processu_img3 = $imgSaved;
				//return $this->responseRequestSuccess($Processu);
				try {
					$Processu->save();
				} catch (QueryException $e) {
					return $this->http_bad();
				}
				return new JsonResponse([
					'message' => 'Success update Processu',
					'data' => $Processu
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
