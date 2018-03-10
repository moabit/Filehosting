<?php


namespace Filehosting\Models;

use Illuminate\Database\Eloquent\Model;
use Filehosting\Traits\MaterializedPathTrait;

class Comment extends Model
{
    use MaterializedPathTrait;

    public $timestamps = false;

}