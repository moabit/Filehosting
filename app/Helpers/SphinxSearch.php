<?php

namespace Filehosting\Helpers;

use \Illuminate\Database\Capsule\Manager as DB;

class SphinxSearch
{
    public function search ($match, $offset, $limit):array
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

    public function indexFile (int $fileId, string $fileOriginalName) // void
    {
        DB::connection('sphinxSearch')->insert('INSERT INTO rt_files (id, original_name) VALUES(:fileId, :fileOriginalName)',
            ['fileId'=>$fileId,'fileOriginalName'=>$fileOriginalName]);
    }

    public function deleteIndexedFile (int $fileId) // void
    {
        DB::connection('sphinxSearch')->delete('DELETE FROM rt_files WHERE id = :fileId',['fileId'=>$fileId]);
    }
}