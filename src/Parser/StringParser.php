<?php

namespace Nodebox\Parc\Parser;

use Nodebox\Parc\Node;
use Nodebox\Parc\ParseException;

class StringParser extends BaseParser
{
    /** @var string */
    protected $needle;

    /**
     * @param string $needle The string to accept.
     */
    public function __construct($needle)
    {
        $this->needle = (string)$needle;
        parent::__construct();
    }

    /**
     * Returns the string to accept.
     *
     * @return string The string to accept.
     */
    public function needle()
    {
        return $this->needle;
    }

    /** */
    public function accept($string, $from)
    {
        if ($this->needle !== '' && strpos($string, $this->needle, $from) !== $from) {
            throw new ParseException($this, $from);
        }

        return new Node($this, $from, strlen($this->needle), $this->needle);
    }

    /** */
    protected function createDescription()
    {
        return sprintf('new StringParser(%s)', var_export($this->needle, true));
    }

    /** */
    protected function evalAcceptsEmpty()
    {
        return $this->needle === '';
    }
}
