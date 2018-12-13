<?php
/**
 * The header for our theme
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package feedme
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
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'feedme' ); ?></a>

	<header id="masthead">
		<div id="menu">
			<h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
			<ul>
				<?php
				$main_query = new WP_Query(
					array(
						'post_type' => 'page',
						'orderby'   => 'menu_order',
						'order'     => 'ASC',
					)
				);

				if ( $main_query->have_posts() ) :

					/* Start the Loop */
					while ( $main_query->have_posts() ) :
						$main_query->the_post();
						?>
				<li>
					<a href="#<?php echo esc_html( $post->post_name ); ?>" title="<?php echo esc_html( $post->post_title ); ?>"><?php echo esc_html( $post->post_title ); ?></a>
				</li>
						<?php
					endwhile;
				endif;

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
		</div>
	</header>

	<div id="content">
