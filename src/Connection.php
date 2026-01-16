<?php

namespace Pioneers\ClickHouse;

use ClickHouseDB\Client;

class Connection
{
    public Client $client;

    public function __construct(bool $withDatabase = true)
    {
        $config = config('clickhouse.connection');

        $this->client = new Client([
            'host' => $config['host'],
            'port' => $config['port'],
            'username' => $config['username'],
            'password' => $config['password'],
        ]);

        if ($withDatabase) {
            $this->client->database($config['database']);
        }
    }

    public static function client(): Client
    {
        return (new self)->client;
    }
}
