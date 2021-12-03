<?php
/*
Edits the login screen
*/
class JohnCoffee_Admin_Login_Screen {
	public function __construct() {
		$this->add_actions();
	}

	private function add_actions() {
		add_action( 'login_enqueue_scripts', 'JohnCoffee_Admin_Login_Screen::login_enqueue_scripts' );
	}

	public static function login_enqueue_scripts() {
		$plugin_url = plugin_dir_url( __FILE__ ) . '..';
		?>
		<style>
		body.login h1 a {
			background-image: url('<?php echo $plugin_url; ?>/assets/img/login.png');
		}
		</style>
		<?php
	}
}