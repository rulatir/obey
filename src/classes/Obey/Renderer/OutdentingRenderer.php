<?php


namespace Obey\Renderer;

use Obey\Renderer;
use Obey\Parser;
use Obey\Node\Sequence;
use Obey\Node\Text;
use Obey\Node\Here;
use Obey\Node\Given;
use Obey\Node\Into;

class OutdentingRenderer extends Renderer
{
    /** @var string[] */
    protected $segments = [];

    /** @var Parser|null  */
    protected $parser = null;

    protected $rules = [];

    public function render(Parser $parser, Sequence $sequence) : string
    {
        $this->parser = $parser;
        $this->segments = [];
        $this->outputSequence($sequence);
        $this->parser=null;
        return implode("", $this->segments);
    }

    public function outputSequence(Sequence $sequence)
    {
        foreach ($sequence->getNodes() as $node) {
            switch (true) {
                case $node instanceof Text:
                    $this->segments[] = $this->outdent($node);
                    break;
                case $node instanceof Here:
                    $this->outputSequence($node->getContent());
                    break;
                case $node instanceof Given:
                    if ($node->isSatisfiedBy($this->parser)) {
                        $this->outputSequence($node);
                    }
                    break;
                default:
                    break;
            }
        }
    }
    protected function outdent(Text $node)
    {
        $content = $node->getContent();
        if (!($parent = $node->getParent()) || !($parent instanceof Into)) {
            return $content;
        }
        $result = [];
        $baseIndent = max(0, $parent->getIndent() ?? 0);
        foreach (explode("\n", $content) as $line) {
            $matches = [];
            preg_match("/^(\\s*)(.*)$/u", $line, $matches);
            [, $ws, $printable] = $matches;
            $wsLen = Text::computeIndentLength($ws);
            $ws = str_repeat(" ", max(0, $wsLen - $baseIndent));
            $result[] = $ws.$printable;
        }
        return implode("\n", $result);
    }
}
