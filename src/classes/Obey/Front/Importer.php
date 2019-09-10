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
     * @param bool $once
     */
    public function import(string $name, bool $once=false) : void
    {
        if (Path::isAbsolute($name)) {
            $this->importAbsolute($name, $once);
        } else {
            $this->importRelative($name, $once);
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
     * @param bool $once
     */
    protected function importAbsolute(string $name, bool $once=false): void
    {
        if (!$this->getObtainer()->incl("{$name}.php", $once)) {
            throw new RuntimeException("Template \"{$name}\" not found");
        }
    }

    /**
     * @param string $name
     * @param bool $once
     */
    protected function importRelative(string $name, bool $once = false) : void
    {
        foreach ($importPaths = $this->getImportPaths() as $importPath) {
            $segments = [$importPath, "{$name}.php"];
            if (!Path::isAbsolute($importPath)) {
                array_unshift($segments, $this->getRootDir());
            }
            if ($this->getObtainer()->incl(Path::cat(... $segments), $once)) {
                return;
            }
        }
        throw new RuntimeException(
            "Template \"{$name}\" not found in (" . implode(":", $importPaths) . ")"
        );
    }
}
