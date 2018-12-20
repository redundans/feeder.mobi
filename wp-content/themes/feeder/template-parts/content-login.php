<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package feeder
 */

?>

<article id="post-login">
	<header>
		<h2><?php esc_html_e( 'Log in', 'feeder' ); ?></h2>
	</header>

	<div>
		<p>hej</p>
		<?php
		$args = array(
			'echo'           => true,
			'redirect'       => home_url(),
			'form_id'        => 'loginform',
			'label_username' => __( 'Username', 'feeder' ),
			'label_password' => __( 'Password', 'feeder' ),
			'label_remember' => __( 'Remember Me', 'feeder' ),
			'label_log_in'   => __( 'Log In', 'feeder' ),
			'id_username'    => 'user_login',
			'id_password'    => 'user_pass',
			'id_remember'    => 'rememberme',
			'id_submit'      => 'wp-submit',
			'remember'       => true,
			'value_username' => null,
			'value_remember' => true,
		);

		// Calling the login form.
		wp_login_form( $args );
		?>
	</div>

	<footer>
	</footer>
</article><!-- #post-<?php the_ID(); ?> -->
