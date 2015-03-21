<?php

namespace Nodebox\Parc;

class NodeTest extends \Nodebox\Parc\TestCase
{
    public function testConstruct()
    {
        $parser = new Parser\StringParser('needle');
        $node = new Node($parser, 2, 6, 'needle');

        $this->assertSame($parser, $node->parser());
        $this->assertSame(2, $node->from());
        $this->assertSame(6, $node->length());
        $this->assertSame('needle', $node->result());

        return $node;
    }

    public function testState()
    {
        $parser = $this->getMockBuilder('Nodebox\Parc\ParserInterface')
                       ->getMock();

        // Configure the stub.
        $parser->method('name')
               ->will($this->returnValue('mock'));

        $node1 = new Node($parser, 2, 6, 'needle');
        $this->assertSame(['parser' => 'mock', 'result' => 'needle'], $node1->state());

        $node2 = new Node($parser, 2, 6, $node1);
        $this->assertSame(['parser' => 'mock', 'result' => ['parser' => 'mock', 'result' => 'needle']], $node2->state());

        $node2 = new Node($parser, 2, 6, [$node1]);
        $this->assertSame(['parser' => 'mock', 'result' => [['parser' => 'mock', 'result' => 'needle']]], $node2->state());
    }

    public function testToString()
    {
        $parser = $this->getMockBuilder('Nodebox\Parc\ParserInterface')
                       ->getMock();

        // Configure the stub.
        $parser->method('name')
               ->will($this->returnValue('mock'));

        $node1 = new Node($parser, 2, 6, 'needle');
        $this->assertSame(json_encode(['parser' => 'mock', 'result' => 'needle']), (string)$node1);
    }
}
