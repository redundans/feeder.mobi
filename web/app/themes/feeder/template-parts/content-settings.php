<?php
/**
 * Template part for displaying settings content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package feeder
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'my-16' ); ?>>

	<?php
	the_content();
	?>

	<section class="my-12">
		<header>
			<h1 class="screen-heading general-settings-screen text-3xl font-bold my-3"><?php esc_html_e( 'Kindle device settings', 'feeder' ); ?></h1>
		</header><!-- .entry-header -->
		<p>
			<?php echo wp_kses( __( 'This settings are important for Feeder.mobi to be able to send the RSS feed to your device.', 'feeder' ), array( 'code' => array() ) ); ?>
		</p>

		<?php
		$user         = wp_get_current_user();
		$user_settigs = new Feeder_Settings( $user );
		?>
		<form id="settingsform" method="post" action="" class="my-6">

			<?php wp_nonce_field( 'feeder_settings' ); ?>
			<p class="my-6">
				<label for="feeder_email" class="block my-3 font-bold"><?php echo esc_html__( 'Your device email', 'feeder' ); ?></label>
				<input type="email" id="feeder_email" name="feeder_email" class="border border-slate-200 p-3 w-full font-mono text-sm" value="<?php echo esc_html( $user_settigs->get_setting( 'email' ) ); ?>" />
			</p>
			<p class="my-6">
				<label for="feeder_schedule" class="block my-3 font-bold"><?php echo esc_html__( 'Scheduled delivery', 'feeder' ); ?></label>
				<select id="feeder_schedule" name="feeder_schedule" class="border border-slate-200 p-3 w-full font-mono text-sm">
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
	
				<small class="text-sm">Last parsed <?php echo esc_html( human_time_diff( (int) $user_settigs->get_setting( 'last' ), (int) current_time( 'timestamp' ) ) ); ?> ago. Next time in <?php echo esc_html( human_time_diff( (int) $user_settigs->get_setting( 'next' ), (int) current_time( 'timestamp' ) ) ); ?>.</small>
			</p>
			<p class="my-6">
				<input type="submit" class="bg-black text-white px-3 py-2 cursor-pointer" value="<?php echo esc_html__( 'Save', 'feeder' ); ?>">
			</p>
		</form>
	</section>

	<section class="my-12">
		<header>
			<h2 class="font-bold text-2xl my-3">Whitelist the Push to Kindle e-mail address</h2>
		</header>
	
		<p>Amazon requires all documents sent to your Send-to-Kindle address to come from an approved e-mail address. So the last step in this stage is to add feeder.mobi's sending address as an approved address.</p>
		
		<ol class="list-decimal my-6 mx-6 flex flex-col gap-3">
			<li>Load the 'Content and Devices' page on Amazon</li>
			<li>Click the 'Preferences' tab at the top of the page</li>
			<li>Click 'Personal Document Settings'</li>
			<li>Scroll down to the 'Approved Personal Document E-Mail List' section</li>
			<li>Click 'Add a new approved e-mail address'</li>
			<li>Enter 'delivery@feeder.mobi' and click 'Add Address'</li>
		</ol>
		
	</section>
	
	<section class="my-12">
		<header>
			<h2 class="font-bold text-2xl my-3">Test delivery</h2>
			<p>Press the button bellow to test your settings. A mail with the last schedule should then be deleivered to the device e-mail address you entered above.</p>
		</header>
	</section>
	
	<button class="bg-black text-white px-3 py-2 cursor-pointer" onClick="getTestMobi()">Test schedule</button>
</article><!-- #post-<?php the_ID(); ?> -->
