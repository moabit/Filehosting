<?php

namespace Filehosting\Traits;


trait MaterializedPathTrait
{
    public function makeRoot(): void
    {
        $maxRootPath = intval($this->where('file_id',$this->file_id)->where('parent_id', null)->get()->max ('matpath'));
        $path = $maxRootPath +1;
        $this->matpath = $this->toPath($path);
    }

    public function makeChild(): void
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

    public function countChildren(string $childMaxMatpath): int
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

    public function getDepth () :int
    {
        return count ($this->getExplodedMatpath($this->matpath));
    }




}