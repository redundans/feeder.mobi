<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package feeder
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header>
		<?php
		the_title( '<h1>', '</h1>' );

		if ( 'post' === get_post_type() ) :
			?>
			<div class="entry-meta">
				<?php
				feeder_wp_posted_on();
				feeder_wp_posted_by();
				?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</header>

	<?php feeder_wp_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'feeder' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);
		if ( ! is_singular() ) :
			echo wp_kses(
				'<a href="' . get_permalink() . '">' . __( 'Read more', 'feeder' ) . '</a>',
				[
					'a' => [
						'href' => [],
					],
				]
			);
		endif;
		?>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
