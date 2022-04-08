<?php
/**
 * The menu template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package feeder
 */

get_header();
?>
	<div id="primary">
		<main id="main">
			<nav id="site-navigation" class="main-navigation">
				<div class="nav-content">
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
						<li>
							<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" title="Logout">Logout</a>
						</li>
						<?php
					} else {
						?>
						<li>
							<a href="<?php echo esc_url( wp_login_url( home_url() ) ); ?>" title="Logout">Login</a> / <?php wp_register( '', '' ); ?>
						</li>
						<?php
					}
					?>
					</ul>
				</div>
			</nav><!-- #site-navigation -->
		</main>
	</div>

<?php
get_footer();
