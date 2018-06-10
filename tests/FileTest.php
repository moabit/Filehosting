<?php


namespace Testsuite;

use PHPUnit\Framework\TestCase;
use Filehosting\Models\File;

class FileTest extends TestCase
{
    public function testIsImage ()
    {
        $file=new File (['media_type' => 'image/jpeg']);
        $this->assertTrue($file->isImage());
        $file->media_type='application/pdf';
        $this->assertFalse($file->isImage());
    }

    public function testIsAudio ()
    {
        $file=new File (['media_type' => 'audio/mp3']);
        $this->assertTrue($file->isAudio());
        $file->media_type='application/pdf';
        $this->assertFalse($file->isAudio());
    }

    public function testIsVideo ()
    {
        $file=new File (['media_type' => 'video/webm']);
        $this->assertTrue($file->isVideo());
        $file->media_type='application/pdf';
        $this->assertFalse($file->isVideo());
    }
}