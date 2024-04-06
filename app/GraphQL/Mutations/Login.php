<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

final readonly class Login
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        info($_);
        // TODO implement the resolver
        return "Hello";
    }
}
