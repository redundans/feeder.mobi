<?php
/**
 * Template part for displaying user feeds content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package feedme
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header>
		<?php the_title( '<h1>', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<?php
	the_content();
	?>

	<div id="feeds">
		<form id="deleteform" method="post" action="<?php echo esc_url( $current_url ); ?>">
			<?php wp_nonce_field( 'feedme_delete' ); ?>

		<?php
		global $wp;

		$user        = wp_get_current_user();
		$user_feeds  = new Feedme_Feeds( $user );
		$feeds       = $user_feeds->get_feeds();
		$current_url = home_url( add_query_arg( array(), $wp->request ) );

		if ( $feeds->have_posts() ) {

			echo '<table>';

			while ( $feeds->have_posts() ) {
				$feeds->the_post();
				$url = get_post_meta( get_the_ID(), 'url', true );
				?>
				<tr>
					<td>
						<input type="checkbox" value="<?php the_ID(); ?>" name="feedme_delete[]">
					</td>
					<td>
						<strong><?php the_title(); ?></strong><br/>
						<?php echo esc_url( $url ); ?>
					</td>
				</tr>
				<?php
			}

			echo '</table>';
			echo '<p>input type="submit" value="' . esc_html__( 'Remove feed', 'feedme' ) . '"></p>';

			wp_reset_postdata();
		} else {
			echo esc_html__( 'Sorry, no feeds could be found.', 'feedme' );
		}
		?>
		</form>

		<form id="feedsform" method="post" action="<?php echo esc_url( $current_url ); ?>">
			<?php wp_nonce_field( 'feedme_feeds' ); ?>
			<p>
				<input type="url" placeholder="http://" id="feedme_url" name="feedme_url" />
			</p>
			<p>
				<input type="submit" value="<?php echo esc_html__( 'Add feed', 'feedme' ); ?>">
			</p>
		</form>
	</div>

</article>
