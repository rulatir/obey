<?php


namespace Obey\Front;

abstract class Obtainer
{
    public function setOptions(array $options) : void
    {
    }

    public function incl(string $fname) : bool
    {
        if (!$this->exists($fname)) {
            return false;
        }
        $this->req($fname);
        return true;
    }
    abstract public function req(string $fname);
    abstract public function exists(string $fname) : bool;
}
