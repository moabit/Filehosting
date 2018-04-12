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
}