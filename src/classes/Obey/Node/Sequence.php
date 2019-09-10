<?php


namespace Obey\Node;

use Obey\Node;

class Sequence extends Node
{
    /** @var Node[] */
    protected $nodes = [];
    public function append(Node $node)
    {
        $this->nodes[]=$node;
    }

    public function prepend(Node $node)
    {
        array_unshift($this->nodes, $node);
    }

    /**
     * @return Node[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }
}
