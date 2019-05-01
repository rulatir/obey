<?php


namespace Obey;


use Obey\Traits\HasOptions;

abstract class Beautifier
{
    use HasOptions;
    abstract public function beautify(string $code) : string;
}
