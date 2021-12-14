<?php
/**
 * This is the hub which decides which text will be sent
 */
class JohnCoffee_Next_Action {
	public static function launch() {
		// Search through users to find which ones need to be called
		date_default_timezone_set( 'Europe/Paris' );
		$today_date = new DateTime();
		global $wpdb;
		
		$users = $wpdb->get_results(
			'SELECT * FROM ' .$wpdb->prefix. 'users'
		);

		$buffer = array();
		foreach ( $users as $user ) {
			$user_profile = new JohnCoffee_User_Profile( $user->ID );
			$index = 1;
			while ( $user_profile->has_channel_index( $index ) ) {
				$chat = new JohnCoffee_Chat( $user->ID, $index );
				if ( $chat->should_ask_today_random_question() ) {
					array_push( $buffer, $chat->chat_with_hook() );
					date_default_timezone_set( 'Europe/Paris' );
					$user_profile_channel = new JohnCoffee_User_Profile_Channel( $user->ID, $index );
					$user_profile_channel->update_last_random_question_datetime( $today_date->format( 'Y-m-d H:i:s' ) );
				}
				$index++;
			}
		}
		return $buffer;
	}
}