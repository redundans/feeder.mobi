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
			<h1 class="screen-heading general-settings-screen text-3xl font-bold my-3"><?php esc_html_e( 'Kindle screensaver', 'feeder' ); ?></h1>
		</header><!-- .entry-header -->
		<p>
			<?php echo wp_kses( __( 'Here you will be able to upload an image that will be transformed to an png image sutable for Kindle Screensaver.', 'feeder' ), array( 'code' => array() ) ); ?>
		</p>

		<?php
		$user         = wp_get_current_user();
		$user_settigs = new Feeder_Settings( $user );
		?>
		<form id="settingsform" method="post" action="" class="my-6" enctype="multipart/form-data">
			<?php wp_nonce_field( 'feeder_screensaver' ); ?>
			<p class="my-6">
				<label for="feeder_email" class="block my-3 font-bold"><?php echo esc_html__( 'An image file', 'feeder' ); ?></label>
				<input type="file" id="feeder_screensaver" name="feeder_screensaver" class="border border-slate-200 p-3 w-full font-mono text-sm" />
			</p>
			<p class="my-6">
				<input type="submit" class="bg-black text-white px-3 py-2 cursor-pointer" value="<?php echo esc_html__( 'Upload', 'feeder' ); ?>">
			</p>
		</form>
	</section>
	<?php
	$image = abs_path_to_url( $user_settigs->get_setting( 'screensaver' ) );
	if ( ! empty ( $image ) ) :
		?>
	<section class="my-12">
		<header>
			<h2 class="font-bold text-2xl my-3">Latest uploaded image</h2>
		</header>
		<p class="my-12">
			<img src="<?php echo esc_url(  $image  ); ?>?<?php echo esc_attr( rand() ); ?>">
		</p>
		<h3 class="font-bold text-xl my-3">Static url</h3>
		<p>
			You can use this url with hacks like <a href="https://matthealy.com/kindle" target="_blank">this one</a> to update your kindle screensaver. The url will allways be the same even if you upload a new image.
		</p>
		<p>
			<code><?php echo esc_url(  $image  ); ?></code>
		</p>
	</section>
		<?php
	endif;
	?>
</article>