<?php


namespace Obey\Node;

use Obey\Node;
use Obey\Parser;

class Given extends Sequence
{
    /**
     * @var string
     */
    protected $condition;

    public function __construct(string $condition)
    {
        $this->condition = $condition;
    }

    public function isSatisfiedBy(Parser $parser) : bool
    {
        return self::containsPrintableText($parser->queryLoci($this->condition), $parser);
    }

    /**
     * @param Node[] $nodes
     * @param Parser $parser
     * @return bool
     */
    protected static function containsPrintableText(array $nodes, Parser $parser)
    {
        foreach ($nodes as $node) {
            switch (true) {

                case $node instanceof Sequence:
                    if (self::containsPrintableText($node->getNodes(), $parser)) {
                        return true;
                    }
                    break;
                case $node instanceof Text:
                    if (strlen(trim($node->getContent()))) {
                        return true;
                    }
                    break;

                case $node instanceof Here:
                    if (self::containsPrintableText($node->getContent()->getNodes(), $parser)) {
                        return true;
                    };
                    break;
                case $node instanceof Given:
                    if ($node->isSatisfiedBy($parser)) {
                        return true;
                    };
                    break;

                default:
                    break;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }
}
