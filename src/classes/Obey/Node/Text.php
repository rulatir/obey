<?php


namespace Obey\Node;

use Obey\Node;

class Text extends Node
{
    const TAB_SIZE = 4;
    /** @var string */
    protected $content;


    public function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    public function getRealIndent() : ?int
    {
        return self::getRealIndentOf($this->getContent());
    }

    public static function getRealIndentOf(string $text): ?int
    {
        if (preg_match("/^\\s*$/", $text)) {
            return null;
        }
        $indent=null;
        foreach ($lines = explode("\n", $text) as $line) {
            $matches = [];
            if (preg_match("~^(\\s*)(\\S.*)$~", $line, $matches)) {
                $indent = min($indent ?? PHP_INT_MAX, self::computeIndentLength($matches[1]));
                continue;
            }
        };
        return $indent;
    }

    public static function computeIndentLength(string $initialWhiteSpaceString)
    {
        $chars = preg_split("//u", $initialWhiteSpaceString, -1, PREG_SPLIT_NO_EMPTY);
        $distinct = array_keys($counts = array_count_values($chars));
        if (1===count($distinct)) {
            switch ($distinct[0]) {
                case " ": return $counts[" "];
                case "\t": return self::TAB_SIZE * $counts["\t"];
                default: return 0;
            }
        }
        $n=0;
        foreach ($chars as $character) {
            switch ($character) {
                case " ":
                    ++$n; break;
                case "\t":
                    $n = self::TAB_SIZE * ($n % self::TAB_SIZE + 1); break;
                default:
                    return $n;
            }
        }
        return $n;
    }
}
