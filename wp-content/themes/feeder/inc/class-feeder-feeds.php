<?php
/**
 * Feeder User Settings
 *
 * @package feeder
 */

/**
 * This class handles all settings for feeder users.
 */
class Feeder_Feeds {

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
	 * Getting all the user feeds.
	 *
	 * @return WP_Query.
	 */
	public function get_feeds() {
		$args = array(
			'author'        => $this->user->ID,
			'post_type'     => $this->prefix . 'feed',
			'post_status'   => 'publish',
			'post_per_page' => -1,
			'order_by'      => 'date',
			'order'         => 'ASC',
		);
		return new WP_Query( $args );
	}

	/**
	 * Setting a user meta value.
	 *
	 * @param string $url The posted url.
	 *
	 * @return mixed.
	 */
	private function get_feed_from_url( $url ) {
		$feed = new SimplePie();
		$feed->enable_cache( false );
		$feed->set_feed_url( $url );

		$success = $feed->init();

		if ( $feed->error() ) {
			$this->add_error_message( $feed->error() );
			return false;
		}

		if ( $success ) {
			if ( ! $this->feed_exists( $feed->subscribe_url() ) ) {
				return (object) array(
					'title' => $feed->get_title(),
					'url'   => $feed->subscribe_url(),
				);
			} else {
				$this->add_error_message( esc_html__( 'The feed you tried to add does allready exists!', 'feeder' ) );
			}
		} else {
			$this->add_error_message( esc_html__( 'No feed found at that url!', 'feeder' ) );
		}
		return false;
	}

	/**
	 * Adding a feed.
	 *
	 * @param object $url The user meta key to be setted.
	 */
	private function add_feed( $url ) {
		$feed = $this->get_feed_from_url( $url );
		if ( $feed ) {
			return $this->insert_feed( $feed );
		}
		return false;
	}

	/**
	 * Checks if the feed allready exist with the user as author.
	 *
	 * @param string $url The posted url.
	 *
	 * @return bool.
	 */
	private function feed_exists( $url ) {
		$existing = new WP_Query(
			array(
				'posts_per_page' => -1,
				'post_type'      => 'feeder_feed',
				'meta_key'       => 'url', // phpcs:ignore
				'meta_value'     => $url, // phpcs:ignore
				'author'         => $this->user->ID,
			)
		);
		return ( 0 === $existing->post_count ? false : true );
	}

	/**
	 * Insert feed as post type feeder_feed with user as author.
	 *
	 * @param object $feed The feed object.
	 */
	private function insert_feed( $feed ) {
		$result = wp_insert_post(
			array(
				'post_title'  => $feed->title,
				'post_type'   => 'feeder_feed',
				'post_status' => 'publish',
				'author'      => $this->user->ID,
			)
		);
		if ( ! is_wp_error( $result ) ) {
			update_post_meta( $result, 'url', $feed->url );
		}
		return $result;
	}

	/**
	 * Adds a error message to a global.
	 *
	 * @param string $message The error message.
	 */
	private function add_error_message( $message ) {
		global $feeder_error_messages;
		$feeder_error_messages[] = $message;
	}

	/**
	 * Handle posted request data if it comes from Feeder User Settings forms.
	 */
	public function process_post() {
		// Continue only if wp nounce sums upp with feeder_settings.
		if ( isset( $_POST['feeder_feed_nounce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['feeder_feed_nounce'] ) ), 'feeder_feed_add' ) ) {

			if ( isset( $_REQUEST['feeder_url'] ) ) {
				$url  = esc_url_raw( wp_unslash( $_REQUEST['feeder_url'] ) );
				$feed = $this->add_feed( $url );
				if ( $feed ) {
					bp_notifications_add_notification(
						[
							'user_id'          => $this->user->ID,
							'item_id'          => $feed,
							'component_name'   => 'feeder',
							'component_action' => 'feeder_add_feed',
							'date_notified'    => bp_core_current_time(),
							'is_new'           => 1,
						]
					);
				}
			}
		}

		// Continue only if wp nounce sums upp with feeder_delete.
		if ( isset( $_POST['feeder_feed_nounce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['feeder_feed_nounce'] ) ), 'feeder_feed_delete' ) ) {

			if ( isset( $_REQUEST['feeder_delete'] ) ) {
				$feed_ids = array_map( 'sanitize_text_field', wp_unslash( $_POST['feeder_delete'] ) );

				foreach ( $feed_ids as $feed_id ) {
					$feed = get_post( $feed_id );
					if ( (int) $feed->post_author === (int) $this->user->ID ) {
						wp_trash_post( $feed->ID );
						bp_notifications_add_notification(
							[
								'user_id'          => $this->user->ID,
								'item_id'          => $feed_id,
								'component_name'   => 'feeder',
								'component_action' => 'feeder_delete_feed',
								'date_notified'    => bp_core_current_time(),
								'is_new'           => 1,
							]
						);
						wp_safe_redirect( get_permalink() );
					} else {
						$this->add_error_message( esc_html__( 'No rights to delete feed.', 'feeder' ) );
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
		$user_settings = new Feeder_Feeds( $user );

		$user_settings->process_post();
	}
);
