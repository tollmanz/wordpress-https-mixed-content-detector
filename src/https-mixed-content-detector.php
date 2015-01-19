<?php
/**
 * Plugin Name:    HTTPS Mixed Content Detector
 * Plugin URI:     https://github.com/tollmanz/wordpress-https-mixed-content-detector
 * Description:    A tool for proactively detecting mixed content issues in TLS enabled WordPress websites.
 * Version:        1.2.0
 * Author:         Zack Tollman
 * Author URI:     https://www.tollmanz.com
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
	var $version = '1.2.0';

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
	 * The one Violation_Location_Collection object.
	 *
	 * @since 1.2.0.
	 *
	 * @var   MCD_Violation_Location_Collection    Holds the violation location collector object.
	 */
	var $violation_location_collector;

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
		include $this->root_dir . '/config.php';
		include $this->root_dir . '/helpers.php';
		include $this->root_dir . '/beacon.php';
		include $this->root_dir . '/policy.php';

		// Load in WP CLI
		if ( defined('WP_CLI') && WP_CLI ) {
			include $this->root_dir . '/wp-cli.php';
		}

		// Load up the violation locations
		$this->setup_violation_locations();
	}

	/**
	 * Load all of the violation location information.
	 *
	 * @since  1.2.0.
	 *
	 * @return void
	 */
	public function setup_violation_locations() {
		// Load the Violation Location files
		include $this->root_dir . '/violation-locations/violation-location-collection.php';
		include $this->root_dir . '/violation-locations/violation-location-interface.php';
		include $this->root_dir . '/violation-locations/violation-location-base.php';

		// Create the collector
		$this->violation_location_collector = new MCD_Violation_Location_Collection();

		// Load in the violation location objects
		include $this->root_dir . '/violation-locations/content-base.php';
		include $this->root_dir . '/violation-locations/content-raw.php';
		$this->violation_location_collector->add( new MCD_Violation_Location_Content_Raw() );

		// Include the shortcode location and add all shortcodes as individual location
		include $this->root_dir . '/violation-locations/content-shortcode.php';

		// Get all shortcodes to register a location for each
		global $shortcode_tags;
		$shortcodes = array_keys( $shortcode_tags );

		foreach ( $shortcodes as $shortcode ) {
			$this->violation_location_collector->add( new MCD_Violation_Location_Content_Shortcode( $shortcode ) );
		}

		// Setup the autoembed location
		include $this->root_dir . '/violation-locations/content-autoembed.php';
		$this->violation_location_collector->add( new MCD_Violation_Location_Content_Autoembed() );

		// Add the filtered content after the shortcodes in order for more specificity
		include $this->root_dir . '/violation-locations/content-filtered.php';
		$this->violation_location_collector->add( new MCD_Violation_Location_Content_Filtered() );

		// Bring in the enqueue checks
		include $this->root_dir . '/violation-locations/enqueue-base.php';

		// Check for enqueued scripts
		include $this->root_dir . '/violation-locations/enqueue-script.php';
		$this->violation_location_collector->add( new MCD_Violation_Location_Enqueue_Script() );

		// Check for enqueued styles
		include $this->root_dir . '/violation-locations/enqueue-style.php';
		$this->violation_location_collector->add( new MCD_Violation_Location_Enqueue_Style() );
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

add_action( 'init', 'mcd_get_mixed_content_detector', 1 );