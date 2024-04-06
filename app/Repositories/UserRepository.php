<?php

namespace App\Repositories;

use App\Interfaces\IUserRepository;
use Illuminate\Support\Facades\Hash;
use App\HelperClass\Helper1;
use App\Models\User;
use App\Traits\HttpResponses;

class UserRepository implements IUserRepository{

    use HttpResponses;

    public function CreateUser(array $user)
    {
        $name = explode('@', $user['email']);
        $password = '@@11223344'; //Helper1::generateSixRandomCharacter();
        $newUser = new User();
        $newUser->name = $name[0];
        $newUser->email = $user['email'];
        $newUser->password = Hash::make($password);
        $newUser->save();
        if($newUser){
            return ['user' => $newUser, 'status' => "OK"];
        }
     
         return ['user' => "", 'status' => "NOK"];
    }

    public function GetUsers()
    {
        
    }

    public function GetUserByEmail($_email)
    {
        return User::where('email', $_email)->first();
    }
}