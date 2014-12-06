<?php
/**
 * Plugin Name:    Mixed Content Detector
 * Plugin URI:     https://github.com/tollmanz/wordpress-mixed-content-detector
 * Description:    A tool for proactively detecting mixed content issues in TLS enabled WordPress websites.
 * Version:        1.0.0.
 * Author:         Zack Tollman
 * Author URI:     http://tollmanz.com
 * License:        GPLv2 or later
 * License URI:    http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! class_exists( 'MCD_Mixed_Content_Detector' ) ) :
/**
 * Bootstrap functionality for the plugin.
 *
 * @since 1.0.0.
 */
class MCD_Mixed_Content_Detector {
	/**
	 * Current plugin version.
	 *
	 * @since 1.0.0
	 *
	 * @var   string    The semantically versioned plugin version number.
	 */
	var $version = '1.0.0';

	/**
	 * File path to the plugin dir (e.g., /var/www/mysite/wp-content/plugins/mixed-content-detector).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    Path to the root of this plugin.
	 */
	var $root_dir = '';

	/**
	 * File path to the plugin main file (e.g., /var/www/mysite/wp-content/plugins/mixed-content-detector/mixed-content-detector.php).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    Path to the plugin's main file.
	 */
	var $file_path = '';

	/**
	 * The URI base for the plugin (e.g., http://domain.com/wp-content/plugins/mixed-content-detector).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The URI base for the plugin.
	 */
	var $url_base = '';

	/**
	 * The one instance of MCD_Mixed_Content_Detector.
	 *
	 * @since 1.0.0.
	 *
	 * @var   MCD_Mixed_Content_Detector
	 */
	private static $instance;

	/**
	 * Instantiate or return the one MCD_Mixed_Content_Detector instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return MCD_Mixed_Content_Detector
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Create a new section.
	 *
	 * @since  1.0.0.
	 *
	 * @return MCD_Mixed_Content_Detector
	 */
	public function __construct() {
		// Set the main paths for the plugin
		$this->root_dir  = dirname( __FILE__ );
		$this->file_path = $this->root_dir . '/' . basename( __FILE__ );
		$this->url_base  = untrailingslashit( plugins_url( '/', __FILE__ ) );

		// Include dependent files
		include $this->root_dir . '/modes/live-check.php';
	}
}
endif;

/**
 * Get the one instance of the MCD_Mixed_Content_Detector.
 *
 * @since  1.0.0.
 *
 * @return MCD_Mixed_Content_Detector
 */
function mcd_get_mixed_content_detector() {
	return MCD_Mixed_Content_Detector::instance();
}

mcd_get_mixed_content_detector();