<?php


namespace Filehosting\Models;

use Illuminate\Database\Eloquent\Model;
use Filehosting\Models\File;

class Comment extends Model
{

    public $timestamps = false;

    protected $fillable = ['file_id','parent_id', 'author', 'comment_text'];

    public function generateMatpath ()
    {
        if ($this->parent_id == null) {
            $this->makeRoot();
        } else {
            $this->makeChild();
        }
    }

    protected function makeRoot() // void
    {
        $maxRootPath = intval($this->where('file_id',$this->file_id)->where('parent_id', null)->get()->max('matpath'));
        $path = $maxRootPath +1;
        $this->matpath = $this->toPath($path);
    }

    protected function makeChild() // void
    {
        $parentMatpath = $this->whereId($this->parent_id)->value('matpath');
        $childMaxPath = ($this->whereParentId($this->parent_id)->max('matpath'));
        if ($childMaxPath == null) {
            $this->matpath = "{$parentMatpath}.001";
        } else {
            $children = $this->countChildren($childMaxPath) +1;
            $this->matpath = "{$parentMatpath}.{$this->toPath($children)}";
        }
    }

    public function getDepth () :int
    {
        return count ($this->getExplodedMatpath($this->matpath));
    }

    private function countChildren(string $childMaxMatpath): int
    {
        $lastpart = $this->getExplodedMatpath($childMaxMatpath);
        if (count($lastpart) == 1) {
            return 0;
        } else {
            return intval(end($lastpart));
        }
    }

    private function getExplodedMatpath(string $matpath): array
    {
        return explode('.', $matpath);

    }

    private function toPath(int $int): string
    {
        return str_pad($int, 3, '0', STR_PAD_LEFT);
    }

}