<?php
/*
Edits the user profile in the back-end
*/
class JohnCoffee_Admin_User_Profile {
	public function __construct() {
		$this->add_actions();
	}

	private function add_actions() {
		add_action( 'show_user_profile', 'JohnCoffee_Admin_User_Profile::display_user_options' );
		add_action( 'edit_user_profile', 'JohnCoffee_Admin_User_Profile::display_user_options' );
		add_action( 'personal_options_update', 'JohnCoffee_Admin_User_Profile::save_user_options' );
		add_action( 'edit_user_profile_update', 'JohnCoffee_Admin_User_Profile::save_user_options' );
	}

	public static function display_user_options( $profileuser ) {
		$plugin_url = plugin_dir_url( __FILE__ ) . '..';
		$user_profile = new JohnCoffee_User_Profile( $profileuser->ID );
		?>
		<h2><?php _e( 'John Coffee Slack Management', 'johncoffee' ); ?></h2>
		<table class="form-table">
			<tr>
				<th>
					<label for="user_slack_webhook_url"><?php _e( 'Slack Webhook URL', 'johncoffee' ); ?></label>
				</th>
				<td>
					<input type="text" name="user_slack_webhook_url" value="<?php echo $user_profile->get_slack_webhook_url(); ?>" class="regular-text" />
					<br>
					<p class="description"><?php _e( 'The webhook URL provided by Slack', 'johncoffee' ); ?></p>
				</td>
			</tr>
		</table>

		<table class="form-table">
			<tr>
				<th>
					<label for="user_slack_channel_question"><?php _e( 'Slack question channel', 'johncoffee' ); ?></label>
				</th>
				<td>
					<input type="text" name="user_slack_channel_question" value="<?php echo $user_profile->get_slack_channel_question(); ?>" class="regular-text" />
					<br>
					<p class="description"><?php _e( 'The name of the channel where random questions are posted. You need to specify this channel when creating the Webhook.', 'johncoffee' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}
	
	public static function save_user_options( $user_id ) {
		if ( current_user_can( 'edit_user', $user_id ) ) {
			$user_profile = new JohnCoffee_User_Profile( $user_id );
			$input_user_slack_webhook_url = esc_attr( sanitize_text_field( filter_input( INPUT_POST, 'user_slack_webhook_url' ) ) );
			$user_profile->update_slack_webhook_url( $input_user_slack_webhook_url );
			$input_user_slack_channel_question = esc_attr( sanitize_text_field( filter_input( INPUT_POST, 'user_slack_channel_question' ) ) );
			$user_profile->update_slack_channel_question( $input_user_slack_channel_question );
		}
	}
}