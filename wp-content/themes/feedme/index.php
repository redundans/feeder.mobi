<?php
/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package feedme
 */

get_header();
?>

	<div id="primary">
		<main id="main">

		<?php feedme_error_message(); ?>

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

				echo '<div id="' . esc_html( $post->post_name ) . '" class="section">';

				/*
				 * Include the Post-Type-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
				 */
				$template = get_page_template_slug( $post->ID );
				if ( ! empty( $template ) ) {
					include $template;
				} else {
					get_template_part( 'template-parts/content', get_post_type() );
				}

				echo '</div>';
			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

		</main>
	</div>

<?php
get_footer();
