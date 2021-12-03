<?php
/**
 * Data added to the user profile
 */
class JohnCoffee_User_Profile {
	private $user_id;

	public static $slack_webhook_url_meta = 'slack_webhook_url';
	private $slack_webhook_url;

	public static $slack_channel_question_meta = 'slack_channel_question';
	private $slack_channel_question;

	public static $last_random_question_datetime_meta = 'last_random_question_date';
	private $last_random_question_datetime;
	
	public function __construct( $user_id ) {
		$this->user_id = $user_id;
	}
	
	/**
	 * Slack Webhook URL
	 */
	public function get_slack_webhook_url() {
		if ( empty( $this->slack_webhook_url ) ) {
			$this->slack_webhook_url = get_user_meta( $this->user_id, self::$slack_webhook_url_meta, TRUE );
		}
		return $this->slack_webhook_url;
	}

	public function update_slack_webhook_url( $new_webhook_url ) {
		$this->slack_webhook_url = $new_webhook_url;
		update_user_meta( $this->user_id, self::$slack_webhook_url_meta, $new_webhook_url );
	}
	
	/**
	 * Name of the Slack channel
	 */
	public function get_slack_channel_question() {
		if ( empty( $this->slack_channel_question ) ) {
			$this->slack_channel_question = get_user_meta( $this->user_id, self::$slack_channel_question_meta, TRUE );
		}
		return $this->slack_channel_question;
	}

	public function update_slack_channel_question( $new_channel ) {
		$this->slack_channel_question = $new_channel;
		update_user_meta( $this->user_id, self::$slack_channel_question_meta, $new_channel );
	}

	/**
	 * Last time the random question was asked on Slack
	 */
	public function get_last_random_question_datetime() {
		if ( empty( $this->last_random_question_datetime ) ) {
			$this->last_random_question_datetime = get_user_meta( $this->user_id, self::$last_random_question_datetime_meta, TRUE );
		}
		return $this->last_random_question_datetime;
	}

	public function update_last_random_question_datetime( $new_datetime ) {
		$this->last_random_question_datetime = $new_datetime;
		update_user_meta( $this->user_id, self::$last_random_question_datetime_meta, $new_datetime );
	}
}