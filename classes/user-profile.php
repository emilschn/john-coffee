<?php
/**
 * Data added to the user profile
 */
class JohnCoffee_User_Profile {
	private $user_id;
	
	public static $questions_asked_previously_meta = 'questions_asked_previously';
	private $list_questions_asked_previously;

	public static $slack_webhook_url_meta = 'slack_webhook_url';
	private $slack_webhook_url;

	public static $slack_channel_question_meta = 'slack_channel_question';
	private $slack_channel_question;

	public static $slack_bot_language_meta = 'slack_bot_language';
	private $slack_bot_language;

	public static $slack_timezone_meta = 'slack_timezone';
	private $slack_timezone;

	public static $slack_time_meta = 'slack_time';
	private $slack_time_hours;
	private $slack_time_minutes;

	public static $slack_days_meta = 'slack_days';
	private $slack_days;

	public static $last_random_question_datetime_meta = 'last_random_question_date';
	private $last_random_question_datetime;
	
	public function __construct( $user_id ) {
		$this->user_id = $user_id;
	}

	/**
	 * Helpers to access/update user meta
	 */
	public function get_meta( $meta_name ) {
		return get_user_meta( $this->user_id, $meta_name, TRUE );
	}
	public function update_meta( $meta_name, $meta_value ) {
		update_user_meta( $this->user_id, $meta_name, $meta_value );
	}

	/**
	 * Can't ask question if already asked and if the delay has not passed
	 */
	public function get_questions_asked_previously() {
		if ( empty( $this->list_questions_asked_previously ) ) {
			$saved_meta = $this->get_meta( self::$questions_asked_previously_meta );
			$this->list_questions_asked_previously = json_decode( $saved_meta, TRUE );
		}
		return $this->list_questions_asked_previously;
	}

	public function update_questions_asked_previously( $question_id ) {
		$date_time = new DateTime();
		$list_questions_asked_previously = $this->get_questions_asked_previously();
		$list_questions_asked_previously[ $question_id ] = $date_time->format( 'Y-m-d' );
		$this->update_meta( self::$questions_asked_previously_meta, json_encode( $list_questions_asked_previously ) );

	}
	
	public function can_ask_question_id( $question_id ) {
		$list_questions_asked_previously = $this->get_questions_asked_previously();
		return empty( $list_questions_asked_previously[ $question_id ] );
	}
	
	/**
	 * Slack Webhook URL
	 */
	public function get_slack_webhook_url() {
		if ( empty( $this->slack_webhook_url ) ) {
			$this->slack_webhook_url = $this->get_meta( self::$slack_webhook_url_meta );
		}
		return $this->slack_webhook_url;
	}

	public function update_slack_webhook_url( $new_webhook_url ) {
		$this->slack_webhook_url = $new_webhook_url;
		$this->update_meta( self::$slack_webhook_url_meta, $new_webhook_url );
	}
	
	/**
	 * Name of the Slack channel
	 */
	public function get_slack_channel_question() {
		if ( empty( $this->slack_channel_question ) ) {
			$this->slack_channel_question = $this->get_meta( self::$slack_channel_question_meta );
		}
		return $this->slack_channel_question;
	}

	public function update_slack_channel_question( $new_channel ) {
		$this->slack_channel_question = $new_channel;
		$this->update_meta( self::$slack_channel_question_meta, $new_channel );
	}

	/**
	 * The bot language
	 */
	public function get_bot_language() {
		if ( empty( $this->slack_bot_language ) ) {
			$this->slack_bot_language = $this->get_meta( self::$slack_bot_language_meta );
		}
		return $this->slack_bot_language;
	}

	public function update_bot_language( $new_bot_language ) {
		$this->slack_bot_language = $new_bot_language;
		$this->update_meta( self::$slack_bot_language_meta, $new_bot_language );
	}

	/**
	 * The timezone
	 */
	public function get_timezone() {
		if ( empty( $this->slack_timezone ) ) {
			$this->slack_timezone = $this->get_meta( self::$slack_timezone_meta );
		}
		if ( empty( $this->slack_timezone ) ) {
			$this->slack_timezone = 'Europe/Paris';
		}
		return $this->slack_timezone;
	}

	public function update_timezone( $new_timezone ) {
		$this->slack_timezone = $new_timezone;
		$this->update_meta( self::$slack_timezone_meta, $new_timezone );
	}

	/**
	 * The messages time
	 */
	public function get_message_time() {
		return $this->get_meta( self::$slack_time_meta );
	}
	public function get_message_time_hours() {
		if ( empty( $this->slack_time_hours ) ) {
			$message_time = $this->get_message_time();
			if ( !empty( $message_time ) ) {
				$date_time = DateTime::createFromFormat( 'H:i', $message_time );
				$this->slack_time_hours = $date_time->format( 'H' );
			}
		}
		return $this->slack_time_hours;
	}
	public function get_message_time_minutes() {
		if ( empty( $this->slack_time_minutes ) ) {
			$message_time = $this->get_message_time();
			if ( !empty( $message_time ) ) {
				$date_time = DateTime::createFromFormat( 'H:i', $message_time );
				$this->slack_time_minutes = $date_time->format( 'i' );
			}
		}
		return $this->slack_time_minutes;
	}

	public function update_message_time( $new_message_time_hours, $new_message_time_minutes ) {
		$this->slack_time_hours = $new_message_time_hours;
		$this->slack_time_minutes = $new_message_time_minutes;
		$this->update_meta( self::$slack_time_meta, $new_message_time_hours . ':' . $new_message_time_minutes );
	}

	/**
	 * The messages days
	 */
	public function can_send_when_day( $day_number ) {
		if ( empty( $this->slack_days ) ) {
			$saved_meta = $this->get_meta( self::$slack_days_meta );
			if ( empty( $saved_meta ) ) {
				$this->slack_days = array(
					'1' => '1',
					'2' => '1',
					'3' => '1',
					'4' => '1',
					'5' => '1',
					'6' => '0',
					'7' => '0'
				);
			} else {
				$this->slack_days = json_decode( $saved_meta, TRUE );
			}
		}
		return $this->slack_days[ $day_number ];
	}

	public function update_message_days( $days_list ) {
		$this->slack_days = $days_list;
		$this->update_meta( self::$slack_days_meta, json_encode( $days_list ) );
	}

	/**
	 * Last time the random question was asked on Slack
	 */
	public function get_last_random_question_datetime() {
		if ( empty( $this->last_random_question_datetime ) ) {
			$this->last_random_question_datetime = $this->get_meta( self::$last_random_question_datetime_meta );
		}
		return $this->last_random_question_datetime;
	}

	public function update_last_random_question_datetime( $new_datetime ) {
		$this->last_random_question_datetime = $new_datetime;
		$this->update_meta( self::$last_random_question_datetime_meta, $new_datetime );
	}
}