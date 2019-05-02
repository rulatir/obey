<?php


namespace Obey\Front\Obtainer;

use Obey\Front\Obtainer;

class DirectObtainer extends FileObtainer
{
    public function req(string $fname)
    {
        require $fname;
    }
}
