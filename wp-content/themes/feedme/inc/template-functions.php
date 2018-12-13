<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package feedme
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function feedme_wp_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'feedme_wp_body_classes' );


/**
 * Echoes error messages from feedme.
 */
function feedme_error_message() {
	global $feedme_error_messages;
	if ( ! empty( $feedme_error_messages ) ) {
		echo '<div id="error">';
		foreach ( $feedme_error_messages as $feedme_error_message ) {
			echo '<p>' . esc_html( $feedme_error_message ) . '</p>';
		}
		echo '</div>';
	}
}

/**
 * Prints a delete button for feed lists.
 */
function feedme_delete_button() {
	echo get_feedme_delete_button();
}

/**
 * Retur a delete button for feed lists.
 *
 * @return string.
 */
function get_feedme_delete_button() {
	global $wp;
	$current_url = home_url( add_query_arg( array(), $wp->request ) );

	$output  = '<form method="post" action="' . esc_url( $current_url ) . '">';
	$output .= wp_nonce_field( 'feedme_delete', '_wpnonce', true, false );
	$output .= '<input type="hidden" name="feedme_delete" value="' . get_the_ID() . '">';
	$output .= '<input type="submit" value="' . esc_html__( 'Delete', 'feedme' ) . '">';
	$output .= '</form>';
	return $output;
}

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function feedme_wp_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'feedme_wp_pingback_header' );
