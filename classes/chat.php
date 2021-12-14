<?php
/**
 * This is the hub which decides which text will be sent
 */
class JohnCoffee_Chat {
	private $user_profile;
	private $user_profile_channel;
	private $channel_name;
	private $webhook_url;

	public function __construct( $user_id, $index ) {
		$this->user_profile = new JohnCoffee_User_Profile( $user_id, $index );
		$this->user_profile_channel = new JohnCoffee_User_Profile_Channel( $user_id, $index );
	}

	private function get_channel_name() {
		if ( !isset( $this->channel_name ) ) {
			$this->channel_name = $this->user_profile_channel->get_slack_channel_question();
		}
		return $this->channel_name;
	}

	private function get_webhook_url() {
		if ( !isset( $this->webhook_url ) ) {
			if ( $this->is_slack() ) {
				$this->webhook_url = $this->user_profile_channel->get_slack_webhook_url();
			} else {
				$this->webhook_url = $this->user_profile_channel->get_teams_webhook_url();
			}
		}
		return $this->webhook_url;
	}

	private function is_slack() {
		$slack_webhook_url = $this->user_profile_channel->get_slack_webhook_url();
		return !empty( $slack_webhook_url );
	}


	// doc teams : https://docs.microsoft.com/fr-fr/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook
	public function chat_with_hook() {
		// Init user language
		unload_textdomain( 'johncoffee' );
		if ( $this->user_profile_channel->get_bot_language() == 'fr' ) {
			$plugin_path = plugin_dir_path( __FILE__ ) . '..';
			$mofile = 'johncoffee-fr_FR.mo';
			$mopath = $plugin_path . '/languages/' . $mofile;
			load_textdomain( 'johncoffee', $mopath );
		}

		// Init text to send
		$random_question = new JohnCoffee_Random_Question( $this->user_profile_channel->get_questions_asked_previously() );
		$message_to_send = $random_question->get_message();
		$this->user_profile_channel->update_questions_asked_previously( $random_question->get_id_choosen() );
		
		// Init request parameters
		$webhook_url = $this->get_webhook_url();
		$ch = curl_init( $webhook_url );

		$parameters = array(
			'text'		=> $message_to_send
		);

		if ( $this->is_slack() ) {
			$room = $this->get_channel_name();
			$parameters[ 'channel' ] = '#' . $room;
			$icon = FALSE;
			if ( !empty( $icon ) ) {
				$parameters[ 'icon_emoji' ] = $icon;
			}
			$data = "payload=" . json_encode( $parameters );

		} else {
			// curl.exe -H "Content-Type:application/json" -d "{'text':'Hello World'}" <YOUR WEBHOOK URL>
			$data = json_encode( $parameters );
			curl_setopt($ch, CURLOPT_POST, 1);

			$headers = array();
			$headers[] = 'Content-Type: application/json';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		// Do request
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$result = curl_exec( $ch );
		$error = curl_error( $ch );
		$errorno = curl_errno( $ch );
		curl_close( $ch );
	}

	public function should_ask_today_random_question() {
		// Jamais si l'utilisateur est désactivé
		if ( $this->user_profile->get_status() == 'disabled' ) {
			return false;
		}

		date_default_timezone_set( $this->user_profile_channel->get_timezone() );
		$today_date = new DateTime();

		// On n'a jamais posé de question
		$last_question_date_str = $this->user_profile_channel->get_last_random_question_datetime();
		if ( empty( $last_question_date_str ) ) {
			// On vérifie qu'on a dépassé l'heure d'envoi
			$message_time = $this->user_profile_channel->get_message_time();
			$date_time_to_send = DateTime::createFromFormat( 'H:i', $message_time );
			if ( $date_time_to_send <= $today_date ) {
				return true;
			}
		}

		// Est-ce qu'on peut envoyer un message aujourd'hui d'après les règlages ?
		if ( !$this->user_profile_channel->can_send_when_day( $today_date->format( 'N' ) ) ) {
			return false;
		}

		// On initialise la date de dernier envoi à Paris (fuseau d'enregistrement), puis on repasse sur le fuseau horaire de l'utilisateur
		date_default_timezone_set( 'Europe/Paris' );
		$last_question_date = new DateTime( $last_question_date_str );
		date_default_timezone_set( $this->user_profile_channel->get_timezone() );
		// Si on est à une date qui dépasse d'un jour la date précédemment enregistrée
		if ( $today_date->diff( $last_question_date )->days > 0 ) {
			// On vérifie qu'on a dépassé l'heure d'envoi
			$message_time = $this->user_profile_channel->get_message_time();
			$date_time_to_send = DateTime::createFromFormat( 'H:i', $message_time );
			if ( $date_time_to_send <= $today_date ) {
				return true;
			}
		}

		return false;
	}
}