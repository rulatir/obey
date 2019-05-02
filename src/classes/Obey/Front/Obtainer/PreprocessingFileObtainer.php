<?php


namespace Obey\Front\Obtainer;

use Obey\Front\Obtainer;

abstract class PreprocessingFileObtainer extends FileObtainer
{
    private $cache = [];

    public function req(string $fname)
    {
        return require $this->cache[$fname] ?? $this->cache[$fname] = $this->load($fname);
    }

    protected function load($fname)
    {
        return "data://text/plain;base64,".base64_encode($this->convert($fname));
    }
    protected function convert($fname)
    {
        $result = [];
        $tokens = token_get_all(file_get_contents($fname));
        $this->beforeFile();

        foreach ($tokens as $token) {
            if (is_string($token)) {
                $value = $token;
                $this->onStringToken($token, $value);
                $result[] = $value;
            } else {
                $value = $token[1];
                $this->onCompoundToken($token, $value);
                $result[] = $value;
            }
        }
        $converted = trim(implode("", $result));
        return $converted;
    }

    protected function beforeFile(): void
    {
    }
    protected function onStringToken(string $token, string &$value)
    {
    }
    protected function onCompoundToken(array $token, string &$value)
    {
    }
}
