<?php

namespace App\DataProvider;

interface RegisterReviewProviderInterface
{
    public function save(
        string $title,
        string $content,
        int $userId,
        string $createdAt,
        array $tags = []
    ): int;
}
