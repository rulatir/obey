<?php


namespace Obey;

use Obey\Node\Sequence;

class Node
{
    /** @var Sequence|null */
    protected $parent=null;

    /**
     * @return Sequence|null
     */
    public function getParent(): ?Sequence
    {
        return $this->parent;
    }

    /**
     * @param Sequence|null $parent
     */
    public function setParent(?Sequence $parent): void
    {
        $this->parent = $parent;
    }
}
