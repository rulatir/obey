<?php


namespace Obey;

class PathHelper
{
    public static function cat(?string ...$segments) : string
    {
        return implode("/", array_filter($segments, fn($segment) => ""!==trim(strval($segment))));
    }
    public static function isAbsolute(string $segment) : bool
    {
        //TODO: support windows paths?
        return preg_match('|^/|', $segment);
    }
    public static function norm(?string ...$segments) : string
    {
        $explodedSegments = array_map(self::class.'::split',$segments);
        $allSegments = array_merge(...$explodedSegments);
        $up=0;
        $result=[];
        foreach($allSegments as $segment) {
            if ($segment===".") continue;
            elseif($segment==="..") {
                if (count($result)) array_pop($result);
                else ++$up;
            }
            else {
                $result[] = $segment;
            }
        }
        return implode("/", array_merge(array_fill(0,$up,'..'), $result));
    }

    /**
     * @param string $path
     * @return string[]
     */
    public static function split(?string $path) : array
    {
        return
            "" !== trim(strval($path))
                ? array_filter(explode("/",$path), fn($v) => ""!==trim(strval($v)))
                : [];
    }

    public static function unprefix(string $path, string $prefix) : string
    {
        $pathSegments = self::split(self::norm($path));
        $prefixSegments = self::split(self::norm($prefix));
        while(count($prefixSegments)) {
            if(($pathSegments[0]??null)!==$prefixSegments[0]) break;
            array_shift($prefixSegments);
            array_shift($pathSegments);
        }
        return self::norm(implode("/",array_merge(array_fill(0,count($prefixSegments),".."),$pathSegments)));
    }
}
