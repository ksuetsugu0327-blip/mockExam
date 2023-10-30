<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ResetPasswordMailController extends Controller
{
    public function resetPassword(Request $request) 
{
    $validator = Validator::make($request->all(), [
          'email' => 'required|email|unique:subscribers'
    ]);

    if ($validator->fails()) {
        return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
    }  

    // $email = $request->all()['email'];
    //     $subscriber = Subscriber::create([
    //         'email' => $email
    //     ]
    // ); 
        $email='hin0511.helpnservice@gmail.com';
    //if ($subscriber) {
        Mail::to($email)->send(new ResetPasswordMail($email));
        return new JsonResponse(
            [
                'success' => true, 
                'message' => "Thank you for subscribing to our email, please check your inbox"
            ], 
            200
        );
    //}
}
}
