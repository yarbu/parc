<?php

namespace Nodebox\Parc;

class Node
{
    /** @var ParserInterface */
    protected $parser;

    /** @var int */
    protected $from;

    /** @var int */
    protected $length;

    /** @var mixed */
    protected $result;

    public function __construct(ParserInterface $parser, $from, $length, $result)
    {
        $this->parser = $parser;

        $this->from = (int)$from;
        $this->length = (int)$length;

        $this->result = $result;
    }

    /**
     * Returns the parser that created this node.
     *
     * @return ParserInterface The parser that created this node.
     */
    public function parser()
    {
        return $this->parser;
    }

    /**
     * Returns the position from which this node covers the input string.
     *
     * @return int The position from which this node covers the input string.
     */
    public function from()
    {
        return $this->from;
    }

    /**
     * Returns the length of the input string which is covered by the node.
     *
     * @return int The length of the input string which is covered by the node.
     */
    public function length()
    {
        return $this->length;
    }

    /**
     * The result of the parser.
     *
     * @return mixed The Result.
     */
    public function result()
    {
        return $this->result;
    }

    public function state()
    {
        $state = array();

        if ($this->parser->name()) {
            $state['parser'] = $this->parser->name();
        }

        //$state['from'] = $this->from;
        //$state['length'] = $this->length;

        if (is_array($this->result)) {
            $state['result'] = array();
            foreach ($this->result as $node) {
                $state['result'][] = $node->state();
            }
        } else if ($this->result instanceof Node) {
            $state['result'] = $this->result->state();
        } else {
            $state['result'] = $this->result;
        }

        return $state;
    }

    public function __toString()
    {
        return json_encode($this->state());
    }
}
