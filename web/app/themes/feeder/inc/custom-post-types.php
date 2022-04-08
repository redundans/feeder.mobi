<?php
/**
 * Registrating custom post types.
 *
 * @package feeder
 */

/**
 * Register Custom Post Type Feed.
 */
function feeder_feeds() {

	$labels = array(
		'name'                  => _x( 'Feeds', 'Post Type General Name', 'feeder' ),
		'singular_name'         => _x( 'Feed', 'Post Type Singular Name', 'feeder' ),
		'menu_name'             => __( 'Feeds', 'feeder' ),
		'name_admin_bar'        => __( 'Feed', 'feeder' ),
		'archives'              => __( 'Feed Archives', 'feeder' ),
		'attributes'            => __( 'Feed Attributes', 'feeder' ),
		'parent_item_colon'     => __( 'Parent Feed:', 'feeder' ),
		'all_items'             => __( 'All Feeds', 'feeder' ),
		'add_new_item'          => __( 'Add New Feed', 'feeder' ),
		'add_new'               => __( 'Add Feed', 'feeder' ),
		'new_item'              => __( 'New Feed', 'feeder' ),
		'edit_item'             => __( 'Edit Feed', 'feeder' ),
		'update_item'           => __( 'Update Feed', 'feeder' ),
		'view_item'             => __( 'View Feed', 'feeder' ),
		'view_items'            => __( 'View Feed', 'feeder' ),
		'search_items'          => __( 'Search Feed', 'feeder' ),
		'not_found'             => __( 'Not found', 'feeder' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'feeder' ),
		'featured_image'        => __( 'Featured Image', 'feeder' ),
		'set_featured_image'    => __( 'Set featured image', 'feeder' ),
		'remove_featured_image' => __( 'Remove featured image', 'feeder' ),
		'use_featured_image'    => __( 'Use as featured image', 'feeder' ),
		'insert_into_item'      => __( 'Insert into Feed', 'feeder' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Feed', 'feeder' ),
		'items_list'            => __( 'Feeds list', 'feeder' ),
		'items_list_navigation' => __( 'Feeds list navigation', 'feeder' ),
		'filter_items_list'     => __( 'Filter Feeds list', 'feeder' ),
	);
	$args   = array(
		'label'               => __( 'Feed', 'feeder' ),
		'description'         => __( 'Feed Description', 'feeder' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'custom-fields', 'author' ),
		'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-rss',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		'show_in_rest'        => false,
	);
	register_post_type( 'feeder_feed', $args );

}
add_action( 'init', 'feeder_feeds', 0 );
