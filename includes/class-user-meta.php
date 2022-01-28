<?php
/**
 * Contains functions and definitions for user meta
 *
 * @package LockUserAccount
 * @author babaTechs
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for user meta settings
 */
class User_Meta {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Add filter to add another action in users' bulk action dropdown.
		add_filter( 'bulk_actions-users', array( $this, 'register_bulk_action' ) );

		// Add filter to add another column header in users' listing.
		add_filter( 'manage_users_columns', array( $this, 'wpvip_new_modify_user_table' ) );

		// Add filter to show output of custom column in users' listing.
		add_filter( 'manage_users_custom_column', array( $this, 'wpvip_new_modify_user_table_row' ), 10, 3 );

		// Add filter to process bulk action request.
		add_filter( 'handle_bulk_actions-users', array( $this, 'process_lock_action' ), 10, 3 );

		// Make columns sortable.
		add_filter( 'manage_users_sortable_columns', array( $this, 'wpvip_my_sortable_cake_column' ) );

		// Show Reactivate link when customers are locked out.
		add_filter( 'user_row_actions', array( $this, 'wpvip_reactivate_user_link' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'add_admin_listeners' ) );
	}

	/**
	 * Adds the 'Reactivate' link to the user row actions when customer is locked out.
	 *
	 * @param array   $actions - The user row actions.
	 * @param WP_User $user - The current user.
	 * @return array  $actions - The user row actions.
	 */
	public function wpvip_reactivate_user_link( $actions, $user ) {
		$locked = get_user_meta( $user->ID, 'wpvip_user_locked', true );
		if ( $locked && current_user_can( 'manage_options' ) ) {
			$actions['wplu_reactivate'] = "<a class='wplu_reactivate' href='" . wp_nonce_url( "users.php?action=wplu_reactivate&amp;user=$user->ID", 'wplu-reactivate' ) . "'>" . esc_html__( 'Reactivate User Now', 'wp-lock-users' ) . '</a>';
		}
		return $actions;
	}

	/**
	 * Add another action in bulk action drop down list on users listing screen
	 *
	 * @param array $actions    Array of users bulk actions.
	 * @return array            Array with adition of Lock action
	 */
	public function register_bulk_action( $actions ) {
		$actions['lock']   = esc_html__( 'Lock', 'wp-lock-users' );
		$actions['unlock'] = esc_html__( 'Unlock', 'wp-lock-users' );
		return $actions;
	}

	/**
	 * Add another column header in listing of users
	 *
	 * @param string $column    Output of column header.
	 * @return array            Array with addition of Locked column
	 */
	public function wpvip_new_modify_user_table( $column ) {
		$column['locked'] = 'Have Access?';
		return $column;
	}

	/**
	 * Displaying status of user's account in list of users for Locked column
	 *
	 * @param string $output        Output value of custom column.
	 * @param string $column_name   Column name.
	 * @param int    $user_id       ID of user.
	 * @return string               Output value of custom column.
	 */
	public function wpvip_new_modify_user_table_row( $output, $column_name, $user_id ) {
		if ( 'locked' !== $column_name ) {
			return "$output";
		}
		$locked = get_user_meta( $user_id, sanitize_key( 'wpvip_user_locked' ), true );
		return ( 'yes' === $locked ) ? __( '❌', 'wp-lock-users' ) : __( '✅', 'wp-lock-users' );
	}

	/**
	 * Processing Lock and Unlock users on request of bulk action
	 *
	 * @param string $sendback          Redirect back URL.
	 * @param string $current_action    Current screen id.
	 * @param array  $userids           Array of users IDs.
	 * @return string                   Redirect back URL
	 */
	public function process_lock_action( $sendback, $current_action, $userids ) {
		// Process lock request.
		if ( 'lock' === $current_action ) {
			$current_user_id = get_current_user_id();
			foreach ( $userids as $userid ) {
				if ( $userid === $current_user_id ) {
					continue;
				}
				update_user_meta( (int) $userid, sanitize_key( 'wpvip_user_locked' ), 'yes' );
			}
		}
		// Process unlock request.
		elseif ( 'unlock' === $current_action ) {
			foreach ( $userids as $userid ) {
				update_user_meta( (int) $userid, sanitize_key( 'wpvip_user_locked' ), '' );
			}
		}
		return $sendback;
	}

	/**
	 * Remove the lock from the account.
	 *
	 * @param number $userid      ID of user
	 * @return void
	 */
	public function single_account_reactivate( $userid ) {
		update_user_meta( $userid, sanitize_key( 'wpvip_user_locked' ), '' );
	}

	/**
	 * Show a reactivation admin notice.
	 *
	 * @return void
	 */
	public function account_unlocked_notice() {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'User reactivated!', 'wp-lock-users' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Add admin listeners for the 'Reactivation' user row action.
	 *
	 * @return void
	 */
	public function add_admin_listeners() {
		if ( ! isset( $_GET['action'] ) || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wplu-reactivate' ) || ( 'wplu_reactivate' !== $_GET['action'] ) || ! isset( $_GET['user'] ) ) {
				return;
		}

		/* Reactivate user */
		$this->single_account_reactivate( $_GET['user'] );

		// Show reactivation admin notices.
		add_action( 'admin_notices', [ $this, 'account_unlocked_notice' ] );
	}


	/**
	 * Add another column header in listing of users
	 *
	 * @param string $columns   Output of column header.
	 * @return array            Array with addition of Locked column
	 */
	public function wpvip_my_sortable_cake_column( $columns ) {
		$columns['locked'] = 'Access?';
		return $columns;
	}
}

new User_Meta();
