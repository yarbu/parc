<?php

namespace Nodebox\Parc\Parser;

class LazyAltParserTest extends \Nodebox\Parc\TestCase
{
    public function testConstruct()
    {
        $parser = new LazyAltParser([]);
        $this->assertSame("LazyAltParser([])", $parser->description());

        $parser = new LazyAltParser(['a' => 'b']);
        $this->assertSame("LazyAltParser(['a' => 'b'])", $parser->description());

        $parser = new LazyAltParser([new StringParser('a'), new StringParser('b')]);
        $this->assertSame("LazyAltParser([StringParser('a'), StringParser('b')])", $parser->description());

        return $parser;
    }

    /**
     * @expectedException Exception
     */
    public function testConstructFail1()
    {
        $parser = new LazyAltParser([true]);
    }

    /**
     * @depends testConstruct
     */
    public function testAccept(LazyAltParser $parser)
    {
        $node = $parser->accept(' abc ', 2);

        $this->assertSame($parser, $node->parser());
        $this->assertSame(2, $node->from());
        $this->assertSame(1, $node->length());

        $this->assertSame('b', $node->result()->result());
    }

    /**
     * @depends testConstruct
     * @expectedException Nodebox\Parc\ParseException
     */
    public function testAcceptFail(LazyAltParser $parser)
    {
        $node = $parser->accept(' abc ', 3);
    }

    public function testEvalAcceptsEmpty()
    {
        $i = new StringParser('');

        $parser = new LazyAltParser([$i]);
        $this->assertSame(false, $this->invokeMethod($parser, 'evalAcceptsEmpty'));

        $this->setProperty($i, 'acceptsEmpty', true);

        $parser = new LazyAltParser([$i]);
        $this->assertSame(true, $this->invokeMethod($parser, 'evalAcceptsEmpty'));
    }

    public function testFirstSet()
    {
        $i = new StringParser('');

        $parser = new LazyAltParser([$i]);
        $this->assertSame([$i], $this->invokeMethod($parser, 'firstSet'));
    }
}
