<?php

namespace Nodebox\Parc\Parser;

class EmptyParserTest extends \Nodebox\Parc\TestCase
{
    public function testConstruct()
    {
        $parser = new EmptyParser();

        $this->assertSame('EmptyParser()', $parser->description());

        $this->invokeMethod($parser, 'setName', array('empty'));
        $this->assertSame('empty', $parser->name());

        return $parser;
    }

    /**
     * @depends testConstruct
     */
    public function testAccept(EmptyParser $parser)
    {
        $node = $parser->accept('abcd', 2);

        $this->assertSame($parser, $node->parser());
        $this->assertSame(2, $node->from());
        $this->assertSame(0, $node->length());
        $this->assertSame(null, $node->result());
    }

    public function testEvalAcceptsEmpty()
    {
        $parser = new EmptyParser();
        $this->assertSame(true, $this->invokeMethod($parser, 'evalAcceptsEmpty'));
    }
}
