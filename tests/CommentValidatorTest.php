<?php
namespace Testsuite;
use PHPUnit\Framework\TestCase;
use Filehosting\Validators\CommentValidator;
use Filehosting\Models\Comment;

class CommentValidatorTest extends TestCase
{
    protected $validator;

    protected function setUp()
    {
        $this->validator = new CommentValidator();
    }

    public function testValidationSuccess ()
    {

    }

    public function testValidationFail ()
    {
    }
}