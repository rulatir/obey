<?php


namespace Obey\Front\Obtainer;

class NewlineReinserter extends PreprocessingFileObtainer
{
    protected function onCompoundToken(array $token, string &$value)
    {
        parent::onCompoundToken($token, $value);
        if ($value === "?>\n") {
            $value = "?>\n\n";
        }
    }
}
