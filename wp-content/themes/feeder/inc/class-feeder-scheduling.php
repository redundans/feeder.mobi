<?php
/**
 * Feeder Scheduling
 *
 * @package feeder
 */

use PHPePub\Core\EPub;
use PHPePub\Helpers\CalibreHelper;

/**
 * This class handles all scheduling.
 */
class Feeder_Scheduling {

	/**
	 * A user object set up by __construct.
	 *
	 * @var WP_User $user.
	 */
	private $user;

	/**
	 * A prefix so no user_meta keys may be duplucates with other plugin variables.
	 *
	 * @var string $prefix.
	 */
	private $prefix = 'feeder_';

	/**
	 * Instantiate class variables.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'setup_scheduling' ) );
		add_action( 'feeder_run_single_schedule', array( $this, 'run_single_schedule' ), 10, 1 );
		add_action( 'feeder_run_scheduling', array( $this, 'run_scheduling' ) );
		add_action( 'wp_ajax_run_test_schedule', array( $this, 'run_test_schedule' ) );
	}

	/**
	 * Setup new single schedule.
	 *
	 * @param WP_User $user A user object.
	 */
	private static function add_single_schedule( $user ) {
		as_schedule_single_action(
			current_time( 'timestamp' ),
			'feeder_run_single_schedule',
			array(
				$user->ID,
			)
		);
	}

	/**
	 * A callback to run when the 'eg_midnight_log' scheduled action is run.
	 */
	public function run_scheduling() {
		$user_query = new WP_User_Query(
			array(
				'meta_key'     => 'feeder_next', // phpcs:ignore
				'meta_value'   => current_time( 'timestamp' ), // phpcs:ignore
				'meta_compare' => '<',
			)
		);

		if ( ! empty( $user_query->get_results() ) ) {
			foreach ( $user_query->get_results() as $user ) {
				$user_settings = new Feeder_Settings( $user );
				$schedule      = $user_settings->get_setting( 'schedule' );
				$next          = strtotime( $schedule );
				$last          = current_time( 'timestamp' );

				self::add_single_schedule( $user );
				$user_settings->set_setting( 'next', $next );
			}
		}
	}

	/**
	 * Schedule an action with the hook 'eg_midnight_log' to run at midnight each day
	 * so that our callback is run then.
	 */
	public function setup_scheduling() {
		if ( false === as_next_scheduled_action( 'feeder_run_scheduling' ) ) {
			as_schedule_recurring_action( strtotime( '+1 hours' ), HOUR_IN_SECONDS, 'feeder_run_scheduling' );
		}
	}

	/**
	 * Genereate test mobi and return a download link.
	 */
	public static function run_test_schedule() {
		$user          = wp_get_current_user();
		$user_settings = new Feeder_Settings( $user );
		$schedule      = $user_settings->get_setting( 'schedule' );
		$last          = strtotime( ( 'tomorrow 06:00' === $schedule ? 'tomorrow 06:00' : 'last week 06:00' ) );
		$feeds         = self::get_user_feeds( $user );
		$chapters      = self::prepare_chapters_from_feeds( $feeds, $last );
		$epub          = self::create_epub_from_chapters( $chapters, $user );
		$mobi          = self::create_mobi_from_epub( $epub );
		$mail          = self::mail_mobi( $user, $mobi );
		$attachement   = self::feeder_handle_upload_from_path( $mobi, true );
		$notification  = self::notify_user( $user, $attachement );

		echo wp_json_encode(
			array(
				'error' => ( false === $mail ? true : false ),
			)
		);
		wp_die();
	}

	/**
	 * Genereate mobi and sent it to user.
	 *
	 * @param int $user_id User ID from the hook.
	 */
	public static function run_single_schedule( int $user_id ) {
		$user          = get_user_by( 'ID', $user_id );
		$user_settings = new Feeder_Settings( $user );
		$last          = (int) $user_settings->get_setting( 'last' );
		$feeds         = self::get_user_feeds( $user );
		$chapters      = self::prepare_chapters_from_feeds( $feeds, $last );
		$epub          = self::create_epub_from_chapters( $chapters, $user );
		$mobi          = self::create_mobi_from_epub( $epub );
		$mail          = self::mail_mobi( $user, $mobi );

		$user_settings->set_setting( 'last', current_time( 'timestamp' ) );
	}

	/**
	 * Getting all the user feeds.
	 *
	 * @param WP_User $user The current user object.
	 */
	public static function get_user_feeds( $user ) : array {
		return get_posts(
			array(
				'author'         => $user->ID,
				'posts_per_page' => -1,
				'post_type'      => 'feeder_feed',
				'post_status'    => 'publish',
			)
		);
	}

