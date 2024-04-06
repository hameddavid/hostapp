<?php

namespace App\GraphQL\Mutations;

class CustomerMutation{

    public function login(null $_, array $args){
        info($args);
        return "Is working ....";
    }
}