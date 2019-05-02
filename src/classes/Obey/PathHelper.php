<?php


namespace Obey;

class PathHelper
{
    public static function cat(?string ...$segments) : string
    {
        return implode("/", array_filter($segments, 'strlen'));
    }
    public static function isAbsolute(string $segment) : bool
    {
        //TODO: support windows paths?
        return preg_match('|^/|', $segment);
    }
}
