<?php


namespace Obey\Front;

abstract class Obtainer
{
    public function setOptions(array $options) : void
    {
    }

    public function incl(string $fname, bool $once=false) : bool
    {
        if (!$this->exists($fname)) {
            return false;
        }
        $this->req($fname, $once);
        return true;
    }
    abstract public function req(string $fname, bool $once=false);
    abstract public function exists(string $fname) : bool;
}
