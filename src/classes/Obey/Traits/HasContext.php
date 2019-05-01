<?php


namespace Obey\Traits;

use Obey\Context;

trait HasContext
{
    /** @var Context */
    protected $context;

    protected function constructHasContext(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * @param Context $context
     */
    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    public function getRootDir() : string
    {
        return $this->getContext()->getRootDir();
    }

    public function getTabSize() : string
    {
        return $this->getContext()->getTabSize();
    }

    public function getOutputDir() : string
    {
        return $this->getContext()->getOutputDir();
    }
}
