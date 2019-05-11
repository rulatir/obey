<?php


namespace Obey\Renderer;

use Obey\Beautifier;

class PreservingRenderer extends SmartRenderer
{
    public function __construct()
    {
        parent::__construct(new Beautifier\Nop());
    }

}
