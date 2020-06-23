<?php


namespace Obey\Renderer;


use Obey\Node\Sequence;
use Obey\Parser;
use Obey\Renderer;

class NopRenderer extends Renderer
{
    public function render(Parser $parser, Sequence $sequence): string
    {
        return "";
    }
}