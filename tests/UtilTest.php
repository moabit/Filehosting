<?php

namespace Testsuite;

use PHPUnit\Framework\TestCase;
use Filehosting\Helpers\Util;
use Filehosting\Exceptions\ConfigException;

class UtilTest extends TestCase
{
    public function testJSON()
    {
        $this->assertEquals(['test' => 'test'], Util::readJSON(__DIR__ . '/resources/test.json'));
    }

    public function testReadJSONwithWrongPath()
    {
        $this->expectException(ConfigException::class);
        Util::readJSON('/wrongPath/');
    }

    public function testNormalizeFilename()
    {
        $filename = 'test.jpg';
        $this->assertEquals($filename, Util::normalizeFilename($filename));
        $filename = str_repeat('a', 160) . '.jpg';
        $normalizedFilename = Util::normalizeFilename($filename);
        $this->assertTrue(mb_strlen($normalizedFilename) == 150);
        $this->assertStringEndsWith('.jpg', $normalizedFilename);
    }

    public function testGenerateSafeFilename()
    {
        $filename = 'test.php';
        $this->assertEquals('test.txt', Util::generateSafeFilename($filename));
        $filename = 'тест.html';
        $this->assertEquals('test.txt', Util::generateSafeFilename($filename));
        $filename = 'test.jpg';
        $this->assertEquals('test.jpg', Util::generateSafeFilename($filename));
    }

}