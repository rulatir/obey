<?php


namespace Obey\Front\Obtainer;

use Obey\Front\Obtainer;

class DirectObtainer extends Obtainer
{
    public function req(string $fname)
    {
        require($fname);
    }
}
