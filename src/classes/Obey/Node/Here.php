<?php


namespace Obey\Node;

class Here extends SequenceHolder
{
    /**
     * @var string
     */
    protected $name;

    public function __construct(string $name, Sequence $content)
    {
        parent::__construct($content);
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
