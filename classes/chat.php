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

		unload_textdomain( 'johncoffee' );
		if ( $this->user_profile->get_bot_language() == 'fr' ) {
			$plugin_path = plugin_dir_path( __FILE__ ) . '..';
			$mofile = 'johncoffee-fr_FR.mo';
			$mopath = $plugin_path . '/languages/' . $mofile;
			load_textdomain( 'johncoffee', $mopath );
		}

		$message_to_send = JohnCoffee_Random_Question::get_funky();
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

	public function should_ask_today_random_question() {
		date_default_timezone_set( $this->user_profile->get_timezone() );
		$today_date = new DateTime();

		// On n'a jamais posé de question
		$last_question_date_str = $this->user_profile->get_last_random_question_datetime();
		if ( empty( $last_question_date_str ) ) {
			// On vérifie qu'on a dépassé l'heure d'envoi
			$message_time = $this->user_profile->get_message_time();
			$date_time_to_send = DateTime::createFromFormat( 'H:i', $message_time );
			if ( $date_time_to_send <= $today_date ) {
				return true;
			}
		}

		// On initialise la date de dernier envoi à Paris (fuseau d'enregistrement), puis on repasse sur le fuseau horaire de l'utilisateur
		date_default_timezone_set( 'Europe/Paris' );
		$last_question_date = new DateTime( $last_question_date_str );
		date_default_timezone_set( $this->user_profile->get_timezone() );
		// Si on est à une date qui dépasse d'un jour la date précédemment enregistrée
		if ( $today_date->diff( $last_question_date )->days > 0 ) {
			// On vérifie qu'on a dépassé l'heure d'envoi
			$message_time = $this->user_profile->get_message_time();
			$date_time_to_send = DateTime::createFromFormat( 'H:i', $message_time );
			if ( $date_time_to_send <= $today_date ) {
				return true;
			}
		}

		return false;
	}
}