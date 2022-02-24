<?php
/**
 * Template part for displaying settings content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package feeder
 */

?>

<article>
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
					echo wp_kses( '<option value="' . $key . '"' . ( $user_settigs->get_setting( 'schedule' ) === $key ? ' selected' : '' ) . '>' . $value . '</option>', [ 'option' => [ 'value' => [], 'selected' => [] ] ] );
				}
				?>
			</select>

			<small>Last parsed <?php echo esc_html( human_time_diff( (int) $user_settigs->get_setting( 'last' ), (int) current_time( 'timestamp' ) ) ); ?> ago. Next time in <?php echo esc_html( human_time_diff( (int) $user_settigs->get_setting( 'next' ), (int) current_time( 'timestamp' ) ) ); ?>.</small>
		</p>
		<p>
			<input type="submit" value="<?php echo esc_html__( 'Save', 'feeder' ); ?>">
		</p>
	</form>

	<header>
		<h2>Whitelist the Push to Kindle e-mail address</h2>
	</header>
	
	<p>Amazon requires all documents sent to your Send-to-Kindle address to come from an approved e-mail address. So the last step in this stage is to add feeder.mobi's sending address as an approved address.</p>
	
	<ol>
		<li>Load the 'Content and Devices' page on Amazon</li>
		<li>Click the 'Preferences' tab at the top of the page</li>
		<li>Click 'Personal Document Settings'</li>
		<li>Scroll down to the 'Approved Personal Document E-Mail List' section</li>
		<li>Click 'Add a new approved e-mail address'</li>
		<li>Enter 'delivery@feeder.mobi' and click 'Add Address'</li>
	</ol>

	<br/>
	<br/>
	
	<header>
		<h2>Test scheduled delivery</h2>
		<p>Press the button bellow to test your settings. A mail with the last schedule should then be deleivered to the device e-mail address you entered above.</p>
	</header>
	
	<button onClick="getTestMobi()">Test schedule</button>
<!--
	<br/>
	<br/>
	<br/>

	<header>
		<h2 class="screen-heading general-settings-screen"><?php esc_html_e( 'Adobe encryption settings', 'feeder' ); ?></h2>
	</header>

	<p><?php echo wp_kses( __( 'The experimental function for removing Adove DRM from epub books, before converting them to MOBI and sending it to your device, needs a adobe encryption key from the same account that created the epub with Adobe Digital Editions.', 'feeder' ), [ 'code' => [] ] ); ?></p>
	
	<?php
		$adobe_key           = $user_settigs->get_setting( 'adobe_key' );
		$adobe_key_file      = get_attached_file( $adobe_key, true );
		$adobe_key_file_only = basename( $adobe_key_file );
		if ( ! empty( $adobe_key_file_only ) ) {
			echo "<p>Existing file: <em>{$adobe_key_file_only}</em></p>";
		}
	?>

	<form method="post" enctype="multipart/form-data">
		<?php wp_nonce_field( 'adobe_settings' ); ?>

		<p>
			<label for="test_upload_pdf"><?php echo esc_html__( 'Upload adobe encryption key', 'feeder' ); ?></label>
        	<input type='file' id='test_upload_pdf' name='test_upload_pdf'></input>
    	</p>
    	<p>
			<input type="submit" value="<?php echo esc_html__( 'Upload', 'feeder' ); ?>">
		</p>
    </form>
	-->
</article><!-- #post-<?php the_ID(); ?> -->
