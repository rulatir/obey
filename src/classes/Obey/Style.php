<?php


namespace Obey;

use Obey\Front\Obtainer\DirectObtainer;
use Obey\Front\Obtainer\NewlineReinserter;
use Obey\Renderer\OutdentingRenderer;
use Obey\Renderer\SmartRenderer;

class Style
{
    const PRESETS = [
        '__base' => [
            'rendererOpts' => [
                'tabSize' => 4
            ]
        ],
        'outdent' => [
            'obtainer' => DirectObtainer::class,
            'renderer' => OutdentingRenderer::class
        ],
        'smart' => [
            'obtainer' => NewlineReinserter::class,
            'renderer' => SmartRenderer::class,
            'rendererOpts' => [
                'rules' => [
                    "\\{\$" => [0,1],
                    "^\\}\$" => [-1,0],
                ]
            ]
        ],
        'default' => 'smart',
        'dockerfile' => 'outdent',
        'openresty' => [
            'extends' => 'smart',
            'rendererOpts' => [
                'rules' => [
                    ";\$" => [0,0],
                    "(\\{|(\b(do|then|else)))\$" => [0,1],
                    "^(\\}|(\b(end)))\$" => [-1,0],
                ]
            ]
        ]
    ];

    const OPTIONS = [
        'tabSize',
        'obtainer',
        'renderer',
        'obtainerOpts',
        'rendererOpts'
    ];

    private static $presets;

    /** @var int */
    private $tabSize;

    /** @var array */
    private $obtainerOpts;

    /** @var array */
    private $rendererOpts;

    /** @var string */
    private $obtainer;

    /** @var string */
    private $renderer;

    public function __construct($preset)
    {
        $this->setOptions(static::resolvePreset($preset));
    }

    /**
     * @return int
     */
    public function getTabSize(): int
    {
        return $this->tabSize;
    }

    /**
     * @param int $tabSize
     */
    public function setTabSize(int $tabSize): void
    {
        $this->tabSize = $tabSize;
    }

    /**
     * @return array
     */
    public function getObtainerOpts(): array
    {
        return $this->obtainerOpts;
    }

    /**
     * @param array $obtainerOpts
     */
    public function setObtainerOpts(array $obtainerOpts): void
    {
        $this->obtainerOpts = $obtainerOpts;
    }

    /**
     * @return array
     */
    public function getRendererOpts(): array
    {
        return $this->rendererOpts;
    }

    /**
     * @param array $rendererOpts
     */
    public function setRendererOpts(array $rendererOpts): void
    {
        $this->rendererOpts = $rendererOpts;
    }

    /**
     * @return string
     */
    public function getObtainer(): string
    {
        return $this->obtainer;
    }

    /**
     * @param string $obtainer
     */
    public function setObtainer(string $obtainer): void
    {
        $this->obtainer = $obtainer;
    }

    /**
     * @return string
     */
    public function getRenderer(): string
    {
        return $this->renderer;
    }

    /**
     * @param string $renderer
     */
    public function setRenderer(string $renderer): void
    {
        $this->renderer = $renderer;
    }

    public function setOptions(array $options) : array
    {
        return OptionsHelper::setOptions($this, $options);
    }

    public static function resolvePreset($preset)
    {
        if (is_array($preset)) {
            return $preset;
        }
        $presets = self::getPresets();
        $presetName = null;
        while (is_string($preset)) {
            $presetName = $preset;
            $preset = $presets[$presetName] ?? $presets['default'];
        }
        if ('__base' !== $presetName && !isset($preset['extends'])) {
            $preset['extends'] = '__base';
        }
        if (isset($preset['extends'])) {
            $parent = self::resolvePreset($preset['extends']);
            unset($preset['extends']);
            $preset = array_merge($parent, $preset);
            $preset['obtainerOpts'] = array_merge($parent['obtainerOpts'] ?? [], $preset['obtainerOpts'] ?? []);
            $preset['rendererOpts'] = array_merge($parent['rendererOpts'] ?? [], $preset['rendererOpts'] ?? []);
        }
        return $preset;
    }

    /**
     * @return mixed
     */
    public static function getPresets()
    {
        return self::$presets ?? self::$presets = self::PRESETS;
    }

    public static function addPreset(string $name, $definition)
    {
        self::$presets = array_merge(self::getPresets(), [$name => $definition]);
    }
}
