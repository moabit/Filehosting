<?php


namespace Filehosting\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class File
 * @package Filehosting\Models
 */
class File extends Model

{
    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var array
     */
    protected $fillable = ['original_name', 'safe_name', 'size', 'uploader_token', 'media_type'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('Filehosting\Models\Comment');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getSortedComments(): \Illuminate\Support\Collection
    {
        $comments = $this->comments()->orderBy('matpath')->get();
        return $comments;
    }

    /**
     * @return int
     */
    public function countRootComments(): int
    {
        $comments = $this->comments()->where('parent_id', null)->get()->max('matpath');
        return intval($comments);
    }

    /**
     * @return bool
     */
    public function isImage(): bool
    {
        $imageTypes = ['image/gif', 'image/jpeg', 'image/png', 'image/bmp'];
        if (in_array($this->media_type, $imageTypes)) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isAudio(): bool
    {
        $audioTypes = ['audio/mp3', 'audio/mpeg', 'audio/ogg', 'audio/wav'];
        if (in_array($this->media_type, $audioTypes)) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isVideo(): bool
    {
        $audioTypes = ['video/mp4', 'video/webm', 'video/ogg'];
        if (in_array($this->media_type, $audioTypes)) {
            return true;
        }
        return false;
    }
}