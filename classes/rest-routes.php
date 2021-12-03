<?php
/**
 * Routes which will be called by CRON tasks
 */
class JohnCoffee_Rest_Routes {
	/**
	 * Singleton avoids multiple load
	 */
	private static $instance = null;
	public static function instance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new JohnCoffee_Rest_Routes;
			self::$instance->add_actions();
		}
		return self::$instance;
	}

	public function add_actions() {
		add_action( 'rest_api_init', 'JohnCoffee_Rest_Routes::register_routes' );
	}

	// http://coffee.local/wp-json/johncoffee/v1/call-next
	public static function register_routes() {
		register_rest_route(
			'johncoffee/v1',
			'/call-next',
			array(
				'methods' => 'GET',
				'callback' => 'JohnCoffee_Rest_Routes::call_next'
			)
		);
	}

	public static function call_next() {
		return JohnCoffee_Next_Action::launch();
	}
}