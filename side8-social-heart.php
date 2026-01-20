<?php
/**
 * Plugin Name: Side 8 Social Heart
 * Description: Front-end social content approval portal for Side 8 Social Heart.
 * Version: 0.1.0
 * Author: Side 8
 * Text Domain: side8-social-heart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/includes/class-side8-constants.php';
require_once __DIR__ . '/includes/class-side8-activator.php';
require_once __DIR__ . '/includes/class-side8-settings.php';
require_once __DIR__ . '/includes/class-side8-shortcode.php';
require_once __DIR__ . '/includes/class-side8-api-client.php';
require_once __DIR__ . '/includes/class-side8-rest-routes.php';
require_once __DIR__ . '/includes/class-side8-storage.php';
require_once __DIR__ . '/includes/class-side8-security.php';

register_activation_hook( __FILE__, array( 'Side8_Social_Heart_Activator', 'activate' ) );

add_action( 'init', array( 'Side8_Social_Heart_Storage', 'register_post_types' ) );
add_action( 'init', array( 'Side8_Social_Heart_Shortcode', 'register' ) );
add_action( 'admin_menu', array( 'Side8_Social_Heart_Settings', 'register_menu' ) );
add_action( 'admin_init', array( 'Side8_Social_Heart_Settings', 'register_settings' ) );
add_action( 'rest_api_init', array( 'Side8_Social_Heart_Rest_Routes', 'register_routes' ) );
