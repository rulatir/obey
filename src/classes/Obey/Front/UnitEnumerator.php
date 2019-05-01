<?php


namespace Obey\Front;

use Obey\PathHelper as Path;
use Obey\Traits\HasOptions;

class UnitEnumerator
{
    use HasOptions;
    const OPTIONS = [
        'rootDir'
    ];

    /** @var string */
    private $rootDir;

    /** @var UnitGroup[] */
    private $unitGroups;

    public function addUnitGroup(string $inputPattern)
    {
        $this->discoverUnits($this->unitGroups[] = new UnitGroup($inputPattern));
    }

    /**
     * @param UnitGroup $group
     * @return Unit[]
     */
    public function discoverUnits(UnitGroup $group) : array
    {
        $result = [];
        $componentExtractor = self::patternToRegex(
            Path::cat($this->getRootDir(), $group->getSubDir(), $group->getFilePattern())
        );
        $globPattern = Path::cat($this->getRootDir(), $group->getInputPattern());
        foreach (glob($globPattern) as $file) {
            $matches=[];
            if (
                basename($file, '.php') !== 'Obeyfile'
                && is_file($file)
                && preg_match($componentExtractor, $file, $matches)
            ) {
                $group->addUnit(new Unit($group, $matches[1]));
            }
        }
        return $result;
    }

    public function all()
    {
        foreach ($this->unitGroups as $group) {
            yield from $group->getUnits();
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

    public static function patternToRegex(string $pattern) : string
    {
        $segments = explode('*', $pattern);
        $segments = array_map(function (string $seg) : string {
            return preg_quote($seg, '/');
        }, $segments);
        return '/^'.implode('(.*)', $segments).'$/';
    }
}
