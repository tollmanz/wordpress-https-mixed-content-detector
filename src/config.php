<?php
/**
 * Define the path to the beacon.
 *
 * @since 1.0.0.
 */
define( 'MCD_REPORT_URI', site_url( '/?mcd=report' ) );

/**
 * Define the policies to monitor for.
 *
 * @since 1.0.0.
 */
if ( ! defined( 'MCD_POLICIES' ) ) {
	define( 'MCD_POLICIES', array(
		'default-src https:',
		'report-uri ' . $this->get_report_url()
	) );
}

/**
 * Determine whether or not to monitor admin mixed content warnings.
 *
 * @since 1.0.0.
 */
if ( ! defined( 'MCD_MONITOR_ADMIN' ) ) {
	define( 'MCD_MONITOR_ADMIN', false );
}

/**
 * Determine whether or not to monitor front end mixed content warnings.
 *
 * @since 1.0.0.
 */
if ( ! defined( 'MCD_MONITOR_FRONT_END' ) ) {
	define( 'MCD_MONITOR_FRONT_END', true );
}