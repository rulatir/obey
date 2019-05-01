<?php


namespace Obey;


use Obey\Node\Sequence;

abstract class Renderer
{
    public function setOptions(array $options) : array
    {
        return OptionsHelper::setOptions($this, $options);
    }
    abstract public function render(Parser $parser, Sequence $sequence) : string;
}
