<?php


namespace Obey\Front;

use Obey\PathHelper as Path;
use Obey\Traits\HasOptions;

class InputResolver
{
    use HasOptions;

    const OPTIONS = [
        'rootDir'
    ];

    /** @var string */
    private $rootDir;

    public function resolve(Unit $unit)
    {
        $inputPattern = Path::cat($this->getRootDir(), $unit->getGroup()->getInputPattern());
        $inputFile = str_replace('*', $unit->getName(), $inputPattern);
        $unit->setInputFile($inputFile);
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
}
