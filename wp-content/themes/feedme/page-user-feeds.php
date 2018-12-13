<?php
/**
 * Template Name: User feeds
 *
 * @package feedme
 */

if ( is_user_logged_in() ) {
	get_template_part( 'template-parts/content', 'feeds' );
}
