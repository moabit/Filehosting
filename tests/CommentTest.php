<?php


namespace Testsuite;

use PHPUnit\Framework\TestCase;
use Filehosting\Models\Comment;

class CommentTest extends Testcase
{
    public function testCountChildren()
    {
      //  $comment= new Comment ();
      // $this->assertEquals(5, $comment->countChildren("001.001.001.005"));
      //  $this->assertEquals(0, $comment->countChildren("001"));
    }

    public function testGetDepth()
    {
        $comment= new Comment ();
        $comment->matpath="001.001.001.001";
        $this->assertEquals(4, $comment->getDepth());
    }

    public function testGetExplodedMatpath ()
    {
        $testComment= new Comment ();
        $this->assertEquals(['001', '002', '003'], $testComment->getExplodedMatpath ('001.002.003'));
    }
}