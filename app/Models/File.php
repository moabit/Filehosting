<?php


namespace Filehosting\Models;

use Illuminate\Database\Eloquent\Model;


class File extends Model

{
    public $timestamps = false;
    protected $fillable = ['original_name', 'safe_name', 'size', 'uploader_token'];

    public function comments()
    {
        return $this->hasMany('Filehosting\Models\Comment');
    }

    public function getSortedComments ()
    {
        $comments=$this->comments()->orderBy('matpath')->get();
        return $comments;
    }
}