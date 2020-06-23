<?php

namespace Obey;

use Obey\Front\Importer;
use Obey\Front\InputRecorder;
use Obey\Front\InputResolver;
use Obey\Front\Obtainer;
use Obey\Front\OutputResolver;
use Obey\Front\Unit;
use Obey\Front\UnitEnumerator;
use Obey\Traits\HasOptions;

class Main
{
    use HasOptions { setOptions as protected applyOptions; }

    const OPTIONS = [

        'rootDir',
        'setupFile',
        'outputDir',
        'inputPattern',
        'outputNameTemplate',
        'inputs',
        'style',
        'op',
        'oneline',
        'listRelTo'
    ];

    protected static ?Main $instance=null;

    protected array $argv;

    protected array $options;

    protected string $rootDir;

    protected string $setupFile;

    protected string $outputDir;

    protected string $inputPattern;

    /** @var string[] */
    protected array $inputs;

    protected array $style;

    protected string $op;

    protected bool $oneline;

    protected ?string $listRelTo;

    //END options

    protected Obtainer $obtainer;

    protected Parser $parser;

    protected Importer $importer;

    protected Renderer $renderer;

    public static function run(array $argv)
    {
        $main = self::$instance = new self();
        $options = CommandLine::parse($main->argv = $argv);
        $options = $main->normalizeOptions($options);
        $main->setOptions($options);
        $main->runInstance();
    }

    /**
     * @return string
     */
    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    /**
     * @param string $rootDir
     */
    public function setRootDir(string $rootDir): void
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @return string
     */
    public function getSetupFile(): string
    {
        return $this->setupFile;
    }

    /**
     * @param string $setupFile
     */
    public function setSetupFile(string $setupFile): void
    {
        $this->setupFile = $setupFile;
    }

    /**
     * @return string
     */
    public function getOutputDir(): string
    {
        return $this->outputDir;
    }

    /**
     * @param string $outputDir
     */
    public function setOutputDir(string $outputDir): void
    {
        $this->outputDir = $outputDir;
    }

    public static function getInstance() : self
    {
        return self::$instance;
    }

    /**
     * @return Obtainer
     */
    public function getObtainer(): Obtainer
    {
        return $this->obtainer;
    }

    /**
     * @param Obtainer $obtainer
     */
    public function setObtainer(Obtainer $obtainer): void
    {
        $obtainer->setOptions($this->getStyle()['obtainerOpts']);
        $this->obtainer = $obtainer;
    }

    /**
     * @return Parser
     */
    public function getParser(): Parser
    {
        return $this->parser;
    }

    /**
     * @param Parser $parser
     */
    public function setParser(Parser $parser): void
    {
        Parser::setInstance($parser);
        $this->parser = $parser;
    }

    /**
     * @return Importer
     */
    public function getImporter(): Importer
    {
        return $this->importer;
    }

    /**
     * @param Importer $importer
     */
    public function setImporter(Importer $importer): void
    {
        $this->passOptionsTo($importer);
        $this->importer = $importer;
    }

    /**
     * @return Renderer
     */
    public function getRenderer(): Renderer
    {
        return $this->renderer;
    }

    /**
     * @param Renderer $renderer
     */
    public function setRenderer(Renderer $renderer): void
    {
        $renderer->setOptions($this->getStyle()['rendererOpts']);
        $this->renderer = $renderer;
    }

    /**
     * @return string
     */
    public function getInputPattern(): string
    {
        return $this->inputPattern;
    }

    /**
     * @param string $inputPattern
     */
    public function setInputPattern(string $inputPattern): void
    {
        $this->inputPattern = $inputPattern;
    }

    /**
     * @return array
     */
    public function getInputs(): array
    {
        return $this->inputs;
    }

    /**
     * @param array $inputs
     */
    public function setInputs(array $inputs): void
    {
        $this->inputs = $inputs;
    }

    /**
     * @return array
     */
    public function getStyle(): array
    {
        return $this->style;
    }

    /**
     * @param array $style
     */
    public function setStyle(array $style): void
    {
        $this->style = $style;
    }

    public function getOp() : string
    {
        return $this->op;
    }

    public function setOp(string $op): void
    {
        $this->op = $op;
    }

    public function getOneline(): bool
    {
        return $this->oneline;
    }

    public function setOneline(bool $oneline) : void
    {
        $this->oneline=$oneline;
    }

    public function getListRelTo(): string
    {
        return $this->listRelTo;
    }

    public function setListRelTo(string $listRelTo) : void
    {
        $this->listRelTo = $listRelTo;
    }

    public static function req(string $fname)
    {
        return Main::getInstance()->getObtainer()->req($fname);
    }

