<?php

namespace Nodebox\Parc\Parser;

class GreedyMultiParserTest extends \Nodebox\Parc\TestCase
{
    public function testConstruct()
    {
        $parser = new GreedyMultiParser('i', 1);
        $this->assertSame(1, $parser->lower());
        $this->assertSame(null, $parser->optional());
        $this->assertSame("GreedyMultiParser('i', 1, NULL)", $parser->description());

        $parser = new GreedyMultiParser('i', 1, 3);
        $this->assertSame(1, $parser->lower());
        $this->assertSame(3, $parser->optional());
        $this->assertSame("GreedyMultiParser('i', 1, 3)", $parser->description());

        return $parser;
    }

    public function testAccept()
    {
        $i = new StringParser('a');

        $p1 = new GreedyMultiParser($i);

        $node = $p1->accept(' ba ', 1);
        $this->assertSame(1, $node->from());
        $this->assertSame(0, $node->length());
        $this->assertSame([], $node->result());

        $node = $p1->accept(' aa ', 1);
        $this->assertSame(1, $node->from());
        $this->assertSame(2, $node->length());

        $result = $node->result();
        $this->assertSame(2, count($result));

        $this->assertSame(1, $result[0]->from());
        $this->assertSame(1, $result[0]->length());

        $this->assertSame(2, $result[1]->from());
        $this->assertSame(1, $result[1]->length());

        $p2 = new GreedyMultiParser($i, 1);

        $node = $p2->accept(' aa ', 1);
        $this->assertSame(1, $node->from());
        $this->assertSame(2, $node->length());

        $result = $node->result();
        $this->assertSame(2, count($result));

        $this->assertSame(1, $result[0]->from());
        $this->assertSame(1, $result[0]->length());

        $this->assertSame(2, $result[1]->from());
        $this->assertSame(1, $result[1]->length());

        $p3 = new GreedyMultiParser($i, 0, 1);

        $node = $p3->accept(' ba ', 1);
        $this->assertSame(1, $node->from());
        $this->assertSame(0, $node->length());
        $this->assertSame([], $node->result());

        $node = $p3->accept(' aa ', 1);
        $this->assertSame(1, $node->from());
        $this->assertSame(1, $node->length());

        $result = $node->result();
        $this->assertSame(1, count($result));

        $this->assertSame(1, $result[0]->from());
        $this->assertSame(1, $result[0]->length());
    }

    /**
     * @expectedException Nodebox\Parc\ParseException
     */
    public function testAcceptFail1()
    {
        $i = new StringParser('a');
        $p1 = new GreedyMultiParser($i, 1);
        $node = $p1->accept(' ba ', 1);
    }

    public function testEvalAcceptsEmpty()
    {
        $i1 = new EmptyParser();
        $this->setProperty($i1, 'acceptsEmpty', true);

        $i2 = new StringParser('a');
        $this->setProperty($i2, 'acceptsEmpty', false);

        $parser = new GreedyMultiParser($i1, 0);
        $this->assertSame(true, $this->invokeMethod($parser, 'evalAcceptsEmpty'));

        $parser = new GreedyMultiParser($i2, 0);
        $this->assertSame(true, $this->invokeMethod($parser, 'evalAcceptsEmpty'));

        $parser = new GreedyMultiParser($i1, 1);
        $this->assertSame(true, $this->invokeMethod($parser, 'evalAcceptsEmpty'));

        $parser = new GreedyMultiParser($i2, 1);
        $this->assertSame(false, $this->invokeMethod($parser, 'evalAcceptsEmpty'));
    }

    public function testFirstSet()
    {
        $parser = new GreedyMultiParser('i');
        $this->assertSame(['i'], $this->invokeMethod($parser, 'firstSet'));
    }
}
