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

    public function __construct(string $name)
    {
        $this->name = $name;
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
}
