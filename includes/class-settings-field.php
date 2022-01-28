<?php

namespace Lock_User_Account;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Class for settings page.
 */
class Locked_User_Settings {

	/**
	 * The Admin options
	 *
	 * @var array - The saved plugin options.
	 */
	private $options;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Create admin page settings page in Users.
		add_action( 'admin_menu', array( $this, 'wplu_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings_section' ) );

		// Add Settings link into plugin's actions links.
		add_filter( 'plugin_action_links_' . plugin_basename( 'lock-account/lock-account.php' ), array( $this, 'settings_link' ) );
	}

	/**
	 * Adds the admin page ( Users -> Lock Users Settings ).
	 *
	 * @return void
	 */
	public function wplu_add_plugin_page() {
		add_users_page(
			'Lock Inactive Users',
			'Lock Users Settings',
			'manage_options',
			'wp-lock-users',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Creates the admin page with its settings.
	 *
	 * @return void
	 */
	public function create_admin_page() {
		$this->options = get_option( 'wplu_options' );
		?>

		<div class="wrap">
			<h2><?php echo esc_html_e( 'WP Lock Inactive Users', 'wp-lock-users' ); ?></h2>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'wplu_option_group' );
					do_settings_sections( 'wp-lock-users' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Initializing field and section in settings page in admin panel
	 */
	public function register_settings_section() {
		// Add settings field to store option.
		register_setting(
			'wplu_option_group',
			'wplu_options',
			array( $this, 'sanitize' )
		);

		// Add settings section to show on general settings page.
		add_settings_section(
			'wplu_locked_message_section',
			esc_html__( 'Lock User Account', 'wp-lock-users' ),
			array( $this, 'settings_section_callback' ),
			'wp-lock-users'
		);

		// Add settings field to show in section of settings on general settings page.
		add_settings_field(
			'wplu_locked_message',
			esc_html__( 'Locked User Message', 'wp-lock-users ' ),
			array( $this, 'settings_field_callback' ),
			'wp-lock-users',
			'wplu_locked_message_section',
			array( 'label_for' => 'wplu_locked_message' )
		);

		add_settings_field(
			'wplu_days_limit',
			esc_html__( 'Number of days', 'wp-lock-users' ),
			array( $this, 'days_limit_callback' ),
			'wp-lock-users',
			'wplu_locked_message_section'
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys.
	 */
	public function sanitize( $input ) {
		$new_input = array();
		if ( isset( $input['wplu_locked_message'] ) )
			$new_input['wplu_locked_message'] = sanitize_text_field( $input['wplu_locked_message'] );

		if ( isset( $input['wplu_days_limit'] ) )
			$new_input['wplu_days_limit'] = sanitize_text_field( $input['wplu_days_limit'] );

		return $new_input;
	}

	/**
	 * Call back function of settings section
	 *
	 * @param array $args   Array of parameters provided to add settings section.
	 */
	public function settings_section_callback( $args ) {
		printf( '<p>%1$s</p>', esc_html__( 'This section is added by Lock User Account plugin.', 'wp-lock-users' ) );
	}

	/**
	 * Call back function of field for settings section
	 * Showing input field to get input from admin to store error message
	 *
	 * @param array $args   Array of paremters provided to register settings field.
	 */
	public function settings_field_callback( $args ) {
		// Show input field to get input from admin.
		printf( '<input type="text" name="wplu_options[wplu_locked_message]" id="locked_message" value="%s" class="regular-text ltr">',
			isset( $this->options['wplu_locked_message'] ) ? esc_attr( $this->options['wplu_locked_message'] ) : ''
		);

		// Show description under input field.
		printf( '<p class="description" id="baba-locked-message-description">%1$s</p>', esc_html__( 'Please enter message to show on login screen in case of locked account', 'wp-lock-users' ) );
	}

	/**
	 * The days limit option.
	 *
	 * @return void
	 */
	public function days_limit_callback() {
		printf(
			'<input id="days_limit" type="number" style="width: 5em;" name="wplu_options[wplu_days_limit]" min="1" id="days_limit" value="%s"> <p class="description">The number of days to wait to deactivate a user.</p>',
			isset( $this->options['wplu_days_limit'] ) ? esc_attr( $this->options['wplu_days_limit'] ) : esc_html( \WPVIP_Lock_User_Account::$number_of_days )
		);
	}

	/**
	 * Adding support link under plugin's description
	 *
	 * @param array $links  Links about plugin.
	 * @return array        Links with support link
	 */
	public function settings_link( $links ) {
		// Build and escape the URL.
		$url          = esc_url(
							add_query_arg(
								'page',
								'wp-lock-users',
								get_admin_url() . 'users.php'
							)
		);
		$setting_link = array(
						sprintf( '<a href="%1$s">%2$s</a>', $url, esc_html__( 'Settings', 'babatechs' ) ),
		);
		return array_merge( $links, $setting_link );
	}
}

new Locked_User_Settings();
