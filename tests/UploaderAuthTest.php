<?php

namespace Testsuite;

use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Filehosting\Auth\UploaderAuth;

class UploaderAuthTest extends TestCase
{

    public function testSetUploaderToken()
    {
        $res = new Response();
        $auth = new UploaderAuth();
        $res = $auth->setUploaderToken('test', $res, 1);
        $this->assertStringStartsWith('1=test', $res->getHeaderLine('Set-Cookie'));
        $this->assertInstanceOf('Slim\Http\Response', $res);
    }

    public function testDeleteUploaderToken()
    {
        $res = new Response();
        $auth = new UploaderAuth();
        $res = $auth->setUploaderToken('test', $res, 1);
        $res = $auth->deleteUploaderToken(1, $res);
        $this->assertFalse($res->hasHeader('Set-Cookie'));
        $this->assertInstanceOf('Slim\Http\Response', $res);
    }
}