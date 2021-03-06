<?php
/**
 * Feeder WP functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package feeder
 */

if ( ! function_exists( 'feeder_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function feeder_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on feeder WP, use a find and replace
		 * to change 'feeder' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'feeder', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'menu-private' => esc_html__( 'Private menu', 'feeder' ),
				'menu-public'  => esc_html__( 'Public menu', 'feeder' ),
			)
		);

		// Make subscribers able to read private pages.
		$subscriber_role = get_role( 'subscriber' );
		$subscriber_role->add_cap( 'read_private_posts' );
		$subscriber_role->add_cap( 'read_private_pages' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'feeder_custom_background_args',
				array(
					'default-color' => 'fffefc',
					'default-image' => '',
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		add_theme_support(
			'post-formats',
			array(
				'post',
				'link',
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'feeder_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function feeder_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'feeder_content_width', 640 );
}
add_action( 'after_setup_theme', 'feeder_content_width', 0 );

/**
 * Enqueue scripts and styles.
 */
function feeder_scripts() {
	wp_enqueue_style( 'feeder-style', get_stylesheet_uri(), array(), '1.0' );
	wp_enqueue_script( 'feeder-script', get_template_directory_uri() . '/js/feeder.js', array(), '1.0', true );
	wp_localize_script( 'feeder-script', 'wp', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	wp_deregister_script( 'wp-embed' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Remove styles for blocks.
	wp_dequeue_style( 'wp-block-library' );

	// Remove styles for buddypress.
	wp_dequeue_style( 'bp-nouveau' );
}
add_action( 'wp_enqueue_scripts', 'feeder_scripts' );

/**
 * Load vendor classes.
 */
require get_template_directory() . '/vendor/autoload.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom post types for this theme.
 */
require get_template_directory() . '/inc/custom-post-types.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Feeder User Settings.
 */
require get_template_directory() . '/inc/class-feeder-settings.php';

/**
 * Feeder User feeds.
 */
require get_template_directory() . '/inc/class-feeder-feeds.php';

/**
 * Feeder Scheduling.
 */
require get_template_directory() . '/inc/class-feeder-scheduling.php';

/**
 * Feeder buddypress.
 */
require get_template_directory() . '/inc/class-feeder-buddypress.php';

/**
 * Feeder dithering.
 */
require get_template_directory() . '/inc/class-feeder-dither.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

add_action(
	'wp_enqueue_scripts',
	function() {
		if ( ! is_user_logged_in() ) {
			wp_deregister_style( 'dashicons' );
		}
	}
);

add_filter(
	'wp_nav_menu_objects',
	function( $items, $args ) {
		foreach ( $items as $i => $obj ) {
			if ( ! is_user_logged_in() && 'private' === get_post_status( $obj->object_id ) ) {
				unset( $items[ $i ] );
			}
		}
		return $items;
	},
	10,
	2
);

add_action(
	'init',
	function() {
		add_rewrite_rule( 'menu', 'index.php?menu=true', 'top' );
	}
);

add_filter(
	'query_vars',
	function( $vars ) {
		$vars[] = 'menu';
		return $vars;
	}
);

add_filter(
	'template_include',
	function( $path ) {
		if ( get_query_var( 'menu' ) ) {
			return get_template_directory() . '/menu.php';
		}
		return $path;
	}
);

// Only show admin bar for admin user.
if ( ! current_user_can( 'manage_options' ) ) {
	show_admin_bar( false );
}

/**
 * This is required such that social networks using your content will
 * actually know that there are Open Graph tags being used to describe
 * your WordPress content.
 */
add_filter(
	'language_attributes',
	function( $output ) {
		return $output . '
		xmlns="https://www.w3.org/1999/xhtml"
		xmlns:og="https://ogp.me/ns#" 
		xmlns:fb="http://www.facebook.com/2008/fbml"';
	}
);


/**
 * Add Open Graph Meta Info from the actual article data, or customize as necessary.
 */
add_action(
	'wp_head',
	function() {
		global $post, $wp;
		if ( is_singular() ) {
			echo '<meta property="og:title" content="' . esc_html( get_the_title() ) . '"/>';
		} else {
			echo '<meta property="og:title" content="' . esc_html( get_bloginfo( 'name' ) ) . '"/>';
		}
		if ( '' !== $post->post_excerpt ) {
			$excerpt = wp_strip_all_tags( $post->post_excerpt );
			$excerpt = str_replace( '', "'", $excerpt );
		} else {
			$excerpt = get_bloginfo( 'description' );
		}
		echo '<meta property="og:description" content="' . esc_html( $excerpt ) . '"/>';
		echo '<meta property="og:type" content="website"/>';
		echo '<meta property="og:url" content="' . esc_url( home_url( add_query_arg( array( $_GET ), $wp->request ) ) ) . '"/>'; // phpcs:ignore
		echo '<meta property="og:site_name" content="' . esc_html( get_bloginfo( 'name' ) ) . '"/>';

		if ( ! has_post_thumbnail( $post->ID ) ) { // the post does not have featured image, use a default image.
			// Create a default image on your server or an image in your media library, and insert it's URL here.
			$default_image = get_template_directory_uri() . '/images/feeder.png';
			echo '<meta property="og:image" content="' . esc_url( $default_image ) . '"/>';
		} else {
			$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
			echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '"/>';
		}
	},
	10
);

/**
 * Allow uploading .MOBI and .EPUB files.
 */
add_filter(
	'upload_mimes',
	function( $existing_mimes ) {
		$existing_mimes['mobi'] = 'application/x-mobipocket-ebook';
		$existing_mimes['epub'] = 'application/epub+zip';
		return $existing_mimes;
	}
);
