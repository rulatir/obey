<?php


namespace Obey\Front;

use Obey\PathHelper as Path;
use Obey\Traits\HasOptions;
use RuntimeException;

class Importer
{
    use HasOptions;
    const OPTIONS = [
        'rootDir',
        'importPaths'
    ];

    /** @var string */
    private $rootDir;

    /** @var string[] */
    private $importPaths;

    /**
     * @var Obtainer
     */
    private $obtainer;

    public function __construct(Obtainer $obtainer)
    {
        $this->obtainer = $obtainer;
    }

    /**
     * @param string $name
     * @throws RuntimeException
     */
    public function import(string $name)
    {
        foreach ($this->getImportPaths() as $importPath) {
            $file = Path::cat($this->getRootDir(), $importPath, "{$name}.php");
            if ($this->getObtainer()->incl($file)) {
                return;
            }
        }
        throw new RuntimeException(
            "Template \"{$name}\" not found in (".implode(":", $this->getImportPaths()).")"
        );
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
     * @return string[]
     */
    public function getImportPaths(): array
    {
        return $this->importPaths;
    }

    /**
     * @param string[] $importPaths
     */
    public function setImportPaths(array $importPaths): void
    {
        $this->importPaths = $importPaths;
    }

    /**
     * @return Obtainer
     */
    public function getObtainer(): Obtainer
    {
        return $this->obtainer;
    }
}
