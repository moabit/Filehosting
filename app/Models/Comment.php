<?php


namespace Filehosting\Models;

use Illuminate\Database\Eloquent\Model;
use Filehosting\Traits\MaterializedPathTrait;
use Filehosting\Models\File;

class Comment extends Model
{
    use MaterializedPathTrait;

    public $timestamps = false;

    protected $fillable = ['file_id','parent_id', 'author', 'text'];


}