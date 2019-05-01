<?php


namespace Obey\Front;

abstract class Obtainer
{
    public function setOptions(array $options) : void
    {
    }

    abstract public function req(string $fname);
}
