<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //function to craete user
    public function createUser(Request $request)
    {
        //validation
        $validator = Validator::make($request->all(), [
            'name' => "required|string",
            'email' => "required|string",
            'phone' => "required|numeric",
            'password' => "required|min:6",
        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation Error Occured", 'error_message' => $validator->errors());
            return response()->json($result, 400); //bad request
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);
        if ($user->id) {
            $result = array('status' => true, 'message' => "User created", "data" => $user);
            $responsecode = 200;  //succesfull request
        } else {
            $result = array('status' => false, 'message' => "User not created", "data" => $user);
            $responsecode = 400;
        }

        return response()->json($result, $responsecode);
    }


    //function to get all user
    public function getUsers()
    {
        try {
            $users = User::all();

            $result = array('status' => true, 'message' => count($users) . "user(s) fetched", "data" => $users);
            $responsecode = 200;
            return response()->json($result, $responsecode);
        } catch (Exception $e) {
            $result = array('status' => false, 'message' => "Api failed due to an error", "error" => $e->getMessage());
            return response()->json($result, 500);
        }
    }


    public function getUserDetail($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => "User not found"], 404);
        }
        $result = array('status' => true, 'message' =>  "user found", "data" => $user);
        $responsecode = 200;
        return response()->json($result, $responsecode);
    }

    //function to update user
    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => "User not found"], 404);
        }
        //validation
        $validator = Validator::make($request->all(), [
            'name' => "required|string",
            'email' => "required|string",
            'phone' => "required|numeric|digits:10"

        ]);
        if ($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation Error Occured", 'error_message' => $validator->errors());
            return response()->json($result, 400); //bad request
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();

        $result = array('status' => true, 'message' => "user has been updated successfully", 'data' => $user);
        return response()->json($result, 200);
    }

    //delete user

    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => "User not found"], 404);
        }
        $user->delete();

        $result = array('status' => true, 'message' => "user has been deleted successfully");
        return response()->json($result, 200);
    }
}
