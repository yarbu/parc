<?php

namespace Nodebox\Parc\Parser;

class RegexpParserTest extends \Nodebox\Parc\TestCase
{
    public function testConstruct()
    {
        $parser = new RegexpParser('/^ab[c|d]/');

        $this->assertSame('/^ab[c|d]/', $parser->regexp());
        $this->assertSame("RegexpParser('/^ab[c|d]/')", $parser->description());

        return $parser;
    }

    /**
     * @expectedException Exception
     */
    public function testConstructFail1()
    {
        $parser = new RegexpParser('$/no regexp/');
    }

    /**
     * @expectedException Exception
     */
    public function testConstructFail2()
    {
        $parser = new RegexpParser('/no anchor/');
    }

    /**
     * @depends testConstruct
     */
    public function testAccept1(RegexpParser $parser)
    {
        $node = $parser->accept(' abc ', 1);

        $this->assertSame($parser, $node->parser());
        $this->assertSame(1, $node->from());
        $this->assertSame(3, $node->length());
        $this->assertSame('abc', $node->result());
    }

    /**
     * @depends testConstruct
     */
    public function testAccept2(RegexpParser $parser)
    {
        $node = $parser->accept(' abd ', 1);

        $this->assertSame($parser, $node->parser());
        $this->assertSame(1, $node->from());
        $this->assertSame(3, $node->length());
        $this->assertSame('abd', $node->result());
    }

    /**
     * @depends testConstruct
     * @expectedException Nodebox\Parc\ParseException
     */
    public function testAcceptFail1(RegexpParser $parser)
    {
        $node = $parser->accept(' abe ', 1);
    }

    /**
     * @depends testConstruct
     * @expectedException Nodebox\Parc\ParseException
     */
    public function testAcceptFail2(RegexpParser $parser)
    {
        $node = $parser->accept(' abc ', 0);
    }

    public function testEvalAcceptsEmpty()
    {
        $parser = new RegexpParser('/^(abc|.*)/');
        $this->assertSame(true, $this->invokeMethod($parser, 'evalAcceptsEmpty'));

        $parser = new RegexpParser('/^abc/');
        $this->assertSame(false, $this->invokeMethod($parser, 'evalAcceptsEmpty'));
    }
}
