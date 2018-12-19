<?php
/**
 * Feedme Scheduling
 *
 * @package feedme
 */

use PHPePub\Core\EPub;
use PHPePub\Helpers\CalibreHelper;

/**
 * This class handles all scheduling.
 */
class Feedme_Scheduling {

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
	private $prefix = 'feedme_';

	/**
	 * Instantiate class variables.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'setup_scheduling' ) );
		add_action( 'feedme_run_single_schedule', array( $this, 'run_single_schedule' ), 10, 1 );
		add_action( 'feedme_run_scheduling', array( $this, 'run_scheduling' ) );
	}

	/**
	 * Setup new single schedule.
	 *
	 * @param WP_User $user A user object.
	 */
	private static function add_single_schedule( $user ) {
		as_schedule_single_action(
			current_time( 'timestamp' ),
			'feedme_run_single_schedule',
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
				'meta_key'     => 'feedme_next',
				'meta_value'   => current_time( 'timestamp' ),
				'meta_compare' => '<',
			)
		);

		if ( ! empty( $user_query->get_results() ) ) {
			foreach ( $user_query->get_results() as $user ) {
				$user_settings = new Feedme_Settings( $user );
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
		if ( false === as_next_scheduled_action( 'feedme_run_scheduling' ) ) {
			as_schedule_recurring_action( strtotime( '+1 hours' ), DAY_IN_SECONDS, 'feedme_run_scheduling' );
		}
	}

	/**
	 * Genereate mobi and sent it to user.
	 *
	 * @param int $user_id User ID from the hook.
	 */
	public static function run_single_schedule( int $user_id ) {
		$user          = get_user_by( 'ID', $user_id );
		$user_settings = new Feedme_Settings( $user );
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
				'post_type'      => 'feedme_feed',
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
					if ( strtotime( $item->get_date( 'Y-m-d h:i:s' ) ) > $last ) {
						$chapters[ $slug ]['title']       = $title;
						$chapters[ $slug ]['description'] = '<h1>' . $title . '</h1>' . $description;

						$content = strip_tags( $item->get_content(), '<p><strong><b><em><i><a><ul><li><blockquote><h1><h2><h3><h4><h5><h6>' );

						$chapters[ $slug ]['items'][] = (object) array(
							'title'   => $item->get_title(),
							'content' => '<h1>' . $item->get_title() . '</h1>' . $content,
						);
					}
				}
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
		// file_put_contents( $file_dir . '/output.txt', print_r( $chapters, true ), FILE_APPEND | LOCK_EX );

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
		$book->setAuthor( 'feeder.mobi', 'feedme' );

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
		$user_settings = new Feedme_Settings( $user );
		$schedule      = $user_settings->get_setting( 'schedule' );
		$title         = 'Your ' . ( '08:00 tomorrow' === $schedule ? 'daily' : 'weekly' ) . ' update';
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
			$output = shell_exec( '/usr/bin/kindlegen ' . $epub . ' -o ' . basename( $mobi ) );
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
			$email       = get_user_meta( $user->ID, 'feedme_email', true );
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
}

new Feedme_Scheduling();
