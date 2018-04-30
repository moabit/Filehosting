<?php


namespace Filehosting\Models;

use Illuminate\Database\Eloquent\Model;


class File extends Model

{
    public $timestamps = false;
    protected $fillable = ['original_name', 'safe_name', 'size', 'uploader_token', 'media_type'];

    public function comments()
    {
        return $this->hasMany('Filehosting\Models\Comment');
    }

    public function getSortedComments (): \Illuminate\Support\Collection
    {
        $comments=$this->comments()->orderBy('matpath')->get();
        return $comments;
    }

    public function countRootComments () : int
    {
        $comments=$this->comments()->where('parent_id', null)->get()->max ('matpath');
        return intval($comments);
    }

    public function isImage(): bool
    {
        $imageTypes = ['image/gif', 'image/jpeg', 'image/png', 'image/bmp'];
        if (in_array($this->media_type, $imageTypes)) {
            return true;
        }
        return false;
    }
    public function isAudio(): bool
    {
        $audioTypes = ['audio/mp3', 'audio/mpeg', 'audio/ogg', 'audio/wav'];
        if (in_array($this->media_type, $audioTypes)) {
            return true;
        }
        return false;
    }
    public function isVideo():bool
    {
        $audioTypes = ['video/mp4', 'video/webm', 'video/ogg'];
        if (in_array($this->media_type, $audioTypes)) {
            return true;
        }
        return false;
    }
}