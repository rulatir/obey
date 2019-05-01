<?php


namespace Obey\Front;

use Obey\PathHelper as Path;
use Obey\Traits\HasOptions;

class Importer
{
    use HasOptions;
    const OPTIONS = [
        'rootDir'
    ];

    /** @var string */
    private $rootDir;

    /**
     * @var Obtainer
     */
    private $obtainer;

    public function __construct(Obtainer $obtainer)
    {
        $this->obtainer = $obtainer;
    }

    public function import(string $name)
    {
        $this->obtainer->req(Path::cat($this->getRootDir(), "{$name}.php"));
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
