<?php


namespace Filehosting\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model

{
    public $timestamps = false;
    protected $fillable = ['original_name', 'safe_name', 'size', 'uploader_token'];

    public function isImage()
    {
        $imageTypes = array(
            'image/gif',
            'image/jpeg',
            'image/png',
            'image/bmp'
        );
        if (in_array($this->mimetype, $imageTypes)) {
            return true;
        }
        return false;
    }
    public function isAudio()
    {
        $audioTypes = array(
            'audio/mp3',
            'audio/mpeg',
            'audio/ogg',
            'audio/wav'
        );
        if (in_array($this->mimetype, $audioTypes)) {
            return true;
        }
        return false;
    }
    public function isVideo()
    {
        $audioTypes = array(
            'video/mp4',
            'video/webm',
            'video/ogg'
        );
        if (in_array($this->mimetype, $audioTypes)) {
            return true;
        }
        return false;
    }

}