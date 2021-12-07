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

		<table class="form-table">
			<tr>
				<th>
					<label for="user_bot_language"><?php _e( 'Bot language', 'johncoffee' ); ?></label>
				</th>
				<td>
					<select name="user_bot_language">
						<option value="fr" <?php selected( $user_profile->get_bot_language() == 'fr' ) ?>>Français</option>
						<option value="en" <?php selected( $user_profile->get_bot_language() == 'en' ) ?>>English</option>
					</select>
					<br>
					<p class="description"><?php _e( 'The language in which the bot will ask questions.', 'johncoffee' ); ?></p>
				</td>
			</tr>
		</table>

		<table class="form-table">
			<tr>
				<th>
					<label for="user_timezone"><?php _e( 'Timezone', 'johncoffee' ); ?></label>
				</th>
				<td>
					<select name="user_timezone" aria-describedby="timezone-description">
						<?php echo wp_timezone_choice( $user_profile->get_timezone(), get_user_locale() ); ?>
					</select>
					<br>
					<p id="timezone-description" class="description"><?php _e( 'Choose either a city in the same timezone as you or a UTC (Coordinated Universal Time) time offset.', 'johncoffee' ); ?></p>
				</td>
			</tr>
		</table>

		<table class="form-table">
			<tr>
				<th>
					<label for="user_message_time_hour"><?php _e( 'Time when to send messages', 'johncoffee' ); ?></label>
				</th>
				<td>
					<select name="user_message_time_hours">
						<?php for ( $i = 0; $i < 24; $i++ ): ?>
							<option value="<?php echo str_pad( $i, 2, '0', STR_PAD_LEFT ); ?>" <?php selected( $i == $user_profile->get_message_time_hours() ) ?>><?php echo str_pad( $i, 2, '0', STR_PAD_LEFT ); ?></option>
						<?php endfor; ?>
					</select>
					&nbsp;:&nbsp;
					<select name="user_message_time_minutes">
						<?php for ( $i = 0; $i < 60; $i++ ): ?>
							<option value="<?php echo str_pad( $i, 2, '0', STR_PAD_LEFT ); ?>" <?php selected( $i == $user_profile->get_message_time_minutes() ) ?>><?php echo str_pad( $i, 2, '0', STR_PAD_LEFT ); ?></option>
						<?php endfor; ?>
					</select>
					<br>
					<p class="description"><?php _e( 'The time at which messages are sent.', 'johncoffee' ); ?></p>
				</td>
			</tr>
		</table>

		<table class="form-table">
			<tr>
				<th>
					<label for="user_message_day"><?php _e( 'Days when to send messages', 'johncoffee' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="message_day_1" value="1" <?php checked( $user_profile->can_send_when_day( 1 ) ); ?>><?php _e( 'Monday', 'johncoffee' ); ?><br>
					<input type="checkbox" name="message_day_2" value="2" <?php checked( $user_profile->can_send_when_day( 2 ) ); ?>><?php _e( 'Tuesday', 'johncoffee' ); ?><br>
					<input type="checkbox" name="message_day_3" value="3" <?php checked( $user_profile->can_send_when_day( 3 ) ); ?>><?php _e( 'Wednesday', 'johncoffee' ); ?><br>
					<input type="checkbox" name="message_day_4" value="4" <?php checked( $user_profile->can_send_when_day( 4 ) ); ?>><?php _e( 'Thursday', 'johncoffee' ); ?><br>
					<input type="checkbox" name="message_day_5" value="5" <?php checked( $user_profile->can_send_when_day( 5 ) ); ?>><?php _e( 'Friday', 'johncoffee' ); ?><br>
					<input type="checkbox" name="message_day_6" value="6" <?php checked( $user_profile->can_send_when_day( 6 ) ); ?>><?php _e( 'Saturday', 'johncoffee' ); ?><br>
					<input type="checkbox" name="message_day_7" value="7" <?php checked( $user_profile->can_send_when_day( 7 ) ); ?>><?php _e( 'Sunday', 'johncoffee' ); ?><br>
					<br>
					<p class="description"><?php _e( 'The days when messages are sent.', 'johncoffee' ); ?></p>
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
			$input_user_bot_language = esc_attr( sanitize_text_field( filter_input( INPUT_POST, 'user_bot_language' ) ) );
			$user_profile->update_bot_language( $input_user_bot_language );
			$input_user_timezone = esc_attr( sanitize_text_field( filter_input( INPUT_POST, 'user_timezone' ) ) );
			$user_profile->update_timezone( $input_user_timezone );
			$user_message_time_hours = esc_attr( sanitize_text_field( filter_input( INPUT_POST, 'user_message_time_hours' ) ) );
			$user_message_time_minutes = esc_attr( sanitize_text_field( filter_input( INPUT_POST, 'user_message_time_minutes' ) ) );
			$user_profile->update_message_time( $user_message_time_hours, $user_message_time_minutes );
			$days_list = array();
			for ( $i = 1; $i <= 7; $i++ ) {
				$input_message_day = filter_input( INPUT_POST, 'message_day_' . $i );
				$days_list[ $i ] = ( !empty( $input_message_day ) ) ? '1' : '0';
				$user_profile->update_message_days( $days_list );
			}
		}
	}
}