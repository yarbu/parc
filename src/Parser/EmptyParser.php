<?php

namespace Nodebox\Parc\Parser;

use Nodebox\Parc\Node;

class EmptyParser extends BaseParser
{
    /** */
    public function accept($string, $from)
    {
        return new Node($this, $from, 0, null);
    }

    /** */
    public function createDescription()
    {
        return 'EmptyParser()';
    }

    /** */
    protected function evalAcceptsEmpty()
    {
        return true;
    }
}
