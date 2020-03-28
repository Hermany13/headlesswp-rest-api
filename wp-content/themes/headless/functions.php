<?php

require __DIR__ . '/App/Api/Init.php';

/**
 * Headless functions theme (Rest API Mode!)
 * 
 * @author Object1ve <dev@object1ve.com>
 * @since 1.0
 */

/**
 * The current theme absolute directory path
 * 
 * @var string
 */
$theme_dir = get_template_directory();

/**
 * Start API endpoints
 * 
 * @see (https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/)
 */
Init::api();


/**
 * Themes support
 * 
 * @see (https://developer.wordpress.org/reference/functions/add_theme_support/)
 */
add_theme_support('menus');
add_theme_support('post-thumbnails');
