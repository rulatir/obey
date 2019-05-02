<?php


namespace Obey\Front\Obtainer;

use Obey\Front\Obtainer;

abstract class FileObtainer extends Obtainer
{
    public function exists(string $fname): bool
    {
        return file_exists(stream_resolve_include_path($fname));
    }
}
