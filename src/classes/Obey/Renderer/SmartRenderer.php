<?php


namespace Obey\Renderer;

use Obey\Beautifier;
use Obey\OptionsHelper;
use Obey\Renderer;
use Obey\Parser;
use Obey\Node\Sequence;
use Obey\Node\Text;
use Obey\Node\Here;
use Obey\Node\Given;
use stdClass;

class SmartRenderer extends Renderer
{
    const OPTIONS = [
        'tabSize',
        'rules'
    ];

    /** @var Parser|null  */
    private $parser = null;

    /** @var int */
    private $tabSize;

    /** @var array  */
    private $rules = [];

    /** @var Beautifier|null */
    private $beautifier;

    public function __construct(?Beautifier $beautifier = null)
    {
        $this->beautifier = $beautifier ?? new Beautifier\RuleBasedIndenter();
    }

    public function render(Parser $parser, Sequence $sequence): string
    {
        $this->parser = $parser;
        $result = $this->renderSequence($sequence);
        if ($this->beautifier) {
            $result = $this->beautifier->beautify($result);
        }
        return $result;
    }

    /**
     * @return int
     */
    public function getTabSize(): int
    {
        return $this->tabSize;
    }

    /**
     * @param int $tabSize
     */
    public function setTabSize(int $tabSize): void
    {
        $this->tabSize = $tabSize;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param array $rules
     */
    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }

    /**
     * @return Beautifier|null
     */
    public function getBeautifier(): ?Beautifier
    {
        return $this->beautifier;
    }

    /**
     * @param Beautifier|null $beautifier
     */
    public function setBeautifier(?Beautifier $beautifier): void
    {
        $this->beautifier = $beautifier;
    }

    public function setOptions(array $options) : array
    {
        $remaining = OptionsHelper::setOptions($this, $options);
        OptionsHelper::setOptions($this->getBeautifier(), $options);
        return $remaining;
    }

    protected function renderSequence(Sequence $sequence): string
    {
        $result = "";
        foreach ($sequence->getNodes() as $node) {
            $text = null;
            $type = null;
            $name = null;
            $condition = null;
            if ($node instanceof Text) {
                $result .= $node->getContent();
            } elseif ($node instanceof Here) {
                $result .= $this->renderSequence($node->getContent());
            } elseif ($node instanceof Given && $node->isSatisfiedBy($this->parser)) {
                $result .= $this->renderSequence($node);
            }
        }
        return $result;
    }

    protected function beautify(string $input) : string
    {
        $beautifier = (object)[

            'lastLine' => "",
            'indent' => 0
        ];
        $lines = explode("\n", $input);
        $lines = array_map(
            function (string $line) use ($beautifier) {
                return $this->beautifyLine($beautifier, $line);
            },
            $lines
        );
        return implode("\n", array_filter($lines, 'is_string'));
    }

    protected function beautifyLine(stdClass $beautifier, string $line) : ?string
    {
        $my = $beautifier;
        if (!strlen($line = trim($line)) && !strlen(trim($my->lastLine))) {
            return null;
        }
        [$before, $after] = $this->classify($line);
        $indent = $my->indent += $before;
        $my->indent += $after;
        return $my->lastLine = str_repeat(' ', $this->tabSize * $indent).$line;
    }

    protected function classify($line) : array
    {
        foreach ($$this->rules as $regex=>$indentOp) {
            if (preg_match("/".$regex."/", $line)) {
                return $indentOp;
            }
        }
        return [0,0];
    }
}
