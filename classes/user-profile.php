<?php
/**
 * Data added to the user profile
 */
class JohnCoffee_User_Profile {
	private $user_id;
	
	public static $status_meta = 'johncoffee_status';
	private $status;
	
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
	 * Slack Webhook URL
	 */
	public function get_status() {
		if ( empty( $this->status ) ) {
			$this->status = $this->get_meta( self::$status_meta );
		}
		return $this->status;
	}

	public function update_status( $new_status ) {
		$this->status = $new_status;
		$this->update_meta( self::$status_meta, $new_status );
	}

	/**
	 * Defines if a channel has been defined
	 */
	public function has_channel_index( $index ) {
		$slack_meta = JohnCoffee_User_Profile_Channel::$slack_webhook_url_meta . '_' . $index;
		$teams_meta = JohnCoffee_User_Profile_Channel::$teams_webhook_url_meta . '_' . $index;
		return ( $this->get_meta( $slack_meta ) != '' || $this->get_meta( $teams_meta ) != '' );
	}
}