<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package feeder
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'my-16' ); ?>>
	<header>
		<?php
		the_title( '<h1 class="font-bold text-3xl my-6">', '</h1>' );

		if ( 'post' === get_post_type() ) :
			?>
			<div class="entry-meta my-3">
				<?php
				feeder_wp_posted_on();
				feeder_wp_posted_by();
				?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</header>

	<?php feeder_wp_post_thumbnail(); ?>

	<div class="entry-content my-3">
		<?php the_content(); ?>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
