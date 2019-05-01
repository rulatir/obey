<?php


namespace Obey\Front;

class UnitGroup
{
    /** @var string  */
    private $inputPattern;

    /** @var Unit[] */
    private $units = [];

    public function __construct(string $inputPattern)
    {
        $this->inputPattern = $inputPattern;
    }

    public function getInputPattern() : string
    {
        return $this->inputPattern;
    }

    public function getFilePattern() : string
    {
        return basename($this->getInputPattern());
    }

    public function getSubDir() : string
    {
        return "."===($subdir = dirname($this->getInputPattern())) ? "" : $subdir;
    }

    public function addUnit(Unit $unit) : void
    {
        $this->units[] = $unit;
    }

    /**
     * @return Unit[]
     */
    public function getUnits() : array
    {
        return $this->units;
    }
}
