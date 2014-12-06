<?php

if ( ! class_exists( 'MCD_Live_Mode' ) ) :
/**
 * Bootstrap functionality for the plugin.
 *
 * @since 1.0.0.
 */
class MCD_Live_Mode {
	/**
	 * The one instance of MCD_Live_Mode.
	 *
	 * @since 1.0.0.
	 *
	 * @var   MCD_Live_Mode
	 */
	private static $instance;

	/**
	 * Instantiate or return the one MCD_Live_Mode instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return MCD_Live_Mode
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
	 * @return MCD_Live_Mode
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_cps_header' ) );
	}

	/**
	 * Adds the Content-Security-Policy-Report-Only header.
	 *
	 * Add this header allows us to indicate that we want only https resources loaded on the page; however, since this
	 * is the report only header, nothing is blocked. Instead, violations are reported to the beacon URL.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function add_cps_header() {
		header( $this->get_cps_header() );
	}

	/**
	 * Get the CPS header content.
	 *
	 * Returns the content used for the CPS header.
	 *
	 * @since  1.0.0.
	 *
	 * @return string    The CPS header string.
	 */
	public function get_cps_header() {
		return 'Content-Security-Policy-Report-Only: default-src https:; report-uri ' . MCD_REPORT_URI;
	}
}
endif;

/**
 * Get the one instance of the MCD_Live_Mode.
 *
 * @since  1.0.0.
 *
 * @return MCD_Live_Mode
 */
function mcd_get_live_mode() {
	return MCD_Live_Mode::instance();
}

mcd_get_live_mode();