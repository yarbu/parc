<?php

namespace Nodebox\Parc\Parser;

class StringParserTest extends \Nodebox\Parc\TestCase
{
    public function testConstruct()
    {
        $parser = new StringParser('needle');

        $this->assertSame('needle', $parser->needle());
        $this->assertSame("StringParser('needle')", $parser->description());

        return $parser;
    }

    /**
     * @depends testConstruct
     */
    public function testAccept(StringParser $parser)
    {
        $node = $parser->accept(' needle ', 1);

        $this->assertSame($parser, $node->parser());
        $this->assertSame(1, $node->from());
        $this->assertSame(6, $node->length());
        $this->assertSame('needle', $node->result());
    }

    /**
     * @depends testConstruct
     * @expectedException Nodebox\Parc\ParseException
     */
    public function testAcceptFail(StringParser $parser)
    {
        $node = $parser->accept(' needle ', 2);
    }

    public function testEvalAcceptsEmpty()
    {
        $parser = new StringParser('');
        $this->assertSame(true, $this->invokeMethod($parser, 'evalAcceptsEmpty'));

        $parser = new StringParser('needle');
        $this->assertSame(false, $this->invokeMethod($parser, 'evalAcceptsEmpty'));
    }
}
