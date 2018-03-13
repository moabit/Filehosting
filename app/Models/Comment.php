<?php


namespace Filehosting\Models;

use Illuminate\Database\Eloquent\Model;
use Filehosting\Traits\MaterializedPathTrait;
use Filehosting\Models\File;

class Comment extends Model
{
    use MaterializedPathTrait;

    public $timestamps = false;

    public function getComments ()
    {
       return $this->whereFileId($this->file_id)->orderBy('matpath','asc')->get();
    }


}