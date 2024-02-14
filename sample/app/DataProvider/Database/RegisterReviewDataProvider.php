<?php

namespace App\DataProvider\Database;

use App\DataProvider\RegisterReviewProviderInterface;

class RegisterReviewDataProvider implements RegisterReviewProviderInterface
{
    public function save(
        string $title,
        string $content,
        int $userId,
        string $createdAt,
        array $tags = []
    ): int {
        $reviewId = $this->createReview($title, $content, $userId, $createdAt);
        foreach ($tags as $tag) {
            $this->createReviewTag(
                $reviewId,
                $this->createTag($tag, $createdAt),
                $createdAt
            );
        }
        return $reviewId;
    }

    protected function createTag(string $name, $createdAt): int
    {
        $result = Tag::firstOrCreate([
            'tag_name' => $name
        ], [
            'created_at' => $createdAt,
        ]);
        return $result->id;
    }

    protected function createReview(
        string $title,
        string $content,
        int $userId,
        string $createdAt
    ): int {
        $result = Review::firstOrCreate([
            'user_id' => $userId,
            'title' => $title,
        ], [
            'content' => $content,
            'created_at' => $createdAt,
        ]);
        return  $result->id;
    }

    protected function createReviewTag(int $reviewId, int $tagId, string $createdAt)
    {
        ReviewTag::firstOrCreate([
            'tag_id' => $tagId,
            'review_id' => $reviewId,
        ], [
            'created_at' => $createdAt,
        ]);
    }
}
