<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Models\Pharmacist;
use App\Models\UserPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class RegisterController extends BaseController
{
    //
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|string',
            'email' => 'required|email|unique:pharmacists',
            'password' => 'required|min:8',
            'pharmacy_id' => ['required', 'int', 'exists:stores,id'],
            'card_number' => ['required', 'string', 'size:16'],
            'card_holder_name' => ['required', 'string', 'max:255'],
            'card_expiry_date' => ['required', 'string', 'size:5'],
            'card_cvv' => ['required', 'string', 'size:3'],
            'type' => 'required|in:user,admin,super-admin'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }


        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = Pharmacist::create($input);
        $success['token'] =  $user->createToken($request->userAgent())->plainTextToken;
        $success['name'] =  $user->name;

        $request['pharmacist_id'] = $user->id;
        UserPayment::create($request->all());

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken($request->userAgent())->plainTextToken;
            $success['type'] =  $user['type'];
            $success['name'] =  $user['name'];

            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
}
