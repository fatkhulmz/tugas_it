<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\Models\User;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except'=>['login','register']]);    
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name_user' => 'required',
            'username'  => 'required',
            'password'  => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name_user' => $request->name_user,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'address'   => $request->address,
            'birthdate' => $request->birthdate,
            'number_phone'  => $request->number_phone
        ]);

        return response()->json([
            'message'=> 'User suceessfully registered',
            'user'  => $user
        ], 201) ;
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(),[
            'username'  => 'required',
            'password'  => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        if(!$token=auth()->attempt($validator->validated())){
            return response()->json(['error'=>'Unauthorized'],401);
        }

        return $this->createNewToken($token);
    }

    public function createNewToken($token){
        return response()->json([
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => auth()->factory()->getTTL()*60,
            'user'  => auth()->user()
        ]);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message'=> 'User logout'
        ]) ;
    }

}
