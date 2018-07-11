<?php

namespace App\MongoModels;

use App\Services\MongoDBManager;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Exception\Exception;

class AbstractMongoModel extends Eloquent
{
    protected $connection = 'mongodb';

    protected static $collection_name = null;

    private static $bulkWrite;

    private static $result;

    private static function getCollectionName()
    {
        if (!static::$collection_name) {
            throw new \Exception("Property 'collection_name' is empty!");
        }

        return static::$collection_name;
    }

    private static function getBulkWrite()
    {
        if (!self::$bulkWrite) {
            self::$bulkWrite = new BulkWrite(['ordered' => false]);
        }

        return self::$bulkWrite;
    }

    public static function batchUpdate($filter, $newObj, array $updateOptions)
    {
        self::getBulkWrite()->update($filter, $newObj, $updateOptions);
    }

    public static function execute()
    {
        try {
            $namespace = sprintf('%s.%s', config('database.connections.mongodb.database'), self::getCollectionName());
            self::$result = MongoDBManager::getInstance()->executeBulkWrite($namespace, self::getBulkWrite(), new WriteConcern(1));
            self::$bulkWrite = null;
        } catch (BulkWriteException $e) {
            self::$result = $e->getWriteResult();

            // Check if the write concern could not be fulfilled
            if ($writeConcernError = self::$result->getWriteConcernError()) {
                printf("%s (%d): %s\n",
                    $writeConcernError->getMessage(),
                    $writeConcernError->getCode(),
                    var_export($writeConcernError->getInfo(), true)
                );
            }

            // Check if any write operations did not complete at all
            foreach (self::$result->getWriteErrors() as $writeError) {
                printf("Operation#%d: %s (%d)\n",
                    $writeError->getIndex(),
                    $writeError->getMessage(),
                    $writeError->getCode()
                );
            }
        } catch (Exception $e) {
            printf("Other error: %s\n", $e->getMessage());
            exit;
        }
    }
}
