<?php
/**
 * Plugin Name: A faster load_textdomain
 * Version: 0.0.1
 * Description: While we're wating for https://core.trac.wordpress.org/ticket/32052.
 * Author: Per Soderlind
 * Author URI: https://soderlind.no
 * Plugin URI: https://gist.github.com/soderlind/610a9b24dbf95a678c3e
 * License: GPL
 * Save the plugin in mu-plugins. You don't have to, but you should add an an object cache. See benchmarks at https://core.trac.wordpress.org/ticket/32052#comment:7
 * Credit: nicofuma , I just created the plugin based on his patch (https://core.trac.wordpress.org/ticket/32052)
 *
 * @package feeder
 **/

add_filter(
	'override_load_textdomain',
	function( $retval, $domain, $mofile ) {
		global $l10n;
		if ( ! is_readable( $mofile ) ) {
			return false;
		}
		$data = get_transient( md5( $mofile ) );
		$mtime = filemtime( $mofile );
		$mo = new MO();
		if ( ! $data || ! isset( $data['mtime'] ) || $mtime > $data['mtime'] ) {
			if ( ! $mo->import_from_file( $mofile ) ) {
				return false;
			}
			$data = array(
				'mtime'   => $mtime,
				'entries' => $mo->entries,
				'headers' => $mo->headers,
			);
			set_transient( md5( $mofile ), $data );
		} else {
			$mo->entries = $data['entries'];
			$mo->headers = $data['headers'];
		}
		if ( isset( $l10n[ $domain ] ) ) {
			$mo->merge_with( $l10n[ $domain ] );
		}
		$l10n[ $domain ] = &$mo;
		return true;
	},
	1,
	3
);
