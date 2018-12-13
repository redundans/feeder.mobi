<?php
/**
 * Registrating custom post types.
 *
 * @package feedme
 */

/**
 * Register Custom Post Type Feed.
 */
function feedme_feeds() {

	$labels = array(
		'name'                  => _x( 'Feeds', 'Post Type General Name', 'feedme' ),
		'singular_name'         => _x( 'Feed', 'Post Type Singular Name', 'feedme' ),
		'menu_name'             => __( 'Feeds', 'feedme' ),
		'name_admin_bar'        => __( 'Feed', 'feedme' ),
		'archives'              => __( 'Feed Archives', 'feedme' ),
		'attributes'            => __( 'Feed Attributes', 'feedme' ),
		'parent_item_colon'     => __( 'Parent Feed:', 'feedme' ),
		'all_items'             => __( 'All Feeds', 'feedme' ),
		'add_new_item'          => __( 'Add New Feed', 'feedme' ),
		'add_new'               => __( 'Add Feed', 'feedme' ),
		'new_item'              => __( 'New Feed', 'feedme' ),
		'edit_item'             => __( 'Edit Feed', 'feedme' ),
		'update_item'           => __( 'Update Feed', 'feedme' ),
		'view_item'             => __( 'View Feed', 'feedme' ),
		'view_items'            => __( 'View Feed', 'feedme' ),
		'search_items'          => __( 'Search Feed', 'feedme' ),
		'not_found'             => __( 'Not found', 'feedme' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'feedme' ),
		'featured_image'        => __( 'Featured Image', 'feedme' ),
		'set_featured_image'    => __( 'Set featured image', 'feedme' ),
		'remove_featured_image' => __( 'Remove featured image', 'feedme' ),
		'use_featured_image'    => __( 'Use as featured image', 'feedme' ),
		'insert_into_item'      => __( 'Insert into Feed', 'feedme' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Feed', 'feedme' ),
		'items_list'            => __( 'Feeds list', 'feedme' ),
		'items_list_navigation' => __( 'Feeds list navigation', 'feedme' ),
		'filter_items_list'     => __( 'Filter Feeds list', 'feedme' ),
	);
	$args   = array(
		'label'               => __( 'Feed', 'feedme' ),
		'description'         => __( 'Feed Description', 'feedme' ),
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
	register_post_type( 'feedme_feed', $args );

}
add_action( 'init', 'feedme_feeds', 0 );
