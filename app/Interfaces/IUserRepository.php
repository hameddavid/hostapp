<?php

namespace App\Interfaces;


interface IUserRepository{

    public function CreateUser(array $user);
    public function GetUsers();
    public function GetUserByEmail(string $email);
}