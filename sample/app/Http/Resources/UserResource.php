<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function sprintf;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['user_id'],
            'name' => $this->resource['user_name'],
            '_links' => [
                'self' => [
                    'href' => sprintf(
                        'https://example.com/users/%s',
                        $this->resource['user_id']
                    )
                ]
            ]
        ];
    }
}
