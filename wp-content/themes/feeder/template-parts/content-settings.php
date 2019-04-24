<?php
/**
 * Template part for displaying settings content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package feeder
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header>
		<h2 class="screen-heading general-settings-screen"><?php esc_html_e( 'Kindle device settings', 'feeder' ); ?></h2>
	</header><!-- .entry-header -->

	<p><?php echo wp_kses( __( 'This settings are important for <code>Feeder.mobi</code> to be able to send the RSS feed to your device.', 'feeder' ), [ 'code' => [] ] ); ?></p>

	<?php
	$user         = wp_get_current_user();
	$user_settigs = new Feeder_Settings( $user );

	?>

	<form id="settingsform" method="post" action="<?php echo esc_url( $current_url ); ?>">

		<?php wp_nonce_field( 'feeder_settings' ); ?>
		<p>
			<label for="feeder_email"><?php echo esc_html__( 'Your device email', 'feeder' ); ?></label>
			<input type="email" id="feeder_email" name="feeder_email" value="<?php echo esc_html( $user_settigs->get_setting( 'email' ) ); ?>" />
		</p>
		<p>
			<label for="feeder_schedule"><?php echo esc_html__( 'Scheduled delivery', 'feeder' ); ?></label>
			<select id="feeder_schedule" name="feeder_schedule">
				<?php
				$allowed_keys = array(
					'tomorrow 06:00'  => 'Daily',
					'next week 06:00' => 'Weekly',
				);
				foreach ( $allowed_keys as $key => $value ) {
					echo wp_kses( '<option value="' . $key . '"' . ( $user_settigs->get_setting( 'schedule' ) === $key ? ' selected' : '' ) . '>' . $value . '</option>', [ 'option' => [ 'value' => [] ] ] );
				}
				?>
			</select>

			<small>Last parsed <?php echo esc_html( human_time_diff( (int) $user_settigs->get_setting( 'last' ), (int) current_time( 'timestamp' ) ) ); ?> ago. Next time in <?php echo esc_html( human_time_diff( (int) $user_settigs->get_setting( 'next' ), (int) current_time( 'timestamp' ) ) ); ?>.</small>
		</p>
		<p>
			<input type="submit" value="<?php echo esc_html__( 'Save', 'feeder' ); ?>">
		</p>
	</form> <button onClick="getTestMobi()">Test schedule</button>

</article><!-- #post-<?php the_ID(); ?> -->
