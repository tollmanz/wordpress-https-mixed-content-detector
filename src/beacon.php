<?php

if ( ! class_exists( 'MCD_Beacon' ) ) :
/**
 * Bootstrap the beacon functionality.
 *
 * This class will set up the beacon URL for the plugin. This receives the JSON reports sent to the report-uri defined
 * in the header.
 *
 * @since 1.0.0.
 */
class MCD_Beacon {
	/**
	 * The one instance of MCD_Beacon.
	 *
	 * @since 1.0.0.
	 *
	 * @var   MCD_Beacon
	 */
	private static $instance;

	/**
	 * Instantiate or return the one MCD_Beacon instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return MCD_Beacon
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
	 * @return MCD_Beacon
	 */
	public function __construct() {}
}
endif;

/**
 * Get the one instance of the MCD_Beacon.
 *
 * @since  1.0.0.
 *
 * @return MCD_Beacon
 */
function mcd_get_beacon() {
	return MCD_Beacon::instance();
}

mcd_get_beacon();