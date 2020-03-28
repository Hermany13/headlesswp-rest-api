<?php

include_once __DIR__ . '/../Interfaces/IApi.php';
include_once __DIR__ . '/../../Builders/Response/ResponseBuilder.php';

/**
 * All related menus endpoints
 * 
 * @package Menus
 * @since 1.0
 */
class Menus implements IApi
{
    public $className = __CLASS__;

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_endpoint']);
    }

    public function register_endpoint()
    {
        register_rest_route('custom/v1', '/menus', array(
            'methods' => 'GET',
            'callback' => [$this, 'get_menus'],
        ));
    }

    /**
     * Get menus and submenus
     * 
     * @since 1.0
     * 
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_menus(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            $name = $request->get_param('name');
            if (!$name) throw new \Exception("Name param is required", 401);
            return ResponseBuilder::success(wp_get_nav_menu_items($name));
        } catch (\Exception $ex) {
            return ResponseBuilder::error(
                'An error occurred, please try again later.',
                $ex->getMessage(),
                500,
                $ex
            );
        }
    }
}
new Menus();
