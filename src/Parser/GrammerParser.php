<?php

namespace Nodebox\Parc\Parser;

use Nodebox\Parc\Node;
use Nodebox\Parc\ParseException;

class GrammerParser extends CombiParser
{
    /** @var string */
    protected $s;

    public function __construct($s, array $internals)
    {
        $this->s = (string)$s;
        parent::__construct($internals);

        if ( !isset($this->internals[$this->s])) {
            throw new \Exception(sprintf('No parser given for start rule %s!', $this->s));
        }

        foreach ($this->internals as $name => $internal) {
            $internal->setName($name);
        }

        $this->resolveNames();

        $this->floodAcceptsEmpty();

        $this->testInfinitGreedy();

        $this->testLeftRecursive();
    }

    protected function resolveNames()
    {
        $done = array();
        $todo = array($this);
        while ($current = array_shift($todo)) {
            $done[] = $current;

            foreach ($current->internals as $key => $internal) {
                if ($internal instanceof CombiParser) {
                    if ( !in_array($internal, $done, true) && !in_array($internal, $todo, true)) {
                        $todo[] = $internal;
                    }
                    continue;
                }

                if (is_string($internal)) {
                    if ( !isset($this->internals[$internal])) {
                        throw new \Exception(sprintf('No parser given for rule %s used by %s!', $internal, $current->description()));
                    }
                    $current->internals[$key] = $this->internals[$internal];
                }
            }
        }
    }

    protected function floodAcceptsEmpty()
    {
        $change = true;
        while ($change) {
            $change = false;

            $done = array();
            $todo = array($this);
            while ($current = array_shift($todo)) {
                if ( !($current instanceof CombiParser)) {
                    continue;
                }
                $done[] = $current;

                foreach ($current->internals as $internal) {
                    if ($internal->acceptsEmpty) {
                        continue;
                    }
                    if ( !in_array($internal, $done, true) && !in_array($internal, $todo, true)) {
                        $todo[] = $internal;
                    }
                    if ( !$internal->evalAcceptsEmpty()) {
                        continue;
                    }

                    $internal->acceptsEmpty = true;
                    $change = true;
                    break;
                }

                if ($change) {
                    break;
                }
            }
        }
    }

    protected function testInfinitGreedy()
    {
        $done = array();
        $todo = $this->internals;
        while ($current = array_shift($todo)) {
            if ( !($current instanceof CombiParser)) {
                continue;
            }
            $done[] = $current;

            foreach ($current->internals as $internal) {
                if ( !in_array($internal, $done, true) && !in_array($internal, $todo, true)) {
                    $todo[] = $internal;
                }
            }

            if ( !($current instanceof GreedyMultiParser)) {
                continue;
            }
            if ($current->optional() !== null) {
                continue;
            }
            if ($current->internals[0]->acceptsEmpty) {
                throw new \Exception(sprintf('Parser %s will cause infinite loops, because the internal parser accepts empty!', $current->description()));
            }
        }
    }

    protected function testLeftRecursive()
    {
        foreach ($this->internals as $internal) {
            if ( !($internal instanceof CombiParser)) {
                continue;
            }

            $done = array();
            $todo = array($internal);
            while ($current = array_shift($todo)) {
                $done[] = $current;

                foreach ($current->firstSet() as $next) {
                    if ( !($next instanceof CombiParser)) {
                        continue;
                    }
                    if ($next === $current) {
                        throw new \Exception(sprintf('Grammer is left recursive in %s!', $current->description()));
                    }

                    if (in_array($next, $done, true)) {
                        continue;
                    }

                    $todo[] = $next;
                }
            }
        }
    }

    /** */
    public function accept($string, $from)
    {
        $result = $this->internals[$this->s]->accept($string, $from);

        return new Node($this, $result->from(), $result->length(), $result);
    }

    public function parse($string)
    {
        $result = $this->accept($string, 0);
        if ($result->length() != strlen($string)) {
            throw new ParseException($this, $result->length());
        }
        return $result;
    }

    /** */
    protected function createDescription()
    {
        return sprintf('GrammerParser(%s, %s)', var_export($this->s, true), $this->internalsDescription());
    }

    /** */
    protected function evalAcceptsEmpty()
    {
        return $this->internals[$this->s]->acceptsEmpty;
    }

    /** */
    protected function firstSet()
    {
        return array($this->internals[$this->s]);
    }
}
