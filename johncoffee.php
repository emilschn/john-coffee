<?php
/*
Plugin Name: John Coffee
Description: This WordPress plugin connects to Slack to animate the chat!
Author: Emilien Schneider
Version: 0.0.1
Domain Path: /languages
*/

class JohnCoffee_Starter {
	/**
	 * Singleton avoids multiple load
	 */
	private static $instance = null;
	public static function instance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new JohnCoffee_Starter;
			self::$instance->init_admin();
			self::$instance->init_routes();
		}
		return self::$instance;
	}

	/**
	 * Load other useful files
	 */
	public function __construct() {
		$plugin_path = plugin_dir_path( __FILE__ );
		$classes_path = trailingslashit( $plugin_path . 'classes' );
		$admin_path = trailingslashit( $plugin_path . 'admin' );
		$language_path = trailingslashit( $plugin_path . 'languages' );

		// Login screen
		require_once $admin_path . 'login-screen.php';

		// User Profile management
		require_once $classes_path . 'user-profile.php';
		require_once $admin_path . 'user-profile.php';

		// Random questions
		require_once $classes_path . 'random-questions.php';

		// Hubs to different actions
		require_once $classes_path . 'next-action.php';
		require_once $classes_path . 'chat.php';

		// REST routes
		require_once $classes_path . 'rest-routes.php';
	}

	private function init_admin() {
		new JohnCoffee_Admin_User_Profile();
		new JohnCoffee_Admin_Login_Screen();
	}

	private function init_routes() {
		JohnCoffee_Rest_Routes::instance();
	}
}
JohnCoffee_Starter::instance();