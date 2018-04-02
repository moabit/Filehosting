<?php

namespace Filehosting\Helpers;

use \Illuminate\Database\Capsule\Manager as DB;

class SphinxSearch
{
    public function search ($match, $offset, $limit)
    {
        $search = DB::connection('sphinxSearch')
            ->select('SELECT id FROM index_files, rt_files
         WHERE MATCH(:match)
       ORDER BY id ASC LIMIT :offset, :limit', ['match' => $match, 'offset' => $offset, 'limit' => $limit]);
        return $this->toArray($search);
    }

    private function toArray ($array)
    {
        $output = [];
        foreach ($array as $object) {
            array_push($output, $object->id);
        }
        return $output;
    }

    public function countMatches ($match)
    {
        return DB::connection('sphinxSearch')->select('COUNT(*) FROM index_files, rt_files WHERE MATCH(:match)', ['match'=>$match]);
    }

    public function indexFile (int $fileId, string $fileOriginalName) // void
    {
        DB::connection('sphinxSearch')->select('INSERT INTO rt_files VALUES(:fileId, :fileOriginalName, 1)',
            ['fileId'=>$fileId,'fileOriginalName'=>$fileOriginalName]);
    }

    public function deleteIndexedFile (int $fileId):void
    {
        DB::connection('sphinxSearch')->select('DELETE FROM rt_files WHERE id = :fileId',['fileId'=>$fileId]);
    }
}