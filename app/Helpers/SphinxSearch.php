<?php

namespace Filehosting\Helpers;

use \Illuminate\Database\Capsule\Manager as DB;

/**
 * Class SphinxSearch
 * @package Filehosting\Helpers
 */
class SphinxSearch
{
    /**
     * @param $match
     * @param $offset
     * @param $limit
     * @return array
     */
    public function search($match, $offset, $limit): array
    {
        $search = DB::connection('sphinxSearch')
            ->select('SELECT id FROM index_files, rt_files
         WHERE MATCH(:match)
       ORDER BY id ASC LIMIT :offset, :limit', ['match' => $match, 'offset' => $offset, 'limit' => $limit]);
        return $this->toArray($search);
    }

    /**
     * @param string $match
     * @return int
     */
    public function countSearchResults(string $match): int
    {
        $count = DB::connection('sphinxSearch')->select('SELECT COUNT(*) FROM index_files, rt_files WHERE MATCH(:match)', ['match' => $match]);
        $count = json_decode(json_encode($count), True);
        return $count[0]["count(*)"];
    }

    /**
     * @param int $fileId
     * @param string $fileOriginalName
     */
    public function indexFile(int $fileId, string $fileOriginalName) // void
    {
        DB::connection('sphinxSearch')->insert('INSERT INTO rt_files (id, original_name) VALUES(:fileId, :fileOriginalName)',
            ['fileId' => $fileId, 'fileOriginalName' => $fileOriginalName]);
    }

    /**
     * @param int $fileId
     */
    public function deleteIndexedFile(int $fileId)
    {
        DB::connection('sphinxSearch')->delete('DELETE FROM rt_files WHERE id = :fileId', ['fileId' => $fileId]);
    }

    /**
     * @param array $objects
     * @return array
     */
    private function toArray(array $objects): array
    {
        $output = [];
        foreach ($objects as $object) {
            array_push($output, $object->id);
        }
        return $output;
    }
}