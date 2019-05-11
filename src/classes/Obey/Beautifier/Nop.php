<?php


namespace Obey\Beautifier;

use Obey\Beautifier;

class Nop extends Beautifier
{
    public function beautify(string $code): string
    {
        return $code;
    }
}
