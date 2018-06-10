<?php


namespace Filehosting\Models;

use Illuminate\Database\Eloquent\Model;
use Filehosting\Models\File;

/**
 * Class Comment
 * @package Filehosting\Models
 */
class Comment extends Model
{

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['file_id', 'parent_id', 'author', 'comment_text'];

    /**
     * Generates matpath for a comment
     * If a comment has parent_id, makes it root comment
     * If not, makes it child comment
     */
    public function generateMatpath()
    {
        if ($this->parent_id == null) {
            $this->makeRoot();
        } else {
            $this->makeChild();
        }
    }

    /**
     * Generates root matpaht for a comment
     */
    protected function makeRoot() // void
    {
        $maxRootPath = intval($this->where('file_id', $this->file_id)->where('parent_id', null)->get()->max('matpath'));
        $path = $maxRootPath + 1;
        $this->matpath = $this->toPath($path);
    }

    /**
     * Generates child matpath for a comment
     */
    protected function makeChild() // void
    {
        $parentMatpath = $this->whereId($this->parent_id)->value('matpath');
        $childMaxPath = ($this->whereParentId($this->parent_id)->max('matpath'));
        if ($childMaxPath == null) {
            $this->matpath = "{$parentMatpath}.001";
        } else {
            $children = $this->countChildren($childMaxPath) + 1;
            $this->matpath = "{$parentMatpath}.{$this->toPath($children)}";
        }
    }

    /**
     * Returns depth of a comment
     * @return int
     */
    public function getDepth(): int
    {
        return count($this->getExplodedMatpath($this->matpath));
    }

    /**
     * Counts children of a comment
     * @param string $childMaxMatpath
     * @return int
     */
    private function countChildren(string $childMaxMatpath): int
    {
        $lastpart = $this->getExplodedMatpath($childMaxMatpath);
        if (count($lastpart) == 1) {
            return 0;
        } else {
            return intval(end($lastpart));
        }
    }

    /**
     * Returns matpath array
     * @param string $matpath
     * @return array
     */
    private function getExplodedMatpath(string $matpath): array
    {
        return explode('.', $matpath);

    }

    /**
     * Generates string in the matpath format - 000
     * @param int $int
     * @return string
     */
    private function toPath(int $int): string
    {
        return str_pad($int, 3, '0', STR_PAD_LEFT);
    }

}