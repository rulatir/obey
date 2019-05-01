<?php


namespace Obey\Traits;

use Obey\OptionsHelper;

trait HasOptions
{
    public function setOptions(array $options) : array
    {
        return OptionsHelper::setOptions($this, $options);
    }
}
