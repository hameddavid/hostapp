<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Mail\SendToken;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request){
       $request->validated($request->all());
       
       if(!Auth::attempt($request->only(['email', 'password']))){
        return $this->error('', 'Credentials do not match', 401);
       }
       $user = User::where('email',$request->email)->first();
       return $this->success([
            'user' => $user,
            'token' => $user->createToken('Api Token of' . $user->name)->plainTextToken
       ]);
    }


    public function register(StoreUserRequest $request){
        $request->validated($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('Api Token of'. $user->name)->plainTextToken
        ]);
        
    }

    public function restePassword(Request $request) {

        $request->validate([
            'email' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
            'token' => 'required'
        ]);
        [$email, $password, $token] = $request;
        if ($token) { // check if token has not expired and belongs to the user:: to be implemented 
            User::where('email', $email)->update(
                ['password' => Hash::make($password)]
            );
        }
    }

    public function forgotPassword(Request $request) {
        // TODO:: email config setup
        $request->validate([
            'email' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();
        if($user) {
            // put on a queue after queue is setup
            Mail::to($request->email)->send(new SendToken(
                token: generateSixRandomCharacter(),
                tokenType: 'forgotPassword' // switch to enum later or reuseable consts
            ));
        }
    }

    public function logout(){

        Auth::user()->currentAccessToken()->delete();
        
        return $this->success([
            'message' => 'Logged out successfully'
        ]);
    }
    
}
