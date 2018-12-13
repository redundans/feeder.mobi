<?php
/**
 * Feedme User Settings
 *
 * @package feedme
 */

/**
 * This class handles all settings for feedme users.
 */
class Feedme_Feeds {

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
	private $prefix = 'feedme_';

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
				$this->add_error_message( esc_html__( 'The feed you tried to add does allready exists!', 'feedme' ) );
			}
		} else {
			$this->add_error_message( esc_html__( 'No feed found at that url!', 'feedme' ) );
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
			$result = $this->insert_feed( $feed );
		}
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
				'post_type'      => 'feedme_feed',
				'meta_key'       => 'url',
				'meta_value'     => $url,
				'author'         => $user->ID,
			)
		);
		return ( 0 === $existing->post_count ? false : true );
	}

	/**
	 * Insert feed as post type feedme_feed with user as author.
	 *
	 * @param object $feed The feed object.
	 */
	private function insert_feed( $feed ) {
		$result = wp_insert_post(
			array(
				'post_title'  => $feed->title,
				'post_type'   => 'feedme_feed',
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
		global $feedme_error_messages;
		$feedme_error_messages[] = $message;
	}

	/**
	 * Handle posted request data if it comes from Feedme User Settings forms.
	 */
	public function process_post() {
		if ( isset( $_REQUEST['_wpnonce'] ) ) {
			$retrieved_nonce = sanitize_key( $_REQUEST['_wpnonce'] );

			// Continue only if wp nounce sums upp with feedme_settings.
			if ( wp_verify_nonce( $retrieved_nonce, 'feedme_feeds' ) ) {
				if ( isset( $_REQUEST['feedme_url'] ) ) {
					$url = esc_url_raw( wp_unslash( $_REQUEST['feedme_url'] ) );
					$this->add_feed( $url );
				}
			}

			// Continue only if wp nounce sums upp with feedme_delete.
			if ( wp_verify_nonce( $retrieved_nonce, 'feedme_delete' ) ) {
				if ( isset( $_REQUEST['feedme_delete'] ) ) {
					$feed_ids = wp_unslash( $_REQUEST['feedme_delete'] );
					foreach ( $feed_ids as $feed_id ) {
						$feed = get_post( $feed_id );
						if ( (int) $feed->post_author === (int) $this->user->ID ) {
							wp_trash_post( $feed->ID );
							wp_redirect( get_permalink() );
						} else {
							$this->add_error_message( esc_html__( 'No rights to delete feed.', 'feedme' ) );
						}
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
		$user_settings = new Feedme_Feeds( $user );

		$user_settings->process_post();
	}
);
