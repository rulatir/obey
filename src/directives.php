<?php

use Obey\Parser;
use Obey\Main;

function into($name=false) : string
{
    $parser = Parser::getInstance();
    if (is_string($name)) {
        $parser->beginInto($name);
    } elseif (is_bool($name)) {
        $locusName = $parser->endInto();
        if (true===$name) {
            $parser->here($locusName);
        }
    }
    return "";
}

function given(?string $condition=null) : string
{
    $parser = Parser::getInstance();
    if (is_string($condition)) {
        $parser->beginGiven($condition);
    } else {
        $parser->endGiven();
    }
    return "";
}

function quiet() : string
{
    return into('__QUIET__');
}

function loud(string $name) : string
{
    into();
    return here($name);
}

function here($name) : string
{
    Parser::getInstance()->here($name);
    return "";
}

function import($name) : string
{
    into("__IMPORTS__");
    Main::getInstance()->getImporter()->import($name);
    into();
    return "";
}

function req($name) : string
{
    ob_start();
    Main::getInstance()->getImporter()->import($name);
    return "\n".ob_get_clean()."\n";
}
