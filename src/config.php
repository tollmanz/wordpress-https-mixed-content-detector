<?php
/**
 * Define the path to the beacon.
 *
 * @since 1.0.0.
 */
define( 'MCD_REPORT_URI', site_url( '/?mcd=report&nonce=' . wp_create_nonce( 'mcd-report-uri' ) ) );

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

/**
 * Sample mode will accept reports from non-logged in users.
 *
 * @since 1.2.0.
 */
if ( ! defined( 'MCD_SAMPLE_MODE' ) ) {
	define( 'MCD_SAMPLE_MODE', false );
}

/**
 * The percentage of traffic to sample.
 *
 * The value set here will be divided by 100 to get the amount of traffic to sample. Setting this to 1 will sample ~1 in
 * every 100 requests.
 *
 * @since 1.2.0.
 */
if ( ! defined( 'MCD_SAMPLE_FREQUENCY' ) ) {
	define( 'MCD_SAMPLE_FREQUENCY', 10 );
}