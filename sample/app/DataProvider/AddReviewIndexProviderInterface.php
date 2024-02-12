<?php

namespace App\DataProvider;

interface AddReviewIndexProviderInterface
{
    public function add(
        int $id,
        string $title,
        string $content,
        string $epoch,
        array $tags,
        int $userId
    ): array;
}
