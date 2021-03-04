<?php


namespace Obey;

class CommandLine
{
    private array $argv;

    const LIST_OPTIONS = [
        '--list-inputs',
        '--list-outputs',
        '--list-patterns',
        '--list-include-patterns'
    ];

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
            if ($this->interpretListOption($options, $opt = array_shift($this->argv))) continue;
            switch ($opt) {

                case '-f': $this->assignOnce($options, 'setupFile'); break;
                case '-d': $this->assign($options, 'rootDir'); break;
                case '-o': $this->assign($options, 'outputDir'); break;
                case '-n': $this->assign($options, 'outputNameTemplate'); break;
                case '-s': $this->assign($options, 'style'); break;
                case '--oneline': $options['oneline'] = true; break;
                default: trigger_error("Unsupported option {$opt}", E_USER_ERROR);
            }
        }
        return $options;
    }

    protected function interpretListOption(array &$options, string $option) : bool
    {
        if (!in_array($option, self::LIST_OPTIONS)) return false;
        $ops = array_map(fn($v)=>ltrim($v,'-'), self::LIST_OPTIONS);
        if (in_array($options['op'], $ops)) {
            trigger_error(
                "At most most one of the following operations can be requested: ".implode(", ", self::LIST_OPTIONS)
            );
        }
        $options['op'] = ltrim($option, '-');
        $this->assign($options, 'listRelTo');
        return true;
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
