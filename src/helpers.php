<?php

if ( ! function_exists('config_path'))
{
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if ( ! function_exists('is_callback_function'))
{
    /**
     * Check if a callback function.
     *
     * @param  string $callback
     * @return string
     */
    function is_callback_function($callback)
    {
        return is_callable($callback) && is_object($callback) && $callback instanceof Closure;
    }
}

if (!function_exists('array_except')) {
    /**
     * Provide array_except helper. This is normally available in Laravel, however, not in Lumen.
     * Hendawy\Elasticsearch requires this
     *
     * @param array $array
     * @param array $exclude
     * @return array
     */
    function array_except(array $array, array $exclude)
    {
        return collect($array)->except($exclude)->toArray();
    }
}

if (! function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param  string  $path
     * @return string
     */
    function base_path($path = '')
    {
        return app()->basePath().($path ? '/'.$path : $path);
    }
}



