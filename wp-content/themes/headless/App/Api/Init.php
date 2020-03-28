<?php

/**
 * 
 * @package Api
 * @since 1.0.0
 */
class Init
{
    /**
     * Register all endpoints in this theme
     */
    public static function api()
    {
        $endpointDir = __DIR__ . '/Endpoints/';
        $files = glob($endpointDir . '*.php');
        foreach ($files as $file) {
            $class = str_replace(
                '.php',
                '',
                str_replace($endpointDir, '', $file)
            );
            include_once $file;
            new $class();
        }
    }
}
