<?php 

namespace App\Traits;

trait HttpResponses {

    protected function successResponse($data, $message=null, $statusCode=200){

        return response()->json([
           'status' => 'Request was successful.',
           'message' => $message,
           'data' => $data
        ], $statusCode);

    }

    protected function errorResponse($data, $message=null, $statusCode){

        return response()->json([
           'status' => 'Error has occurred ...',
           'message' => $message,
           'data' => $data
        ], $statusCode);

    }
}