    protected function runInstance()
    {
        $inputRecorder = null;
        $this->setObtainer(new $this->style['obtainer']());
        if ('list-inputs'===$this->op) {
            $this->setObtainer($inputRecorder = new InputRecorder($this->getObtainer()));
        }
        $this->setImporter(new Importer($this->getObtainer()));
        $this->setParser(new Parser($this->obtainer));
        $this->setRenderer(new $this->style['renderer']());

        $enumerator = $this->passOptionsTo(new UnitEnumerator());
        foreach ($this->inputs as $inputPattern) {
            $enumerator->addUnitGroup($inputPattern);
        }
        $inputResolver = $this->passOptionsTo(new InputResolver());
        $outputResolver = $this->passOptionsTo(new OutputResolver());

        require_once __DIR__."/../../directives.php";

        /** @var Unit $unit */
        foreach ($enumerator->all() as $unit) {
            $inputResolver->resolve($unit);
            $outputResolver->resolve($unit);
            $this->processUnit($unit);
        }
        if ('list-inputs'===$this->op) {
            $inputs = array_map(
                fn($v) => PathHelper::unprefix($v, $this->getListRelTo()),
                $inputRecorder->getAllInputs()
            );
            echo implode($this->getOneline() ? " " : PHP_EOL, $inputs).PHP_EOL;
        }
    }

    protected function getDefaults() : array
    {
        $defaults = [];
        $defaults['rootDir'] = $dir = rtrim(getcwd(), DIRECTORY_SEPARATOR);
        $defaults['setupFile'] = "{$dir}/Obeyfile.php";
        $defaults['outputDir'] = '.';
        $defaults['inputPattern'] = '*.php';
        $defaults['outputNameTemplate'] = '{}';
        $defaults['inputs'] = [$defaults['inputPattern']];
        $defaults['importPaths'] = ['include'];
        $defaults['style'] = 'smart';
        $defaults['op'] = 'process';
        $defaults['oneline'] = false;
        $defaults['listRelTo'] = ".";
        return $defaults;
    }

    protected function normalizeOptions($options) : array
    {
        $defaults = $this->getDefaults();
        $normalized = array_merge($defaults, array_intersect_key($options, $defaults));
        if (isset($normalized['setupFile']) && $normalized['setupFile'] !== ($defaults['setupFile'] ?? null)) {
            /** @noinspection PhpIncludeInspection */
            $normalized = array_intersect_key(
                array_merge($normalized, require $normalized['setupFile']),
                $normalized
            );
            if ($normalized['rootDir']===$defaults['rootDir']) {
                $normalized['rootDir'] = dirname($normalized['setupFile']);
            }
        }
        if (!strlen($normalized['outputDir'])) {
            $normalized['outputDir'] = $normalized['rootDir'];
        } elseif (substr($normalized['outputDir'], 0, 1)!=='/') {
            $normalized['outputDir'] = "{$normalized['rootDir']}/{$normalized['outputDir']}";
        }
        if (is_string($styleName = $normalized['style'] ?? 'default')) {
            $normalized['style'] = Style::resolvePreset($styleName);
        }
        return $normalized;
    }

    protected function processUnit(Unit $unit) : void
    {
        switch($this->getOp()) {
        case "process":
            $this->generateOutputForUnit($unit); return;
        case "list-inputs":
            $this->generateOutputForUnit($unit, false);
            return;
        case "list-outputs":
            echo
                PathHelper::unprefix($unit->getOutputFile(),$this->getListRelTo())
                .($this->getOneLine() ? " " : PHP_EOL);
            return;
        default:
            throw new \InvalidArgumentException("Unsupported unit operation \"{$this->getOp()}\"");
        }
    }

    protected function generateOutputForUnit(Unit $unit, bool $writeOutput = true) : void
    {
        $inputFile = $unit->getInputFile();
        $outputFile = $unit->getOutputFile();
        $this->getParser()->setDebugInputFile($inputFile);
        try {
            $sequence = $this->getParser()->parseFile($inputFile);
            if ($writeOutput) {
                $output = $this->renderer->render($this->getParser(), $sequence);
                if (!is_dir($dir = dirname($outputFile))) {
                    @mkdir($dir, 0755, true);
                }
                file_put_contents($outputFile, $output);
            }
        } finally {
            $this->getParser()->setDebugInputFile(null);
        }
    }

    public function setOptions(array $options): array
    {
        return $this->applyOptions($this->options = $options);
    }

    protected function passOptionsTo($what)
    {
        if (is_object($what)) {
            OptionsHelper::setOptions($what, $this->options);
        }
        return $what;
    }
}
