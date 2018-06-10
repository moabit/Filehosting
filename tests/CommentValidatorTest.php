<?php
namespace Testsuite;
use PHPUnit\Framework\TestCase;
use Filehosting\Validators\CommentValidator;
use Filehosting\Models\Comment;
use Filehosting\Exceptions\CommentAdditionException;
use Filehosting\Helpers\Util;

class CommentValidatorTest extends TestCase
{
    protected $validator;

    protected function setUp()
    {
        $this->validator = new CommentValidator();
    }

    public function testValidationSuccess ()
    {
        $comment=new Comment (['file_id'=> 42,
            'parent_id'=>null,
            'author'=> 'Анонимус',
            'text'=> 'Это тестовый коммент!',
            ]);
        $comment->matpath="001";
        $errors=$this->validator->validate($comment);
        $this->assertEmpty($errors);
    }

    public function testValidationWithBadUserInput ()
    {
        $comment=new Comment (['file_id'=> 42,
            'parent_id'=>null,
            'author'=> 'А',
            'text'=>Util::generateToken(1000)
        ]);
        $comment->matpath='001';
        $this->assertCount(2, $this->validator->validate ($comment));
    }

    public function testValidationwithBadParentId ()
    {
        $comment=new Comment (['file_id'=> 42,
            'parent_id'=>'test',
            'author'=> 'Анонимус',
            'text'=> 'Это тестовый коммент!',
        ]);
        $comment->matpath='001';
        $this->expectException(CommentAdditionException::class);
        $this->validator->validate($comment);
    }

    public function testValidationWithBadMatpath ()
    {
        $comment=new Comment (['file_id'=> 42,
            'parent_id'=>null,
            'author'=> 'Анонимус',
            'text'=> 'Это тестовый коммент!',
        ]);
        $comment->matpaht="Это неправильный формат matpath!";
        $this->expectException(CommentAdditionException::class);
        $this->validator->validate($comment);
    }
}