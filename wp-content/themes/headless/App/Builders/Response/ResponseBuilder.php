<?php

include_once __DIR__ . '/IResponse.php';

/**
 * Response builders default implementation
 * 
 * @package Builders
 * @since 1.0
 */
class ResponseBuilder implements IResponse
{
    /**
     * Create a success response
     * 
     * @param mixed $data
     * @return WP_REST_Response
     */
    public static function success($data): \WP_REST_Response
    {
        return new \WP_REST_Response($data);
    }

    /**
     * Create a error response
     * 
     * @param string $userMessage
     * @param string $developerMessage
     * @param int $code
     * @param \Throwable $ex
     * @return WP_REST_Response
     */
    public static function error(string $userMessage, string $developerMessage, int $code, \Throwable $ex): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'userMessage' => $userMessage,
            'developerMessage' => $developerMessage,
            'exception' => $ex,
            'code' => $code
        ], $code);
    }
}
