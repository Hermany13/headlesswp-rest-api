<?php

include_once __DIR__ . "/../Interfaces/IApi.php";
include_once __DIR__ . "/../../Builders/Response/ResponseBuilder.php";

class Users implements IApi
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_endpoint']);
    }

    public function register_endpoint()
    {
        register_rest_route('custom/v1', '/signup', array(
            'methods' => 'POST',
            'callback' => [$this, 'user_signup']
        ));
    }

    public function user_signup(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            // Get Request Data
            $username = $request->get_param('username');
            $email = $request->get_param('email');
            $password = $request->get_param('password');
            $roles = $request->get_param('role');
            $first_name = $request->get_param('firstName');
            $last_name = $request->get_param('lastName');


            // Create User and get the ID
            $user = wp_create_user($username, $password, $email);
            $newUser = new WP_User($user);
            $newUser->set_role($roles);

            // Update user metadata 
            update_user_meta($user, 'nickname', $username);
            update_user_meta($user, 'first_name', $first_name);
            update_user_meta($user, 'last_name', $last_name);
            update_user_meta($user, 'wp_user_level', '0');
            
            $userResponse = get_user_by('id', $user);

            return ResponseBuilder::success($userResponse);
        } catch (\Exception $ex) {
            return ResponseBuilder::error(
                'Ocorreu um error, tente novamente mais tarde',
                $ex->getMessage(),
                500,
                $ex
            );
        }     
}