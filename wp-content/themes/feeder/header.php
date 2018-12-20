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

<body <?php body_class(); ?>>

<div id="page">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'feeder' ); ?></a>

	<header id="masthead">
		<div id="menu">
			<?php
			if ( function_exists( 'the_custom_logo' ) ) :
				the_custom_logo();
			else :
				?>
				<h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
				<?php
			endif;
			?>
			<?php
			if ( has_nav_menu( 'menu-private' ) ) :
				if ( get_query_var( 'menu' ) ) :
					?>
					<ul>
						<li id="menunav"><a id="susty-back-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/times-solid.svg"><span class="screen-reader-text"><?php esc_html_e( 'Close menu', 'susty' ); ?></span></a></li>
					</ul>
					<script>
						var susty_home_url = '<?php echo esc_url( home_url( '/' ) ); ?>';
						if ( 0 === document.referrer.indexOf( susty_home_url ) ) {
							document.getElementById( 'susty-back-link' ).href = document.referrer;
						}
					</script>
					<?php
				else :
					?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'menu-private',
						)
					);
					?>
					<ul>
					<?php
					if ( is_user_logged_in() ) {
						?>
						<li id="logoutnav">
							<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" title="Logout">Logout</a>
						</li>
						<?php
					} else {
						?>
						<li id="loginnav">
							<a href="<?php echo esc_url( wp_login_url( home_url() ) ); ?>" title="Logout">Login</a> / <?php wp_register( '', '' ); ?>
						</li>
						<?php
					}
					?>
					</ul>
								
					<ul>
						<li id="menunav"><a href="<?php echo esc_url( ( get_option( 'permalink_structure' ) ? home_url( '/menu/' ) : home_url( '/?menu' ) ) ); ?>"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/align-left-solid.svg"></a></li>
					</ul>
					<?php
				endif;
			endif;
			?>
		</div>
	</header>

	<div id="content">
