<?php

namespace UserAuth\Models;

use Phalcon\Mvc\Model;

/**
 * BaseModel for all model classes
 * Class BaseModel
 * @package UserAuth\Models
 */
class BaseModel extends Model
{
    const CHUNK_SIZE = 50000;

    /**
     * Get all data in a table without fetching all at once
     * @param null $chunkSize
     * @return array
     */

    public function fetchAllTableDataByChunks($chunkSize = null)
    {
        $data = [];

        $start = 0;
        $limit = is_null($chunkSize) || !is_int($chunkSize) || $chunkSize <=0 ? self::CHUNK_SIZE : (int) $chunkSize;

        do {
            $someData = $this->query()
                ->limit($limit, $start)
                ->execute()
                ->toArray();

            if (!empty($someData)) {
                foreach ($someData as $row) {
                    $data[] = $row;
                }
            }

            $start = $start + $limit;
        } while (!empty($someData));

        return $data;

    }
}