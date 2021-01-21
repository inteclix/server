<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

use App\Farmer;
use Illuminate\Support\Facades\Storage;
class FarmerController extends Controller
{
    function get($id, Request $request){
        try{
            $farmer = Farmer::findOrFail($id);
        } catch (Exception $e){
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }


        return new JsonResponse([
            'message' => 'Success get',
            'data' => $farmer
        ]);
    }

    function getAll(Request $request)
    {
        if($request->auth->role === 'admin'){
            $farmers = Farmer::with('user')->get();
            return new JsonResponse([
                'message' => 'Success get all',
                'data' => $farmers !== NULL ? $farmers : []
            ], Response::HTTP_OK);
        } else {
            $farmers = $request->auth->farmers()->with('user')->get();
            return new JsonResponse([
                'message' => 'Success get all',
                'data' => $farmers !== NULL ? $farmers : []
            ], Response::HTTP_OK);
        }

    }

    function create(Request $request)
    {

        try{
            unset($request["id"]);
            unset($request["percentCompletedImg1"]);
            unset($request["percentCompletedImg2"]);
            unset($request["percentCompletedImg3"]);
            $farmer = Farmer::create($request->all());
        } catch (Exception $e){
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
        try{
            $farmer->save();
        } catch (Exception $e){
            return new JsonResponse([
                'message' => 'error whene saving',
            ], Response::HTTP_BAD_REQUEST);
        }


        return new JsonResponse([
            'message' => 'Success create',
            'data' => $farmer
        ], Response::HTTP_CREATED);
    }

    function delete($id, Request $request){
        try{
            Farmer::findOrFail($id);
        } catch (Exception $e){
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        Farmer::destroy($id);
        return new JsonResponse([
            'message' => 'Deleted'
        ]);
    }

    function update($id, Request $request){
        try{
            $farmer = Farmer::findOrFail($id);
        } catch (Exception $e){
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        unset($request["loading"]);
        unset($request["percentCompletedImg1"]);
        unset($request["percentCompletedImg2"]);
        unset($request["percentCompletedImg3"]);
        $farmer->update($request->all());
        try{
            $farmer->save();
        } catch (QueryException $e){
            return new JsonResponse([
                'message' => 'Sql exception'
            ], Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse([
            'message' => 'updated',
            'data' => $farmer
        ]);
    }

    public function uploadImg1($id, Request $request)
    {
        try{
            $farmer = Farmer::findOrFail($id);
        } catch (Exception $e){
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        if ($request->hasFile('fileImg1')) {
            $imgSaved = Storage::put("public", $request->file('fileImg1'));
            if ($imgSaved) {
                $farmer->farmer_img1 = $imgSaved;
                //return $this->responseRequestSuccess($farmer);
                try{
                    $farmer->save();
                } catch (QueryException $e){
                    return new JsonResponse([
                        'message' => 'Sql exception' . $e
                    ], Response::HTTP_BAD_REQUEST);
                }
                return new JsonResponse([
                    'message' => 'Success update farmer',
                    'data' => $farmer
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
        try{
            $farmer = Farmer::findOrFail($id);
        } catch (Exception $e){
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        if ($request->hasFile('fileImg2')) {
            $imgSaved = Storage::put("public", $request->file('fileImg2'));
            if ($imgSaved) {
                $farmer->farmer_img2 = $imgSaved;
                //return $this->responseRequestSuccess($farmer);
                try{
                    $farmer->save();
                } catch (QueryException $e){
                    return new JsonResponse([
                        'message' => 'Sql exception' . $e
                    ], Response::HTTP_BAD_REQUEST);
                }
                return new JsonResponse([
                    'message' => 'Success update farmer',
                    'data' => $farmer
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
        try{
            $farmer = Farmer::findOrFail($id);
        } catch (Exception $e){
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        if ($request->hasFile('fileImg3')) {
            $imgSaved = Storage::put("public", $request->file('fileImg3'));
            if ($imgSaved) {
                $farmer->farmer_img3 = $imgSaved;
                //return $this->responseRequestSuccess($farmer);
                try{
                    $farmer->save();
                } catch (QueryException $e){
                    return new JsonResponse([
                        'message' => 'Sql exception' . $e
                    ], Response::HTTP_BAD_REQUEST);
                }
                return new JsonResponse([
                    'message' => 'Success update farmer',
                    'data' => $farmer
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
