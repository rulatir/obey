<?php


namespace Obey\Front;

use Obey\PathHelper as Path;
use Obey\Traits\HasOptions;

class OutputResolver
{
    use HasOptions;

    const OPTIONS = [
        'rootDir',
        'outputDir',
        'outputNameTemplate'
    ];

    /** @var string */
    private $rootDir;

    /** @var string */
    private $outputDir;

    /** @var string */
    private $outputNameTemplate;

    public function resolve(Unit $unit)
    {
        $outputPattern = Path::cat(
            $this->getOutputDir(),
            $unit->getGroup()->getSubDir(),
            $this->getOutputNameTemplate()
        );
        $outputFile = str_replace('{}', $unit->getName(), $outputPattern);
        $unit->setOutputFile($outputFile);
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

    /**
     * @return string
     */
    public function getOutputNameTemplate(): string
    {
        return $this->outputNameTemplate;
    }

    /**
     * @param string $outputNameTemplate
     */
    public function setOutputNameTemplate(string $outputNameTemplate): void
    {
        $this->outputNameTemplate = $outputNameTemplate;
    }
}
