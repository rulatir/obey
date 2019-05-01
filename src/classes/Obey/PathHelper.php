<?php


namespace Obey;

class PathHelper
{
    public static function cat(?string ...$segments) : string
    {
        return implode("/", array_filter($segments, 'strlen'));
    }
}
