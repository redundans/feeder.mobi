<?php
/**
 * Template Name: User settings
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

			get_template_part( 'template-parts/content', 'settings' );

		endwhile; // End of the loop.

		?>

		</main>
	</div>

<?php
get_footer();
