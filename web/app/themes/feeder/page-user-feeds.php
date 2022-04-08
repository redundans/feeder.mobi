<?php
/**
 * Template Name: User feeds
 *
 * @package feeder
 */

get_header();
?>

	<div id="primary">
		<main id="main">

		<?php feeder_error_message(); ?>

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'feeds' );

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.

		?>

		</main>
	</div>

<?php
get_footer();
