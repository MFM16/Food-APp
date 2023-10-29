<?php

namespace App\Http\Controllers;

class ResponseFormater extends Controller
{
  protected static $response = [
    'meta' => [
      'code' => 200,
      'status' => 'success',
      'title' => null,
      'message' => null
    ],
    'data' => null
  ];

  public static function success($data = null, $message = null, $title = null)
  {
    self::$response['meta']['message'] = $message;
    self::$response['meta']['title'] = $title;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['meta']['code']);
  }

  public static function error($data = null, $message = null, $title = null, $code = 400)
  {
    self::$response['meta']['status'] = 'error';
    self::$response['meta']['code'] = $code;
    self::$response['meta']['message'] = $message;
    self::$response['meta']['title'] = $title;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['meta']['code']);
  }

}
