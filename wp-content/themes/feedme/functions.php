<?php
/**
 * Feedme WP functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package feedme
 */

if ( ! function_exists( 'feedme_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function feedme_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on feedme WP, use a find and replace
		 * to change 'feedme' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'feedme', get_template_directory() . '/languages' );

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
				'menu-private' => esc_html__( 'Private menu', 'feedme' ),
				'menu-public'  => esc_html__( 'Public menu', 'feedme' ),
			)
		);

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
				'feedme_custom_background_args',
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
add_action( 'after_setup_theme', 'feedme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function feedme_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'feedme_content_width', 640 );
}
add_action( 'after_setup_theme', 'feedme_content_width', 0 );

/**
 * Enqueue scripts and styles.
 */
function feedme_scripts() {
	wp_enqueue_style( 'feedme-style', get_stylesheet_uri(), array(), '1.0' );

	wp_enqueue_script( 'feedme-script', get_template_directory_uri() . '/js/feedme.js', array(), '1.0', true );

	wp_deregister_script( 'wp-embed' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'feedme_scripts' );

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
 * Feedme User Settings.
 */
require get_template_directory() . '/inc/class-feedme-settings.php';

/**
 * Feedme User feeds.
 */
require get_template_directory() . '/inc/class-feedme-feeds.php';

/**
 * Feedme Scheduling.
 */
require get_template_directory() . '/inc/class-feedme-scheduling.php';

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
