<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

use App\Exports\FarmersExport;
use Maatwebsite\Excel\Facades\Excel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;


use App\User;
use App\Farmer;


class StateController extends Controller
{
    function getNumbersFarmersAndUsers(Request $request){
        if($request->auth->role === "admin"){
            return new JsonResponse([
                'nb_users' => User::count(),
                'nb_farmers' => Farmer::count(),
            ], Response::HTTP_OK);
        } else {
            return new JsonResponse([
                'nb_users' => 0,
                'nb_farmers' => $request->auth->farmers->count(),
            ], Response::HTTP_OK);
        }
    }

    function dowloadFarmers(Request $request){
        if($request->auth->role === "admin"){
            return Excel::download(new FarmersExport, 'farmers.xlsx');
        }else {
            return new JsonResponse([
                'nb_users' => 0,
                'nb_farmers' => $request->auth->farmers->count(),
            ], Response::HTTP_OK);
        }
    }

    function dowloadFarmersNoUser(Request $request){
        return Excel::download(new FarmersExport, 'farmers.xlsx');
    }

    function test(Request $request){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $farmers = Farmer::all()->toArray();
        $f = Farmer::first()->toArray();
        $col = "A";
        foreach($f as $key => $value){
            $sheet->setCellValue($col."1", $key);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        $lighn = 2;
        foreach($farmers as $farmer){
            $col = "A";
            foreach($farmer as $value){
                $sheet->setCellValue($col.$lighn, $value);
                
                $col++;
            }
            $lighn ++;
        }
        $writer = new Xlsx($spreadsheet);
        
        $response =  new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="ExportScan.xlsx"');
        $response->headers->set('Cache-Control','max-age=0');
        return $response;
    
    }

}
