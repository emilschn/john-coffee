<?php
/**
 * This is the hub which decides which text will be sent
 */
class JohnCoffee_Chat {
	private $user_profile;
	private $channel_name;
	private $webhook_url;

	public function __construct( $user_id ) {
		$this->user_profile = new JohnCoffee_User_Profile( $user_id );
	}

	public function get_channel_name() {
		if ( !isset( $this->channel_name ) ) {
			$this->channel_name = $this->user_profile->get_slack_channel_question();
		}
		return $this->channel_name;
	}

	public function get_webhook_url() {
		if ( !isset( $this->webhook_url ) ) {
			$this->webhook_url = $this->user_profile->get_slack_webhook_url();
		}
		return $this->webhook_url;
	}

	public function chat_with_hook() {
		$webhook_url = $this->get_webhook_url();
		$room = $this->get_channel_name();
		$message_to_send = JohnCoffee_Random_Question::get_funky( FALSE, FALSE );
		$icon = FALSE;

		$parameters = array(
			'channel'	=> '#' . $room,
			'text'		=> $message_to_send
		);
		if ( !empty( $icon ) ) {
			$parameters[ 'icon_emoji' ] = $icon;
		}

		$data = "payload=" . json_encode( $parameters );

		$ch = curl_init( $webhook_url );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$result = curl_exec( $ch );
		$error = curl_error( $ch );
		$errorno = curl_errno( $ch );
		curl_close( $ch );
	}

	/*
	private function get_built_headers() {
		$headers = array(
			'Authorization'	=> 'Bearer ' .$this->get_token_id(),
			'Content-Type'	=> 'application/x-www-form-urlencoded'
		);
		return $headers;
	}

	private function get_users_list() {
		$api_url = 'https://slack.com/api/conversations.members';

		$api_url .= '?channel=' .$this->get_channel_id();

		$result = wp_remote_post( 
			$api_url, 
			array( 
				'headers'	=> $this->get_built_headers()
			)
		);
		
		if ( $result[ 'response' ][ 'code' ] == 200 ) {
			$result_body_decoded = json_decode( $result[ 'body' ] );
			if ( !empty( $result_body_decoded->ok ) && !empty( $result_body_decoded->members ) ) {
				return $result_body_decoded->members;
			}
		}

		return FALSE;
	}

	private function send_text( $text ) {
		$api_url = 'https://slack.com/api/chat.postMessage';

		$api_url .= '?channel=' .$this->get_channel_id();
		$api_url .= '&text=' .$text;

		$result = wp_remote_post( 
			$api_url,
			array(
				'headers'	=> $this->get_built_headers()
			)
		);
		
		if ( $result[ 'response' ][ 'code' ] == 200 ) {
			$result_body_decoded = json_decode( $result[ 'body' ] );
			if ( !empty( $result_body_decoded->ok ) ) {
				return TRUE;
			}
		}

		return FALSE;
	}

	public function chat_with_app() {
		$user_list = $this->get_users_list();
		if ( !empty( $user_list ) ) {
			$slack_number_persons = $this->user_profile->get_slack_number_persons();
			$message_to_send = JohnCoffee_Random_Question::get_funky( $user_list, $slack_number_persons );
			return $this->send_text( $message_to_send );
		}
	}
	*/

	public function has_asked_today_random_question() {
		$today_date = new DateTime();
		$last_question_date_str = $this->user_profile->get_last_random_question_datetime();
		if ( empty( $last_question_date_str ) ) {
			return false;
		}
		$last_question_date = new DateTime( $last_question_date_str );
		if ( $today_date->format( 'Y-m-d' ) == $last_question_date->format( 'Y-m-d' ) ) {
			return true;
		}
		return false;
	}
}