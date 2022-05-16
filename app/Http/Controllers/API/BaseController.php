<?php

namespace App\Http\Controllers\API;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class BaseController extends Controller{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function __construct(){
  
    }
    public function sendResponse($result){
        $response = [];
        if(!empty($result)){
          $response = $result;
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
  
    public function sendError($error, $errorMessages = [], $code = 404){
      $response = [
      'success' => false,
      'message' => $error,
      ];
      if(!empty($errorMessages)){
        $response['data'] = $errorMessages;
      }
      return response()->json($response, $code);
      }
  }