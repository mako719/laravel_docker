<?php

namespace App\DataProvider;

use App\Domain\Entity\Publisher;

interface PublisherRepositoryInterface
{
    public function findByName(string $name): ?Publisher;

    public function store(Publisher $publisher): int;
}
