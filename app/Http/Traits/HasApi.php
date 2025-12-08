<?php
namespace App\Http\Traits;
use Illuminate\Http\Request;

trait HasApi{
    protected function failMessage($message='رکورد یافت نشد')
    {
        return response()->json(['success' => false, 'message' =>$message, 'data' => []]);
    }
    protected function validationToken(Request $request)
    {
        if ($request->header('token') == env('SAINAEX_TOKEN'))
            return true;
        else
            return false;
    }
}
