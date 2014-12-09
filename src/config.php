<?php
/**
 * Define the path to the beacon.
 *
 * @since 1.0.0.
 */
define( 'MCD_REPORT_URI', site_url( '/?mcd=report&nonce=' . wp_create_nonce( 'mcd-report-uri' ) ) );

/**
 * Define the policies to monitor for.
 *
 * @since 1.0.0.
 */
if ( ! defined( 'MCD_POLICY' ) ) {
	define( 'MCD_POLICY', 'default-src https:; report-uri ' . MCD_REPORT_URI );
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