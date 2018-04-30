<?php


namespace Testsuite;

use PHPUnit\Framework\TestCase;
use Filehosting\Models\Comment;

class CommentTest extends Testcase
{
    public function testGetDepth()
    {
        $comment= new Comment ();
        $comment->matpath="001.001.001.001";
        $this->assertEquals(4, $comment->getDepth());
    }
}