<?php


namespace Obey;

use Obey\Node\Sequence;

class Node
{
    protected ?Sequence $parent=null;

    public function getParent(): ?Sequence
    {
        return $this->parent;
    }

    public function setParent(?Sequence $parent): void
    {
        $this->parent = $parent;
    }
}
