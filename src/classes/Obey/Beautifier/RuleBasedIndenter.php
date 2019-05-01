<?php


namespace Obey\Beautifier;

use Obey\Beautifier;
use Obey\Traits\HasOptions;

class RuleBasedIndenter extends Beautifier
{
    use HasOptions;

    const OPTIONS = [
        'tabSize',
        'rules'
    ];

    /** @var int */
    private $tabSize;

    /** @var array */
    private $rules;

    private $lastLine="";
    private $indent=0;

    public function beautify(string $code) : string
    {
        $this->lastLine = "";
        $this->indent = 0;

        $result = [];
        foreach (explode("\n", $code) as $line) {
            $result[] = $this->beautifyLine($line);
        }

        return implode("\n", array_filter($result, 'is_string'));
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

    protected function beautifyLine(string $line) : ?string
    {
        if (!strlen($line = trim($line)) && !strlen(trim($this->lastLine))) {
            return null;
        }
        [$before, $after] = $this->classify($line);
        $indent = $this->indent += $before;
        $this->indent += $after;
        return $this->lastLine = str_repeat(' ', $this->tabSize * $indent).$line;
    }

    protected function classify($line) : array
    {
        foreach ($this->rules as $regex=>$indentOp) {
            if (preg_match("/".$regex."/", $line)) {
                return $indentOp;
            }
        }
        return [0,0];
    }
}
