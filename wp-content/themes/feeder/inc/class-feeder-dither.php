<?php
/**
 * Feeder Dithering Functions
 *
 * @package feeder
 */

/**
 * This class for the dithering of images and rest functions.
 */
class Feeder_Dither {

	/**
	 * Instantiate class actions.
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'registet_endpoint' ] );
	}

	/**
	 * Register REST API endpoints.
	 */
	public function registet_endpoint() {
		register_rest_route(
			'feeder/v1',
			'/dither',
			array(
				'methods'  => 'GET',
				'callback' => [
					$this,
					'dither_image',
				],
			)
		);
	}

	/**
	 * Dither a image.
	 */
	public function dither_image( $request ) {
		$url = $request['url'];
		if ( extension_loaded( 'imagick' )
			&& isset( $url ) ) {
			$remote_image = file_get_contents( $url );
			$imagick      = new Imagick();

			$imagick->readImageBlob( $remote_image );

			$width  = $imagick->getImageWidth();
			$height = $imagick->getImageHeight();
			if ( $width > 920 ) {
				$imagick->thumbnailImage( 640, null, false );
			} elseif ( $height > 920 ) {
				$imagick->thumbnailImage( null, 640, false );
			}
			//$imagick->quantizeImage( 2, Imagick::COLORSPACE_GRAY, 0, true, true );
			$imagick->modulateImage( 100, 0, 100 );
			$imagick->orderedPosterizeImage( 'o4x4,3,3', Imagick::CHANNEL_GRAY );
			$imagick->setImageFormat( 'png' );
			header( 'Content-type: image/png' );
			echo $imagick->getImageBlob();
		}
	}
}

$feeder_dither = new Feeder_Dither();
