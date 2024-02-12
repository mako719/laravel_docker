<?php

namespace App\DataProvider\Elasticsearch;

use App\DataProvider\AddReviewIndexProviderInterface;
use App\Foundation\ElasticsearchClient;

use function sprintf;
use function array_map;

class AddReviewIndexDataProvider implements AddReviewIndexProviderInterface
{
    private $client;

    public function __construct(ElasticsearchClient $client)
    {
        $this->client = $client;
    }

    public function add(
        int $id,
        string $title,
        string $content,
        string $epoch,
        array $tags,
        int $userId
    ): array {
        $params = [
            'index' => 'reviews',
            'id' => sprintf('review:%s', $id),
            'body' => [
                'review_id' => $id,
                'title' => $title,
                'content' => $content,
                'tags' => array_map(function (string $value) {
                    return ['tag_name' => $value];
                }, $tags),
                'user_id' => $userId,
                'craeted_at' => $epoch
            ]
        ];

        return $this->client->client()
            ->index($params);
    }
}
