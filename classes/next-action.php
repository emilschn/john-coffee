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
		
		$users_never_done = $wpdb->get_results(
			'SELECT * FROM ' .$wpdb->prefix. 'users'.
			' WHERE ' .$wpdb->prefix. 'users.ID NOT IN ('.
				' SELECT ' .$wpdb->prefix. 'usermeta.user_id FROM ' .$wpdb->prefix. 'usermeta'.
				' WHERE ' .$wpdb->prefix. 'usermeta.meta_key = \'' .JohnCoffee_User_Profile::$last_random_question_datetime_meta. '\''.
			')'
		);

		$users_already_done = $wpdb->get_results(
			'SELECT * FROM ' .$wpdb->prefix. 'users'.
			' INNER JOIN ' .$wpdb->prefix. 'usermeta'.
			' ON ' .$wpdb->prefix. 'users.ID = ' .$wpdb->prefix. 'usermeta.user_id'.
			' WHERE ' .$wpdb->prefix. 'usermeta.meta_key = "' .JohnCoffee_User_Profile::$last_random_question_datetime_meta. '"'.
			' AND ' .$wpdb->prefix. 'usermeta.meta_value < "' .$today_date->format( 'Y-m-d' ). '"'.
			' ORDER BY ' .$wpdb->prefix. 'usermeta.meta_value DESC'
		);

		$users = array_merge( $users_never_done, $users_already_done );

		$buffer = array();
		foreach ( $users as $user ) {
			$chat = new JohnCoffee_Chat( $user->ID );
			if ( $chat->should_ask_today_random_question() ) {
				array_push( $buffer, $chat->chat_with_hook() );
				$user_profile = new JohnCoffee_User_Profile( $user->ID );
				date_default_timezone_set( 'Europe/Paris' );
				$user_profile->update_last_random_question_datetime( $today_date->format( 'Y-m-d H:i:s' ) );
			}
		}
		
		return $buffer;
	}
}