	/**
	 * Getting all the feed items as chapters.
	 *
	 * @param array $feeds An array of feeds.
	 * @param int   $last An unix time of last time scheduled.
	 * @return mixed.
	 */
	public static function prepare_chapters_from_feeds( $feeds, $last ) {
		$chapters = array();
		foreach ( $feeds as $feed_object ) {
			$feed_url = get_post_meta( $feed_object->ID, 'url', true );
			$feed     = new SimplePie();

			$feed->enable_cache( false );
			$feed->set_feed_url( $feed_url );

			$success = $feed->init();
			if ( $success ) {
				$slug        = sanitize_title( $feed->get_title() );
				$description = $feed->get_description();
				$title       = $feed->get_title();
				$items       = $feed->get_items();

				foreach ( $items as $item ) {
					if ( strtotime( $item->get_date( 'Y-m-d H:i:s O' ) ) > $last ) {
						$chapters[ $slug ]['title']       = $title;
						$chapters[ $slug ]['description'] = '<h1>' . $title . '</h1>' . $description;

						$content = strip_tags( $item->get_content(), '<p><strong><b><em><i><a><ul><li><blockquote><h1><h2><h3><h4><h5><h6>' );

						$chapters[ $slug ]['items'][] = (object) array(
							'title'   => $item->get_title(),
							'content' => '<h1>' . $item->get_title() . '</h1>' . $content,
						);
					}
				}

				$last_update = self::gmt_to_local_timestamp( strtotime( $items[0]->get_date( 'Y-m-d H:i:s O' ) ) );
				update_post_meta( $feed_object->ID, 'feed_updated', $last_update );
			}
		}
		return $chapters;
	}

	/**
	 * Creates an epub file and returns its file url.
	 *
	 * @param array   $chapters An array of prepared chapters.
	 * @param WP_User $user An user object.
	 * @return mixed.
	 */
	public static function create_epub_from_chapters( $chapters, $user ) {
		if ( empty( $chapters ) ) {
			return null;
		}

		$upload_dir = wp_upload_dir();
		$title      = self::get_epub_title( $user );
		$file_name  = 'latest_book.epub';
		$file_dir   = $upload_dir['basedir'] . '/' . $user->user_login;
		if ( ! file_exists( $file_dir ) ) {
			wp_mkdir_p( $file_dir );
		}

		$content_start =
			"<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
			. "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n"
			. "    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n"
			. "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n"
			. "<head>\n"
			. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n"
			. "<title>$title</title>\n"
			. "</head>\n"
			. "<body>\n";
		$book_end      = "</body>\n</html>\n";

		$book = new EPub();

		// Title and Identifier are mandatory!
		$book->setTitle( $title );
		$book->setDescription( 'Your automatic updated feeds from feeder.mobi.' );
		$book->setAuthor( 'feeder.mobi', 'feeder' );

		// Set chapter indexing.
		$index    = 1;
		$subindex = 1;

		foreach ( $chapters as $chapter ) {
			if ( ! empty( $chapter['items'] ) ) {

				// Add feed intro chapter.
				$content = $content_start . $chapter['description'] . $book_end;
				$book->addChapter( $chapter['title'], 'Chapter' . $index . '.html', $content, true, EPub::EXTERNAL_REF_IGNORE );

				// Add real chapters.
				$book->subLevel();
				foreach ( $chapter['items'] as $key => $item ) {
					$content = $content_start . $item->content . $book_end;
					$book->addChapter( $item->title, 'Chapter' . $index . $subindex . '.html', $content, true, EPub::EXTERNAL_REF_IGNORE );
					$subindex++;
				}
				$book->backLevel();
				$subindex = 1;
			}
			$index++;
		}

		$book->finalize(); // Finalize the book, and build the archive.
		$book->saveBook( $file_name, $file_dir );
		return $file_dir . '/' . $file_name;
	}

	/**
	 * Creates at title from the user settings.
	 *
	 * @param WP_User $user An epub file url.
	 * @return string
	 */
	private function get_epub_title( $user ): string {
		$user_settings = new Feeder_Settings( $user );
		$schedule      = $user_settings->get_setting( 'schedule' );
		$title         = 'Your ' . ( 'tomorrow 06:00' === $schedule ? 'daily' : 'weekly' ) . ' update';
		return $title;
	}

	/**
	 * Creates an mobi file from the epub file url.
	 *
	 * @param string $epub An epub file url.
	 * @return mixed.
	 */
	public static function create_mobi_from_epub( $epub ) {
		if ( file_exists( $epub ) ) {
			$mobi   = str_replace( '.epub', '.mobi', $epub );
			$output = shell_exec( 'kindlegen ' . $epub . ' -o ' . basename( $mobi ) ); // phpcs:ignores
			return $mobi;
		}
		return false;
	}

