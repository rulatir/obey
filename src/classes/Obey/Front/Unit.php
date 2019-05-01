<?php


namespace Obey\Front;

class Unit
{
    /** @var UnitGroup */
    protected $group;

    /** @var string */
    protected $name;

    /** @var string|null */
    protected $inputFile;

    /** @var string|null */
    protected $outputFile;

    public function __construct(UnitGroup $group, string $name)
    {
        $this->group = $group;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getInputFile(): ?string
    {
        return $this->inputFile;
    }

    /**
     * @param string|null $inputFile
     */
    public function setInputFile(?string $inputFile): void
    {
        $this->inputFile = $inputFile;
    }

    /**
     * @return string|null
     */
    public function getOutputFile(): ?string
    {
        return $this->outputFile;
    }

    /**
     * @param string|null $outputFile
     */
    public function setOutputFile(?string $outputFile): void
    {
        $this->outputFile = $outputFile;
    }

    /**
     * @return UnitGroup
     */
    public function getGroup(): UnitGroup
    {
        return $this->group;
    }
}
