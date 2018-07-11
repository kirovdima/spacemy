<?php

namespace App\Services;

use MongoDB\Driver\Manager;

class MongoDBManager
{
    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Manager(sprintf('mongodb://%s:%s/%s', config('database.connections.mongodb.host'), config('database.connections.mongodb.port'), config('database.connections.mongodb.database')));
        }

        return self::$instance;
    }
}
