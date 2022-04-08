<?php
/**
 * Feeder Buddypress Functions
 *
 * @package feeder
 */

/**
 * This class handles all settings and functions for buddypress integration.
 */
class Feeder_Buddypress {

	/**
	 * Instantiate class variables.
	 *
	 * @param WP_User $user A user object set upp when class is initiated.
	 */
	public function __construct() {
		// Add notices for theme requirements.
		add_action( 'admin_notices', [ $this, 'add_buddypress_warning' ] );

		// Add notification registred component.
		add_filter( 'bp_notifications_get_registered_components', [ $this, 'feeder_filter_notifications_get_registered_components' ], 10, 1 );

		add_filter( 'bp_notifications_get_notifications_for_user', [ $this, 'feeder_format_buddypress_notifications' ], 10, 5 );
		add_action( 'bp_after_member_settings_template', [ $this, 'feeder_settings_template' ], 99 );
	}

	/**
	 * Adds an warning if using the theme without buddypress plugin.
	 */
	public function add_buddypress_warning() {
		if ( ! function_exists( 'buddypress' ) ) {
			echo '<div class="error"><p>' . esc_html__( 'Warning: The Feeder.mobi theme needs Buddypress to function', 'feeder' ) . '</p></div>';
		}
	}

	/**
	 * Adds page for user feeds.
	 */
	public function feeder_feeds_page() {
		global $bp;
		$args = [
			'name'                    => __( 'Feeds', 'feeder' ),
			'slug'                    => 'feeds',
			'default_subnav_slug'     => 'feeds',
			'position'                => 50,
			'show_for_displayed_user' => false,
			'screen_function'         => [ $this, 'bp_nav_tab_stuff' ],
			'item_css_id'             => 'feeder',
		];
		bp_core_new_nav_item( $args );
	}

	/**
	 * Creates the screen function content for feeds page.
	 */
	public function bp_nav_tab_stuff() {
		add_action( 'bp_template_content', [ $this, 'feeder_feeds_content' ] );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Prints the template for feeds.
	 */
	public function feeder_feeds_content() {
		get_template_part( 'template-parts/content', 'feeds' );
	}

	/**
	 * Prints the template for settings.
	 */
	public function feeder_settings_template() {
		if ( 'general' === bp_current_action() ) {
			get_template_part( 'template-parts/content', 'settings' );
		}
	}

	/**
	 * Adds feeder as a buddypress component.
	 */
	public function feeder_filter_notifications_get_registered_components( $component_names ) {
		// Force $component_names to be an array.
		if ( ! is_array( $component_names ) ) {
			$component_names = array();
		}

		// Add 'custom' component to registered components array.
		array_push( $component_names, 'feeder' );

		// Return component's with 'custom' appended.
		return $component_names;

	}

	/**
	 * Adds format for feeder notifications.
	 *
	 * @param string $action The name of the action.
	 * @param int    $item_id The item ID.
	 * @param int    $secondary_item_id The id of the second thing.
	 * @param int    $total_items Total items in action.
	 * @param string $format The return format.
	 *
	 * @return string.
	 */
	public function feeder_format_buddypress_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {
		switch ( $action ) {
			case 'feeder_add_feed':
				$user_nicename = bp_get_displayed_user_username();
				$custom_title  = $user_nicename . ' added a feed.';
				$custom_text   = $user_nicename . ' added the feed: ' . get_the_title( $item_id ) . '.';

				// WordPress Toolbar.
				if ( 'string' === $format ) {
					$return = apply_filters( 'custom_filter', esc_html( $custom_text ), $custom_text, $custom_link ); // Deprecated BuddyBar.
				} else {
					$return = apply_filters(
						'custom_filter',
						[
							'text' => $custom_text,
						],
						$custom_link,
						(int) $total_items,
						$custom_text,
						$custom_title
					);
				}
				return $return;
			case 'feeder_delete_feed':
				$user_nicename = bp_get_displayed_user_username();
				$custom_title  = $user_nicename . ' deleted a feed.';
				$custom_text   = $user_nicename . ' deleted a feed.';

				// WordPress Toolbar.
				if ( 'string' === $format ) {
					$return = apply_filters( 'custom_filter', esc_html( $custom_text ), $custom_text, $custom_link );
					// Deprecated BuddyBar.
				} else {
					$return = apply_filters(
						'custom_filter',
						[
							'text' => $custom_text,
						],
						(int) $total_items,
						$custom_text,
						$custom_title
					);
				}
				return $return;
			case 'feeder_sent_feed':
				$user_nicename = bp_get_displayed_user_username();
				$custom_title  = 'A mobi was sent to your Kindle device.';
				$custom_text   = 'A mobi was generated and sent to your Kindle device. You can download it manualy here';
				$custom_link   = wp_get_attachment_url( $item_id );
				// WordPress Toolbar.
				if ( 'string' === $format ) {
					$return = apply_filters( 'custom_filter', '<a href="' . esc_url( $custom_link ) . '" title="' . esc_attr( $custom_title ) . '">' . esc_html( $custom_text ) . '</a>', $custom_text, $custom_link );
					// Deprecated BuddyBar.
				} else {
					$return = apply_filters(
						'custom_filter',
						[
							'text' => $custom_text,
							'link' => $custom_link,
						],
						$custom_links,
						(int) $total_items,
						$custom_text,
						$custom_title
					);
				}
				return $return;
		}
	}
}

$feeder_buddypress = new Feeder_Buddypress();
add_action( 'bp_setup_nav', [ $feeder_buddypress, 'feeder_feeds_page' ], 999 );
