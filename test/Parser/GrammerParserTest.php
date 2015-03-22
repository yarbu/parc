<?php

namespace Nodebox\Parc\Parser;

class GrammerParserTest extends \Nodebox\Parc\TestCase
{
    public function testConstruct()
    {
        $i = new StringParser('a');
        $p = new GrammerParser('s', ['s' => $i]);
        $this->assertSame('s', $i->name());
        $this->assertSame("GrammerParser('s', ['s' => StringParser('a')])", $p->description());
    }

    /**
     * @expectedException Exception
     */
    public function testConstructFail()
    {
        $i = new StringParser('a');
        $p = new GrammerParser('s', ['a' => $i]);
    }

    public function testResolveNames()
    {
        $is = [
            'i1' => new StringParser('a'),
            'i2' => new GreedyMultiParser('i1', 1),
        ];
        $p = new GrammerParser('i2', $is);
        $this->assertSame([$is['i1']], $this->getProperty($is['i2'], 'internals'));
    }

    /**
     * @expectedException Exception
     */
    public function testResolveNamesFail()
    {
        $is = [
            'i1' => new StringParser('a'),
            'i2' => new GreedyMultiParser('a', 1),
        ];
        $p = new GrammerParser('i2', $is);
    }

    public function testFloodAcceptsEmpty()
    {
        $is = [
            'i1' => new StringParser('a'),
            'i2' => new StringParser(''),
            'i3' => new ConcatParser(['i1', 'i2']),
            'i4' => new ConcatParser(['i2', 'i2']),
        ];
        $p = new GrammerParser('i3', $is);
        $this->assertSame(false, $this->getProperty($is['i1'], 'acceptsEmpty'));
        $this->assertSame(true, $this->getProperty($is['i2'], 'acceptsEmpty'));
        $this->assertSame(false, $this->getProperty($is['i3'], 'acceptsEmpty'));
        $this->assertSame(true, $this->getProperty($is['i4'], 'acceptsEmpty'));
    }

    public function testInfinitGreedy()
    {
        $is = [
            'i1' => new StringParser('a'),
            'i2' => new GreedyMultiParser('i1'),
        ];
        $p = new GrammerParser('i2', $is);

        $is = [
            'i1' => new StringParser(''),
            'i2' => new GreedyMultiParser('i1', 0, 1),
        ];
        $p = new GrammerParser('i2', $is);
    }

    /**
     * @expectedException Exception
     */
    public function testInfinitGreedyFail1()
    {
        $is = [
            'i1' => new StringParser(''),
            'i2' => new GreedyMultiParser('i1'),
        ];
        $p = new GrammerParser('i2', $is);
    }

    public function testLeftRecursive()
    {
        $is = [
            'i1' => new StringParser('a'),
            'i2' => new ConcatParser(['i3']),
            'i3' => new ConcatParser(['i2', 'i1']),
        ];
        $p = new GrammerParser('i3', $is);
    }

    /**
     * @expectedException Exception
     */
    public function testLeftRecursiveFail()
    {
        $is = [
            'i1' => new StringParser('a'),
            'i2' => new ConcatParser(['i2', 'i1']),
        ];
        $p = new GrammerParser('i2', $is);
    }

    public function testAccept()
    {
        $is = [
            'i1' => new StringParser('a'),
        ];
        $p = new GrammerParser('i1', $is);

        $node = $p->accept(' abc ', 1);
        $this->assertSame(1, $node->from());
        $this->assertSame(1, $node->length());

        $result = $node->result();
        $this->assertSame(1, $result->from());
        $this->assertSame(1, $result->length());
        $this->assertSame('a', $result->result());
    }

    /**
     * @expectedException Nodebox\Parc\ParseException
     */
    public function testAcceptFail1()
    {
        $is = [
            'i1' => new StringParser('a'),
        ];
        $p = new GrammerParser('i1', $is);

        $node = $p->accept(' bbc ', 1);
    }

    public function testParse()
    {
        $is = [
            'i1' => new StringParser('a'),
        ];
        $p = new GrammerParser('i1', $is);

        $node = $p->parse('a');
        $this->assertSame(0, $node->from());
        $this->assertSame(1, $node->length());

        $result = $node->result();
        $this->assertSame(0, $result->from());
        $this->assertSame(1, $result->length());
        $this->assertSame('a', $result->result());
    }

    /**
     * @expectedException Nodebox\Parc\ParseException
     */
    public function testParseFail1()
    {
        $is = [
            'i1' => new StringParser('a'),
        ];
        $p = new GrammerParser('i1', $is);

        $node = $p->parse('abc');
    }

    public function testEvalAcceptsEmpty()
    {
        $i1 = new EmptyParser();
        $this->setProperty($i1, 'acceptsEmpty', true);

        $i2 = new StringParser('a');
        $this->setProperty($i2, 'acceptsEmpty', false);

        $parser = new GrammerParser('s', ['s' => $i1]);
        $this->assertSame(true, $this->invokeMethod($parser, 'evalAcceptsEmpty'));

        $parser = new GrammerParser('s', ['s' => $i2]);
        $this->assertSame(false, $this->invokeMethod($parser, 'evalAcceptsEmpty'));
    }

    public function testFirstSet()
    {
        $i1 = new EmptyParser();
        $i2 = new StringParser('a');
        $parser = new GrammerParser('s', ['s' => $i1, 'a' => $i2]);
        $this->assertSame([$i1], $this->invokeMethod($parser, 'firstSet'));
    }
}
