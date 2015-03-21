<?php

namespace Nodebox\Parc\Parser;

use Nodebox\Parc\Node;
use Nodebox\Parc\ParseException;

class LazyAltParser extends CombiParser
{
    /** */
    public function accept($string, $from)
    {
        foreach ($this->internals as $internal) {
            try {
                $result = $internal->accept($string, $from);
                return new Node($this, $result->from(), $result->length(), $result);
            } catch (ParseException $oje) { }
        }
        throw new ParseException($this, $from);
    }

    /** */
    public function createDescription()
    {
        return sprintf('LazyAltParser(%s)', $this->internalsDescription());
    }

    /** */
    protected function evalAcceptsEmpty()
    {
        foreach ($this->internals as $internal) {
            if ($internal->acceptsEmpty) {
                return true;
            }
        }
        return false;
    }

    /** */
    protected function firstSet()
    {
        return $this->internals;
    }
}
