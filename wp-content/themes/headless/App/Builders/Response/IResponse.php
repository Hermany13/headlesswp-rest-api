<?php

interface IResponse
{
    public static function success($data): \WP_REST_Response;
    public static function error(string $userMessage, string $developerMessage, int $code, \Throwable $ex): \WP_REST_Response;
}
