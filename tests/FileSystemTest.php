<?php

namespace Testsuite;

use PHPUnit\Framework\TestCase;
use Filehosting\Helpers\FileSystem;
use Filehosting\Models\File;
use Filehosting\Exceptions\FileSystemException;

class FileSystemTest extends TestCase
{
    protected $fileSystem;

    protected function setUp()
    {
        $this->fileSystem = new Filesystem(__DIR__ . "/resources");
    }

    public function testGetAbsolutePathToFile()
    {
        $expectedPath = __DIR__ . '/resources/storage/28-Apr-2018/1_test.jpg';
        $file = new File (['safe_name' => 'test.jpg']);
        $file->id = 1;
        $file->uploaded = '2018-04-28 19:48:18.183528';
        $this->assertEquals($expectedPath, $this->fileSystem->getAbsolutePathToFile($file));
    }

    public function testGetAbsolutePathToFileWithBadFile()
    {
        $file = new File (['safe_name' => 'file_does_not_exist.jpg']);
        $file->id = 1;
        $file->uploaded = '2018-04-28 19:48:18.183528';
        $this->expectException(FileSystemException::class);
        $this->fileSystem->getAbsolutePathToFile($file);

    }

    public function testGetPathToThumbnail()
    {
        $expectedPath = '/thumbnails/28-Apr-2018/1_test.jpg';
        $file = new File (['safe_name' => 'test.jpg', 'media_type' => 'image/jpeg']);
        $file->id = 1;
        $file->uploaded = '2018-04-28 19:48:18.183528';
        $this->assertEquals($expectedPath, $this->fileSystem->getPathToThumbnail($file));
    }

}