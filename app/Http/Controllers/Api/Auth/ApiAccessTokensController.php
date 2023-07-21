<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pharmacist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiAccessTokensController extends Controller
{

    public function store(Request $request){
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required|string|min:8',
                'device_name' => 'string|max:255'
            ]
        );

        $device_name = $request->post('device_name', $request->userAgent());
        $user = Pharmacist::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)){

            return response()->json([
                'token' => $user->createToken($device_name)->plainTextToken,
                'message' => 'Login Successful',
                'user' => $user
            ], 201);
        }else{
            return response()->json([
                'message' => 'Login Failed'
            ], 401);
        }

    }
}
