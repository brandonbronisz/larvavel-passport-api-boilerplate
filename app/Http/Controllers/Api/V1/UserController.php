<?php namespace App\Http\Controllers\Api\V1;

use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;

class UserController extends Controller
{

    public function index()
    {
        return new UserResource(User::all());
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email'      => 'required|email|unique:users',
            'first_name' => 'required',
            'password'   => 'required|min:8'
        ]);

        if ($validator->fails()) {

            // Get the first error key
            $failed = $validator->failed();
            reset($failed);
            $failed = key($failed);

            return response()->json([
                'errors' => [
                    [
                        'status' => 422,
                        'title'  => 'Incorrect Validation',
                        'detail' => $validator->errors()->first(),
                        'source' => [
                            'pointer' => "data/attributes/$failed"
                        ]
                    ]
                ]
            ], 422);
        }

        return new UserResource(User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => password_hash($request->password, PASSWORD_DEFAULT)
        ]));

    }
}