<?php

namespace Testsuite;

use PHPUnit\Framework\TestCase;
use Filehosting\Validators\UploadedFileValidator;
use Slim\Http\UploadedFile;
use Filehosting\Exceptions\FileUploadException;

class UploadedFileValidatorTest extends TestCase
{
    protected $validator;

    protected function setUp()
    {
        $this->validator = new UploadedFileValidator(10000);
    }

    public function testValidationSuccess()
    {
        $file = new UploadedFile("/resources/storage", "test.jpg", "image/jpeg", 1000, UPLOAD_ERR_OK);
        $this->assertEmpty($this->validator->validate($file));
    }

    public function testValidationWithError()
    {
        $badFile = new UploadedFile("/resources/storage", "test.jpg", "image/jpeg", 10000, UPLOAD_ERR_NO_FILE);
        $this->expectException(FileUploadException::class);
        $this->validator->validate($badFile);
    }

    public function testValidationWithExceededSizeLimit()
    {
        $badFile = new UploadedFile("/resources/storage", "test.jpg", "image/jpeg", 20000, UPLOAD_ERR_OK);
        $this->expectException(FileUploadException::class);
        $this->validator->validate($badFile);
    }
}