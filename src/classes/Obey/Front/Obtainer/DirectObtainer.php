<?php


namespace Obey\Front\Obtainer;

use Obey\Front\Obtainer;

class DirectObtainer extends FileObtainer
{
    public function req(string $fname, bool $once = false)
    {
        if ($once) {
            require_once $fname;
        } else {
            require $fname;
        }
    }
}
