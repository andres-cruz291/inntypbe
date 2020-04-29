<?php

namespace App\Http\Controllers;

use Dotenv\Validator;
use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function store(Request $request){
        if($request['name'] && $request['email'] && $request['password'] && $request['password_confirm']){
            if($request['password'] == $request['password_confirm']){
                $user = new User();
                $user->name = $request['name'];
                $user->email = $request['email'];
                $user->password = $request['password'];
                $user->type = $request['type'];
                $user->save();
                return response()->json($user, 201);
            }else{
                return response()->json([ "error" => "Error creating the user: the passwords do not match" ], 501);
            }
        }else{
            return response()->json([ "error" => "Error creating the user: not all values sended" ], 501);
        }
    }

    public function show($user){
        if($user){
            $user = User::findOrFail($user);
        }
        if(!$user){
            $user = new User();
        }
        return response()->json($user, 200);
    }
}
