<?php
/**
 * Feedme User Settings
 *
 * @package feeder
 */
use PHPePub\Core\EPub;
use PHPePub\Helpers\CalibreHelper;

$book = new EPub();

/**
 * This class handles all settings for feeder users.
 */
class Feedme_Settings {

	/**
	 * A user object set up by __construct.
	 *
	 * @var WP_User $user.
	 */
	private $user;

	/**
	 * A prefix so no user_meta keys may be duplucates with other plugin variables.
	 *
	 * @var string $prefix.
	 */
	private $prefix = 'feeder_';

	/**
	 * Instantiate class variables.
	 *
	 * @param WP_User $user A user object set upp when class is initiated.
	 */
	public function __construct( $user ) {
		$this->user = $user;
	}

	/**
	 * Fetching and returning a user meta value.
	 *
	 * @param string $name The user meta key to be fetched.
	 *
	 * @return mixed.
	 */
	public function get_setting( $name ) {
		$name = $this->prefix . $name;
		return get_user_meta( $this->user->ID, $name, true );
	}

	/**
	 * Setting a user meta value.
	 *
	 * @param string $name The user meta key to be setted.
	 * @param string $value The user meta vlue to be setted.
	 */
	public function set_setting( $name, $value ) {
		$name = $this->prefix . $name;
		update_user_meta( $this->user->ID, $name, $value );
	}

	/**
	 * Handle posted request data if it comes from Feedme User Settings forms.
	 */
	public function process_post() {
		global $feeder_error_messages;

		if ( isset( $_REQUEST['_wpnonce'] ) ) {
			$retrieved_nonce = sanitize_key( $_REQUEST['_wpnonce'] );

			// Continue only if wp nounce sums upp with feeder_settings.
			if ( wp_verify_nonce( $retrieved_nonce, 'feeder_settings' ) ) {

				if ( isset( $_REQUEST['feeder_email'] ) ) {
					$value = sanitize_email( wp_unslash( $_REQUEST['feeder_email'] ) );
					if ( is_email( $value ) ) {
						$this->set_setting( 'email', $value );
					} else {
						$feeder_error_messages[] = esc_html__( 'Not a valide email.', 'feeder' );
					}
				}

				if ( isset( $_REQUEST['feeder_schedule'] ) ) {
					// If never been scheduled before set default values.
					$last = $this->get_setting( 'last' );
					if ( empty( $last ) ) {
						$this->set_setting( 'next', time() );
						$this->set_setting( 'last', time() );
					}
					// Save scheduling.
					$value = sanitize_text_field( wp_unslash( $_REQUEST['feeder_schedule'] ) );
					if ( ! empty( $value ) ) {
						$this->set_setting( 'schedule', $value );
					} else {
						$feeder_error_messages[] = esc_html__( 'No valide scheduling.', 'feeder' );
					}
				}
			}
		}
	}
}

/**
 * Let class handle post requests for the current user.
 */
add_action(
	'init',
	function() {
		$user          = wp_get_current_user();
		$user_settings = new Feedme_Settings( $user );

		$user_settings->process_post();
	}
);
