<?php
/**
 * Template part for displaying settings content in page.php
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

	$user         = wp_get_current_user();
	$user_settigs = new Feedme_Settings( $user );

	?>

	<form id="settingsform" method="post" action="<?php echo esc_url( $current_url ); ?>">

		<?php wp_nonce_field( 'feedme_settings' ); ?>
		<input type="hidden" name="id" value=" <?php echo esc_html( $user->ID ); ?> " />
		<p>
			<label for="feedme_email"><?php echo esc_html__( 'Your device email', 'feedme' ); ?></label>
			<input type="email" id="feedme_email" name="feedme_email" value="<?php echo esc_html( $user_settigs->get_setting( 'email' ) ); ?>" />
		</p>
		<p>
			<label for="feedme_schedule"><?php echo esc_html__( 'Scheduled delivery', 'feedme' ); ?></label>
			<select id="feedme_schedule" name="feedme_schedule">
				<?php
				$allowed_keys = array(
					'08:00 tomorrow'  => 'Daily',
					'08:00 next week' => 'Weekly',
				);
				foreach ( $allowed_keys as $key => $value ) {
					echo ( '<option value="' . $key . '"' . ( $user_settigs->get_setting( 'schedule' ) === $key ? ' selected' : '' ) . '>' . $value . '</option>' );
				}
				?>
			</select>
			<small>Last parsed <?php echo esc_html( human_time_diff( (int) $user_settigs->get_setting( 'last' ), (int) current_time( 'timestamp' ) ) ); ?> ago.</small>
		</p>
		<p>
			<input type="submit" value="<?php echo esc_html__( 'Save', 'feedme' ); ?>">
		</p>
	</form> <button onClick="getTestMobi()">Test</button>

</article><!-- #post-<?php the_ID(); ?> -->
