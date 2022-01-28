<?php
/**
 * Plugin Name: Lock User Account
 * Plugin URI: http://wpvip.com
 * Description: Lock user accounts with custom message
 * Version: 1.0.0
 * Author: Dustin Hartzler
 * Author URI: http://YourWebsiteEngineer.com
 *
 * @package WPVIP_Lock_User_Account
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Establish class
 */
class Lock_User_Account {

	/**
	 * Contains errors and messages as admin notices.
	 *
	 * @var array
	 */
	public static $number_of_days = 90;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Add filter to check user's account lock status.
		add_filter( 'wp_authenticate_user', array( $this, 'check_lock' ) );

		// Create the custom meta upon login.
		add_action( 'wp_login', array( $this, 'last_login' ), 10, 2 );

		// Overwrite the default $days_limit with the provided number if it is changed using the 'wpdiu_days_limit' filter.
		$number_of_days = apply_filters( 'wplu_days_limit', $number_of_days );
	}

	/**
	 * Applying user lock filter on user's authentication
	 *
	 * @param object $user          WP_User object.
	 * @return \WP_Error || $user   If account is locked then return WP_Error object, else return WP_User object
	 */
	public function check_lock( $user ) {
		if ( is_wp_error( $user ) ) {
			return $user;
		}
		$this->options         = get_option( 'wplu_options' );
		$last_login            = intval( get_user_meta( (int) $user->ID, sanitize_key( 'when_last_login' ), true ) );
		$is_locked             = get_user_meta( (int) $user->ID, sanitize_key( 'wpvip_user_locked' ), true );
		$days_since_last_login = round( ( time() - $last_login ) / ( DAY_IN_SECONDS ) );
		if ( 'yes' === $is_locked || $days_since_last_login > $number_of_days ) {
			update_user_meta( (int) $user->ID, sanitize_key( 'wpvip_user_locked' ), 'yes' );
			$error_message  = $this->options['wplu_locked_message'];
			$error_message .= 'It has been ' . $days_since_last_login . ' days since last login.';
			$error_message .= 'Number of days in settings is ' . $this->options['wplu_days_limit'];
			return new WP_Error( 'locked', ( $error_message ) ? $error_message : __( 'Your account is locked!', 'babatechs' ) );
		} else {
			return $user;
		}
	}
}

new Lock_User_Account();

// Load user meta and settings files in only admin panel.
if ( is_admin() ) {
	// Load user meta file.
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-user-meta.php';

	// Load settings message file.
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-settings-field.php';
}
