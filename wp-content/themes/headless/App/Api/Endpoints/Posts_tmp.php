<?php

include_once __DIR__ . "/../Interfaces/IApi.php";
include_once __DIR__ . "/../../Builders/Response/ResponseBuilder.php";

/**
 * All related posts endpoints
 * 
 * @package Posts
 * @since 1.0
 */
class Posts_tmp implements IApi
{
  public $className = __CLASS__;

  public function __construct()
  {
    add_action('rest_api_init', [$this, 'register_endpoint']);
  }

  public function register_endpoint()
  {
    register_rest_route('custom/v1', '/post_meta', array(
      'methods' => 'POST',
      'callback' => [$this, 'get_posts_meta']
    ));
    register_rest_route('custom/v1', '/example', array(
      'methods' => 'POST',
      'callback' => [$this, 'example']
    ));
  }

  public function example(\WP_REST_Request $request): \WP_REST_Response
  {
    try {


      return ResponseBuilder::success('users');
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
