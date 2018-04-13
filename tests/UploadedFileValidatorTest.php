<?php

namespace Testsuite;
use PHPUnit\Framework\TestCase;
use Filehosting\Validators\UploadedFileValidator;
use Slim\Http\UploadedFile;
use FIlehosting\Exceptions\FileUploadException;

class UploadedFileValidatorTest extends TestCase
{
    protected $validator;

    protected function setUp()
    {
        $this->validator = new UploadedFileValidator(10000);
    }

    public function testValidationSuccess ()
    {

    }

    public function testValidationFail ()
    {
        $badFile = new UploadedFile("/resources/storage", "test.jpg", "image/jpeg", 10000, UPLOAD_ERR_NO_FILE);
        $this->expectException(FileUploadException::class);
        $this->validator->validate($badFile);
    }
}