<?php

namespace Nodebox\Parc\Parser;

use Nodebox\Parc\ParserInterface;

abstract class BaseParser implements ParserInterface
{
    /** @var string */
    protected $name = '';

    /** @var string */
    protected $description;

    public function __construct()
    {
        $this->description = $this->createDescription();
    }

    /** */
    public function name()
    {
        return $this->name;
    }

    /**
     * Sets the name of the parser.
     *
     * @param string $name The name
     */
    protected function setName($name)
    {
        $this->name = (string)$name;
    }

    /** */
    public function description()
    {
        return $this->description;
    }

    /**
     * Returns a description of the parser
     *
     * @return string The description
     */
    protected abstract function createDescription();

    /** @var bool */
    protected $acceptsEmpty = false;

    /**
     * Returns if this Parser accepts an empty input.
     *
     * @return bool This Parser accepts an empty input?
     */
    protected abstract function evalAcceptsEmpty();
}
