<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    //
    protected $model;
    public function __construct(User $model)
    {
        $this->model = $model;
    }
    public function register(RegisterRequest $request)
    {

        $data = $request->validated();
        try{
            $data['password'] = Hash::make($data['password']);

            if($request->hasFile('image'))
            {
                $data['image'] = $this->uploadImage('uploads/users',$request->file('image'));
            }
            $user = $this->model->create(array_merge($data,['role_id'=>1]));
            return response()->json([
                'status' => true,
                'message' => 'Application Created',         
                'token' => $user->createToken("Student Token")->plainTextToken
                
            ], 200);

        }catch(Exception $e)
        {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function login(LoginRequest $request)
    {
        try{
            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Invaild Email or Password.',
                ], 401);
            }

            $user = $this->model->where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken,
                'data'=>$user,
                'role'=>$user->role->id
            ], 200);
        }catch(Exception $e)
        {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $accessToken = $request->bearerToken();
        $token = PersonalAccessToken::findToken($accessToken);
        $token->delete();
        return response()->json([
            'message'=>'Logout Successfuly',
            'status'=>true
        ], 200);
    }

    public function uploadImage($filePath,$image)
    {
        $imageName = time().'.'.$image->extension();  
        $path = $image->move(public_path($filePath), $imageName);
        $link = asset($filePath.'/'.$imageName);
        return $link;   
    }
}
