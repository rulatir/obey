<?php


namespace Obey\Front;

class Unit
{
    protected UnitGroup $group;

    protected string $name;

    protected ?string $inputFile;

    protected ?string $outputFile;

    public function __construct(UnitGroup $group, string $name)
    {
        $this->group = $group;
        $this->name = $name;
        $this->inputFile = $this->outputFile = null;
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
