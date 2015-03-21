<?php

namespace Nodebox\Parc\Parser;

use Nodebox\Parc\Node;

class ConcatParser extends CombiParser
{
    /** */
    public function accept($string, $from)
    {
        $ifrom = $from;
        $result = array();
        foreach ($this->internals as $internal) {
            $iresult = $internal->accept($string, $ifrom);

            $ifrom+= $iresult->length();
            $result[] = $iresult;
        }

        return new Node($this, $from, $ifrom - $from, $result);
    }

    /** */
    protected function createDescription()
    {
        return sprintf('ConcatParser(%s)', $this->internalsDescription());
    }

    /** */
    protected function evalAcceptsEmpty()
    {
        foreach ($this->internals as $internal) {
            if ( !$internal->acceptsEmpty) {
                return false;
            }
        }
        return true;
    }

    /** */
    protected function firstSet()
    {
        $firstSet = array();
        foreach ($this->internals as $internal) {
            $firstSet[] = $internal;

            if ( !$internal->acceptsEmpty) {
                break;
            }
        }
        return $firstSet;
    }
}
