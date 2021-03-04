<?php


namespace Obey\Front;


class InputRecorder extends Obtainer
{
    private Obtainer $obtainer;

    /** @var bool[] */
    private array $inputs = [];

    public function __construct(Obtainer $obtainer)
    {
        $this->obtainer = $obtainer;
    }

    public function req(string $fname, bool $once=false)
    {
        $this->obtainer->req($fname, $once);
        $this->recordInput($fname);
    }

    public function exists(string $fname): bool
    {
        return $this->obtainer->exists($fname);
    }

    public function getAllInputs() : array
    {
        return array_keys($this->inputs);
    }

    protected function recordInput(string $fname) : void
    {
        $this->inputs[stream_resolve_include_path($fname)]=true;
    }
}