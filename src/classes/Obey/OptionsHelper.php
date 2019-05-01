<?php


namespace Obey;

class OptionsHelper
{
    public static function setOptions($object, array $options) : array
    {
        $filtered = $options;
        $mask = [];
        $constant = get_class($object)."::OPTIONS";
        if (defined($constant)) {
            $mask = array_fill_keys(constant($constant), true);
        }
        if (count($mask)) {
            $filtered = array_intersect_key($filtered, $mask);
        }
        foreach ($filtered as $k => $v) {
            $setter = "set".ucfirst($k);
            if (method_exists($object, $setter)) {
                $object->$setter($v);
            }
        }
        return array_diff_key($options, $mask);
    }
}
