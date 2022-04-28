<?php
/**
 * Template part for displaying user feeds content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package feeder
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'my-16' ); ?>>
	<header>
		<?php the_title( '<h1 class="screen-heading general-settings-screen text-3xl font-bold">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<?php
	the_content();
	?>

	<section id="feeds" class="my-12">
		<h2 class="text-2xl font-bold"><?php esc_html_e( 'Your feeds', 'feeder' ); ?></h2>
		<p>
			<?php esc_html_e( 'This is the feeds that you are currently subscribing to.', 'feeder' ); ?>
		</p>
		<form id="deleteform" method="post" class="my-6" action="">
			<?php wp_nonce_field( 'feeder_feed_delete', 'feeder_feed_nounce' ); ?>

		<?php
		global $wp;
		$user        = wp_get_current_user();
		$user_feeds  = new Feeder_Feeds( $user );
		$my_feeds    = $user_feeds->get_feeds();
		$current_url = home_url( add_query_arg( array(), $wp->request ) );
		if ( $my_feeds->have_posts() ) :
			?>
			<table class='table-auto border-collapse border border-slate-200 w-full font-mono text-sm'>
				<tbody>
			<?php

			while ( $my_feeds->have_posts() ) {
				$my_feeds->the_post();
				$url = get_post_meta( get_the_ID(), 'url', true );
				?>
				<tr class="flex flex-row items-center border-b border-slate-200">
					<td class="mx-6 mr-0">
						<label class="checkbox">
							<input type="checkbox" value="<?php the_ID(); ?>" name="feeder_delete[]">
							<span class="checkmark"></span>
						</label>
					</td>
					<td class="my-3 mx-6">
						<strong class="font-bold text-base font-sans"><?php the_title(); ?></strong><br/>
						<a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php echo esc_url( $url ); ?></a><br/>
						<small class="text-sm text-slate-400">Updated: <?php feeder_last_updated(); ?></small>
					</td>
				</tr>
				<?php
			}

			?>
				</tbody>
			</table>
			<div class="my-6">
				<input type="submit" class="bg-black text-white px-3 py-2 cursor-pointer" value="- <?= esc_html__( 'Remove feed', 'feeder' ) ?>">
			</div>
			<?php

			wp_reset_postdata();
		else :
			echo wp_kses( __( '<p>Sorry, no feeds could be found.</p>', 'feeder' ), [ 'p' => [] ] );
		endif;
		?>
		</form>
	</section>
	
	<section>
		<h2 class="text-2xl font-bold"><?php esc_html_e( 'Add a feed', 'feeder' ); ?></h2>
		<p>
			<?php esc_html_e( 'Start subscribing to a rss by entering the url to the Atom/RSS and click + Add feed.', 'feeder' ); ?>
		</p>
		<form id="feedsform" method="post" class="my-6" action="<?php echo esc_url( $current_url ); ?>">
			<?php wp_nonce_field( 'feeder_feed_add', 'feeder_feed_nounce' ); ?>
			<p>
				<input type="url" class="border border-slate-200 p-3 w-full font-mono text-sm" placeholder="http://" id="feeder_url" name="feeder_url" />
			</p>
			<p>
				<input type="submit" class="bg-black text-white px-3 py-2 cursor-pointer" value="+ <?php echo esc_html__( 'Add feed', 'feeder' ); ?>">
			</p>
		</form>
	</section>

</article>
