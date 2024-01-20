<?php

namespace App\Service;

use App\DataProvider\Eloquent\publisher;

class publisherService
{
    public function exists(string $name): bool
    {
        $count = Publisher::whereName($name)->count();
        if ($count > 0) {
            return true;
        }
        return false;
    }

    public function store(string $name, string $address): int
    {
        $publisher = Publisher::create(
            [
                'name' => $name,
                'address' => $address,
            ]
        );
        return (int)$publisher->id;
    }
}
