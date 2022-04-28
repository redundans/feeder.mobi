<?php
/**
 * The header for our theme
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package feeder
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'mx-9' ); ?>>

<div id="page" class="container mx-auto">
	<header id="masthead" class="my-6">
		<nav id="menu" class="flex flex-row justify-between items-center">
			<?php
			if ( function_exists( 'the_custom_logo' ) ) :
				the_custom_logo();
			else :
				?>
				<h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
				<?php
			endif;
			?>

			<ul>
			<?php
			if ( is_user_logged_in() && has_nav_menu( 'menu-private' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'menu-private',
						'menu_class' => 'flex flex-row gap-6'
					)
				);
			} else {
				wp_nav_menu(
					array(
						'theme_location' => 'menu-public',
					)
				);
			}
			?>
			</ul>
		</nav>
	</header>

	<div id="content" class="my-12 max-w-2xl mx-auto">
