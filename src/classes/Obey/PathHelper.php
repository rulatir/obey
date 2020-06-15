<?php


namespace Obey;

class PathHelper
{
    public static function cat(?string ...$fragments) : string
    {
        $nonNullFragments = array_filter($fragments,fn($v) => null!==$v);
        if (0===count($nonNullFragments)) return ".";
        $explodedFragments = [
            self::split($nonNullFragments[0]),
            ...array_map(fn($fragment) => self::split($fragment,false), array_slice($nonNullFragments,1))
        ];
        $segments = array_merge(...$explodedFragments);
        if (!count($segments)) return ".";
        return implode("/",$segments);
    }
    public static function isAbsolute(string $segment) : bool
    {
        //TODO: support windows paths?
        return preg_match('|^/|', $segment);
    }
    public static function norm(?string ...$fragments) : string
    {
        $explodedFragments = array_map(self::class.'::split',$fragments);
        $segments = array_merge(...$explodedFragments);
        $up=0;
        $result=[];
        foreach($segments as $segment) {
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
     * @param bool $allowAbsolute
     * @return string[]
     */
    public static function split(?string $path, bool $allowAbsolute = true) : array
    {
        $trimmedPath = trim($path);
        if (""===$trimmedPath) return [];
        $wasAbsolute = $allowAbsolute && "/"===$trimmedPath[0] ?? null;
        $result = array_filter(explode("/",$trimmedPath), fn($v) => ""!==trim($v));
        return $wasAbsolute ? ["",...$result] : $result;
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
