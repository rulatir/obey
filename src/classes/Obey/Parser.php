<?php


namespace Obey;

use Obey\Front\Obtainer;
use Obey\Node\Given;
use Obey\Node\Here;
use Obey\Node\Into;
use Obey\Node\Sequence;
use Obey\Node\Text;

class Parser
{
    protected ?Sequence $top;

    /** @var Sequence[]  */
    protected array $loci=[];

    protected ?string $debugInputFile = null;

    protected static ?Parser $instance = null;

    private Obtainer $obtainer;

    public function __construct(Obtainer $obtainer)
    {
        $this->obtainer = $obtainer;
    }

    public static function getInstance() : self
    {
        return static::$instance;
    }

    public static function setInstance(Parser $parser) : void
    {
        static::$instance = $parser;
    }

    public function beginInto(string $name, bool $reverse = false)
    {
        $this->flushText();
        $this->append($into = new Into($name, $reverse));
        $this->top = $into;
    }

    public function endInto() : string
    {
        $this->flushText();
        /** @var Into $into */
        $into = $this->top;
        assert($into instanceof Into, "endInto without beginInto");
        $locus = $this->getLocus($into->getName());
        if ($into->isReverse()) {
            foreach (array_reverse($into->getNodes()) as $node) {
                $locus->prepend($node);
            }
        } else {
            foreach ($into->getNodes() as $node) {
                $locus->append($node);
            }
        }
        $this->top = $into->getParent();
        return $into->getName();
    }

    public function beginGiven(string $condition)
    {
        $this->flushText();
        $this->append($given = new Given($condition));
        $this->top = $given;
    }

    public function endGiven()
    {
        $this->flushText();
        /** @var Given $given */
        $given = $this->top;
        assert($given instanceof Given, "endGiven without beginGiven");
        $this->top = $given->getParent();
    }

    public function here(string $name)
    {
        $this->flushText();
        $this->append(new Here($name, $this->getLocus($name)));
    }

    public function parseFile(string $file) : Sequence
    {
        $this->top = null;
        $this->loci = [];
        return $this->parseTemplate($file);
    }

    protected function parseTemplate(string $file) : Sequence
    {
        $oldTop = $this->top;
        $this->top = new Sequence();
        ob_start();
        Main::req($file);
        $this->flushText(false);
        $result = $this->top;
        $this->top = $oldTop;
        return $result;
    }

    protected function append(Node $node)
    {
        $node->setParent($this->top);
        if ($this->top instanceof Sequence) {
            $this->top->append($node);
        }
    }

    protected function flushText(bool $again = true)
    {
        if (ob_get_level()) {
            $text = ob_get_clean();
            if (strlen($text)) {
                $this->append(new Text($text));
            }
        }
        if ($again) {
            ob_start();
        }
    }

    protected function getLocus(string $name) : Sequence
    {
        return $this->loci[$name] ?? ($this->loci[$name] = new Sequence());
    }

    public function queryLoci(string $pattern) : array
    {
        if (false===strpos($pattern, '*')) {
            return ($v=($this->loci[$pattern] ?? null))
                ? [$pattern => $v]
                : [];
        }
        $regex = str_replace(".", "\\.", $pattern);
        $regex = str_replace("*", '.*', $regex);
        $regex = "/^{$regex}$/";
        $result = [];
        foreach ($this->loci as $locus => $sequence) {
            if (preg_match($regex, $locus)) {
                $result[$locus] = $sequence;
            }
        }
        return $result;
    }

    public function getAST() : ?Sequence
    {
        return $this->top;
    }

    public function getObtainer(): Obtainer
    {
        return $this->obtainer;
    }

    public function setObtainer(Obtainer $obtainer): void
    {
        $this->obtainer = $obtainer;
    }

    public function getDebugInputFile(): ?string
    {
        return $this->debugInputFile;
    }

    public function setDebugInputFile(?string $debugInputFile): void
    {
        $this->debugInputFile = $debugInputFile;
    }

    public function templateEndsWith(string $suffix) : bool
    {
        return preg_match('/'.preg_quote($suffix,"/").'$/',$this->debugInputFile ?? '');
    }
}