	/**
	 * Sends the mobi as an attachement in a mail to the users registred device.
	 *
	 * @param WP_User $user An user object.
	 * @param string  $mobi An mobi file url.
	 * @return mixed.
	 */
	private static function mail_mobi( $user, $mobi ) {
		if ( file_exists( $mobi ) ) {
			$attachments = array( $mobi );
			$email       = get_user_meta( $user->ID, 'feeder_email', true );
			$headers     = array(
				'From: feeder.mobi <delivery@feeder.mobi>',
			);
			$headers     = array(
				'Content-Type: text/html; charset=UTF-8',
				'From: feeder.mobi <delivery@feeder.mobi>',
			);

			return wp_mail( $email, 'Your latest scheduled feed!', 'This mail contains the latest scheduled feed from <a href="' . esc_url( home_url( '/' ) ) . '">feeder.mobi</a>.', $headers, $attachments );
		} else {
			return false;
		}
	}

	/**
	 * Translates GMT to local WordPress timestamp.
	 *
	 * @param int $gmt_timestamp A unix timestamp.
	 */
	private static function gmt_to_local_timestamp( $gmt_timestamp ) {
		$iso_date        = date( 'Y-m-d H:i:s', $gmt_timestamp );
		$local_timestamp = get_date_from_gmt( $iso_date, 'U' );

		return $local_timestamp;
	}

	/**
	 * Takes a path to a file, simulates an upload and passes it through wp_handle_upload. If $add_to_media
	 * is set to true (default), the file will appear under Media in the dashboard. Otherwise, it's hidden,
	 * but stored in the uploads folder.
	 *
	 * Return Values: Similar to wp_handle_upload, but with attachment_id:
	 *  - Success: Returns an array including file, url, type, attachment_id.
	 *  - Failure: Returns an array with the key "error" and a value including the error message.
	 *
	 * From : https://gist.github.com/RadGH/3b544c827193927d1772s
	 *
	 * @param string $path The path to the file.
	 * @param bool   $add_to_media If the media shall be visable in the media.
	 *
	 * @return array
	 */
	public function feeder_handle_upload_from_path( $path, $add_to_media = true ) {
		if ( ! file_exists( $path ) ) {
			return false;
		}
		$filename        = basename( $path );
		$filename_no_ext = pathinfo( $path, PATHINFO_FILENAME );
		$extension       = pathinfo( $path, PATHINFO_EXTENSION );

		// Simulate uploading a file through $_FILES. We need a temporary file for this.
		$tmp      = tmpfile();
		$tmp_path = stream_get_meta_data( $tmp )['uri'];
		fwrite( $tmp, file_get_contents( $path ) );
		fseek( $tmp, 0 ); // If we don't do this, WordPress thinks the file is empty.

		$fake_file = [
			'name'     => $filename,
			'type'     => 'image/' . $extension,
			'tmp_name' => $tmp_path,
			'error'    => UPLOAD_ERR_OK,
			'size'     => filesize( $path ),
		];

		// Trick is_uploaded_file() by adding it to the superglobal.
		$_FILES[ basename( $tmp_path ) ] = $fake_file;
		// Handle the upload.
		$result = wp_handle_upload(
			$fake_file,
			[
				'test_form' => false,
				'action'    => 'local',
			]
		);
		fclose( $tmp ); // Close tmp file.
		@unlink( $tmp_path ); // Delete the tmp file. Closing it should also delete it, so hide any warnings with @.
		unset( $_FILES[ basename( $tmp_path ) ] ); // Clean up our $_FILES mess.
		$result['attachment_id'] = 0;

		if ( empty( $result['error'] ) && $add_to_media ) {
			$args   = [
				'post_title'     => $filename_no_ext,
				'post_content'   => '',
				'post_status'    => 'publish',
				'post_mime_type' => $result['type'],
			];
			$result = wp_insert_attachment( $args, $result['file'] );
			if ( is_wp_error( $result['attachment_id'] ) ) {
				$result = false;
			} else {
				$attach_data = wp_generate_attachment_metadata( $result['attachment_id'], $result['file'] );
				wp_update_attachment_metadata( $result['attachment_id'], $attach_data );
			}
		}
		return $result;
	}

	/**
	 * Send notification to user.
	 *
	 * @param WP_User $user A user object.
	 * @param int     $attachement A attachement id.
	 */
	public function notify_user( $user, $attachement ) {
		if ( $attachement ) {
			bp_notifications_add_notification(
				[
					'user_id'          => $user->ID,
					'item_id'          => $attachement,
					'component_name'   => 'feeder',
					'component_action' => 'feeder_sent_feed',
					'date_notified'    => bp_core_current_time(),
					'is_new'           => 1,
				]
			);
		}
	}
}

new Feeder_Scheduling();

