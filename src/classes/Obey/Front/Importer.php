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
    public function import(string $name) : void
    {
        if (Path::isAbsolute($name)) {
            $this->importAbsolute($name);
        } else {
            $this->importRelative($name);
        }
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

    /**
     * @param string $name
     * @throws RuntimeException
     */
    protected function importAbsolute(string $name): void
    {
        if (!$this->getObtainer()->incl("{$name}.php")) {
            throw new RuntimeException("Template \"{$name}\" not found");
        }
    }

    /**
     * @param string $name
     * @throws RuntimeException
     */
    protected function importRelative(string $name) : void
    {
        foreach ($importPaths = $this->getImportPaths() as $importPath) {
            $segments = [$importPath, "{$name}.php"];
            if (!Path::isAbsolute($importPath)) {
                array_unshift($segments, $this->getRootDir());
            }
            if ($this->getObtainer()->incl(Path::cat(... $segments))) {
                return;
            }
        }
        throw new RuntimeException(
            "Template \"{$name}\" not found in (" . implode(":", $importPaths) . ")"
        );
    }
}
