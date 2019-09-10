<?php


namespace Obey\Node;

use Obey\Node;

class Into extends Sequence
{
    const SPACES_PER_TAB = 4;
    /**
     * @var string
     */
    protected $name;

    /**
     * @var int|null
     */
    protected $indent = null;
    /**
     * @var bool
     */
    private $reverse = false;

    public function __construct(string $name, bool $reverse = false)
    {
        $this->name = $name;
        $this->reverse = $reverse;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int|null
     */
    public function getIndent(): ?int
    {
        return $this->indent;
    }

    /**
     * @param int|null $indent
     */
    public function setIndent(?int $indent): void
    {
        $this->indent = $indent;
    }

    public function append(Node $node)
    {
        parent::append($node);
        if ($node instanceof Text && null===$this->getIndent()) {
            $this->setIndent($node->getRealIndent());
        }
    }

    /**
     * @return bool
     */
    public function isReverse(): bool
    {
        return $this->reverse;
    }
}
