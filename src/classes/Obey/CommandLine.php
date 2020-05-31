<?php


namespace Obey;

class CommandLine
{
    private $argv;

    public function __construct(array $argv)
    {
        $this->argv = $argv;
    }

    public static function parse(array $argv) : array
    {
        $me = new self($argv);
        return $me->parseArguments();
    }
    protected function parseArguments() : array
    {
        $options = [
            'op' => 'process'
        ];
        while (count($this->argv)) {
            switch ($opt = array_shift($this->argv)) {

                case '-f': $this->assignOnce($options, 'setupFile'); break;
                case '-d': $this->assign($options, 'rootDir'); break;
                case '-o': $this->assign($options, 'outputDir'); break;
                case '-n': $this->assign($options, 'outputNameTemplate'); break;
                case '-s': $this->assign($options, 'style'); break;
                case '--list-inputs': $options['op'] = 'list-inputs'; break;
                case '--list-outputs': $options['op'] = 'list-outputs'; break;
                default: trigger_error("Unsupported option {$opt}", E_USER_ERROR);
            }
        }
        return $options;
    }

    protected function assignOnce(array &$options, string $key)
    {
        assert(null===($options['key']??null));
        $this->assign($options, $key);
    }

    protected function assign(array &$options, string $key)
    {
        assert(count($this->argv));
        $options[$key] = array_shift($this->argv);
    }
}
