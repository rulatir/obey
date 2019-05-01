<?php


namespace Obey\Node;

use Obey\Node;

abstract class SequenceHolder extends Node
{
    /**
     * @var Sequence|null
     */

    protected $content = null;
    public function __construct(Sequence $content)
    {
        $this->content = $content;
    }

    public function getContent() : Sequence
    {
        return $this->content;
    }
}
