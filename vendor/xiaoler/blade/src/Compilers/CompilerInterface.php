<?php

namespace Xiaoler\Blade\Compilers;

interface CompilerInterface
{
    /**
     * Get the path to the compiled version of a views.
     *
     * @param  string  $path
     * @return string
     */
    public function getCompiledPath($path);

    /**
     * Determine if the given views is expired.
     *
     * @param  string  $path
     * @return bool
     */
    public function isExpired($path);

    /**
     * Compile the views at the given path.
     *
     * @param  string  $path
     * @return void
     */
    public function compile($path);
}
