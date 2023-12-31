<?php

namespace App\Http\Controllers;

use App\Helpers\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ControllersService
{

    public static function generateResponseThrowable($responseArray, $statusCode)
    {
        return response()->json($responseArray, $statusCode);
    }


    public static function generateResponse($responseArray, $statusCode)
    {
        return response()->json($responseArray, $statusCode);
    }

    public static function generateProcessResponse($processStatus, $processCode = null, $statusCode = null, $details = null, $info = null)
    {

        if ($details == null) {
            $responseArray = array("status" => $processStatus, "message" => Messages::getMessage($processCode), 'code' => $statusCode);
        } else {
            $responseArray = array("status" => $processStatus, "message" => Messages::getMessage($processCode), 'code' => $statusCode, 'details' => $details, 'info' => $info);
        }

        if ($statusCode == null) {
            $statusCode = $processStatus ? 200 : 400;
        }

        return self::generateResponse($responseArray, $statusCode);
    }

    public static function generateObjectSuccessResponse($model, $message, $key = "object", $modal, $i = 'object')
    {
        return response()->json(array(
            'status' => true,
            'message' => $message,
            $model => $key,
            $modal => $i
        ), 200);
    }

    public static function generateObjectSuccessResponses($model, $message, $key = "object")
    {
        return response()->json(array(
            'status' => true,
            'message' => $message,
            $key => $model
        ), 200);
    }

    public static function generateArraySuccessResponse($objectsArray, $message)
    {
        return response()->json(array(
            'status' => true,
            'message' => $message,
            'list' => $objectsArray
        ), 200);
    }

    public static function generateValidationErrorMessage($message, $code = 400)
    {
        return response()->json(array(
            'status' => false,
            'code' => $code,
            'message' => $message,
        ), $code);
    }

    public static function getImage(Request $request, $fileName, $folder, $key = 'image')
    {
        if ($request->hasFile($key)) {
            $imageName = $request->file($key)->getClientOriginalExtension();
            $imageName = $fileName . '_' . time() . '.' . $imageName;

            if (App::environment('local')) {
                $request->file($key)->move(public_path('uploads/' . $folder . '/images'), $imageName);
            } else {
                $request->file($key)->move(base_path('www/uploads/' . $folder . '/images'), $imageName);
            }
            return URL::to('/uploads/' . $folder . '/images') . '/' . $imageName;
        }
    }

    public static function isApiRoute(Request $request)
    {
        $route = $request->route()->getPrefix();
        if (str_contains($route, 'api')) {
            return true;
        } else {
            return false;
        }
    }

    public static function generateRandomNumber()
    {
        $number = mt_rand(100000, 999999);
        return $number;
    }

    public static function debug_to_console($data)
    {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);

        echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
    }

    protected static function getClientIp(): string
    {
        $ip = \request()->ip();
        return $ip == '127.0.0.1' ? '66.102.0.0' : $ip;
    }

    public static function checkPermission($permission, $auth): bool
    {

        if (auth($auth)->check()) {
            if (auth($auth)->user()->hasPermissionTo($permission)) {
                return true;
            }
        }
        return false;
    }

    public static function getMessage($processCode)
    {
        return Messages::getMessage($processCode);
    }

    public static function responseSuccess($responseArray, $statusCode = 200)
    {
        return response()->json($responseArray, $statusCode);
    }
    public static function responseErorr($responseArray, $statusCode = 200)
    {
        return response()->json($responseArray, $statusCode);
    }
}
