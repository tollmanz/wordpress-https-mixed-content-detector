<?php
/**
 * Define the path to the beacon.
 *
 * By default, this will report uri to /?mcd=report. You can change the default with a constant to send the report to a
 * custom URL.
 *
 * @since 1.0.0.
 */
if ( ! defined( 'MCD_REPORT_URI' ) ) {
	define( 'MCD_REPORT_URI', site_url( '/?mcd=report' ) );
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