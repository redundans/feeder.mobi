<?php
/**
 * Template part for displaying user feeds content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package feeder
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header>
		<h2 class="screen-heading general-settings-screen"><?php esc_html_e( 'Your feeds', 'feeder' ); ?></h2>
	</header><!-- .entry-header -->

	<?php
	the_content();
	?>

	<div id="feeds">
		<form id="deleteform" method="post" action="<?php echo esc_url( $current_url ); ?>">
			<?php wp_nonce_field( 'feeder_feed_delete', 'feeder_feed_nounce' ); ?>

		<?php
		global $wp;

		$user        = wp_get_current_user();
		$user_feeds  = new Feeder_Feeds( $user );
		$my_feeds    = $user_feeds->get_feeds();
		$current_url = home_url( add_query_arg( array(), $wp->request ) );

		if ( $my_feeds->have_posts() ) {

			echo '<table>';

			while ( $my_feeds->have_posts() ) {
				$my_feeds->the_post();
				$url = get_post_meta( get_the_ID(), 'url', true );
				?>
				<tr>
					<td>
						<label class="checkbox">
							<input type="checkbox" value="<?php the_ID(); ?>" name="feeder_delete[]">
							<span class="checkmark"></span>
						</label>
					</td>
					<td>
						<strong><?php the_title(); ?></strong><br/>
						<a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php echo esc_url( $url ); ?></a><br/>
						<small>Updated: <?php feeder_last_updated(); ?></small>
					</td>
				</tr>
				<?php
			}

			echo '</table>';
			echo '<p><input type="submit" value="- ' . esc_html__( 'Remove feed', 'feeder' ) . '"></p>';

			wp_reset_postdata();
		} else {
			echo wp_kses( __( '<p>Sorry, no feeds could be found.</p>', 'feeder' ), [ 'p' => [] ] );
		}
		?>
		</form>

		<form id="feedsform" method="post" action="<?php echo esc_url( $current_url ); ?>">
			<h2><?php esc_html_e( 'Add a feed', 'feeder' ); ?></h2>
			<p>
				<?php esc_html_e( 'Start subscribing to a rss by entering the url to the Atom/RSS and click + Add feed.', 'feeder' ); ?>
			</p>
			<?php wp_nonce_field( 'feeder_feed_add', 'feeder_feed_nounce' ); ?>
			<p>
				<input type="url" placeholder="http://" id="feeder_url" name="feeder_url" />
			</p>
			<p>
				<input type="submit" value="+ <?php echo esc_html__( 'Add feed', 'feeder' ); ?>">
			</p>
		</form>
	</div>

</article>
