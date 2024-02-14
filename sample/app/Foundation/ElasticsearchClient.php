<?php

namespace App\Foundation;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchClient
{
    protected $hosts = [];

    public function __construct(array $hosts)
    {
        $this->hosts = $hosts;
    }

    public function Client(): Client
    {
        return ClientBuilder::create()->setHosts($this->hosts)
            ->build();
    }
}
