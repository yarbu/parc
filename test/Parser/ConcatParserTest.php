<?php

namespace Nodebox\Parc\Parser;

class ConcatParserTest extends \Nodebox\Parc\TestCase
{
    public function testConstruct()
    {
        $parser = new ConcatParser([]);
        $this->assertSame("ConcatParser([])", $parser->description());

        $parser = new ConcatParser(['a' => 'b']);
        $this->assertSame("ConcatParser(['a' => 'b'])", $parser->description());

        $parser = new ConcatParser([new StringParser('a'), new StringParser('b')]);
        $this->assertSame("ConcatParser([StringParser('a'), StringParser('b')])", $parser->description());

        return $parser;
    }

    /**
     * @expectedException Exception
     */
    public function testConstructFail1()
    {
        $parser = new ConcatParser([true]);
    }

    /**
     * @depends testConstruct
     */
    public function testAccept(ConcatParser $parser)
    {
        $node = $parser->accept(' ab ', 1);

        $this->assertSame($parser, $node->parser());
        $this->assertSame(1, $node->from());
        $this->assertSame(2, $node->length());

        $result = $node->result();
        $this->assertSame('a', $result[0]->result());
        $this->assertSame('b', $result[1]->result());
    }

    /**
     * @depends testConstruct
     * @expectedException Nodebox\Parc\ParseException
     */
    public function testAcceptFail(ConcatParser $parser)
    {
        $node = $parser->accept(' acb ', 1);
    }

    public function testEvalAcceptsEmpty()
    {
        $parser = new ConcatParser([]);
        $this->assertSame(true, $this->invokeMethod($parser, 'evalAcceptsEmpty'));

        $parser = new ConcatParser([new StringParser('needle')]);
        $this->assertSame(false, $this->invokeMethod($parser, 'evalAcceptsEmpty'));
    }

    public function testFirstSet()
    {
        $p1 = new ConcatParser([]);
        $this->assertSame([], $this->invokeMethod($p1, 'firstSet'));

        $p2 = new ConcatParser([$p1]);
        $this->assertSame([$p1], $this->invokeMethod($p2, 'firstSet'));
    }
}
