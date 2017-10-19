<?php

namespace Xiaoler\Blade\Engines;

abstract class Engine
{
    /**
     * The views that was last to be rendered.
     *
     * @var string
     */
    protected $lastRendered;

    /**
     * Get the last views that was rendered.
     *
     * @return string
     */
    public function getLastRendered()
    {
        return $this->lastRendered;
    }
}
