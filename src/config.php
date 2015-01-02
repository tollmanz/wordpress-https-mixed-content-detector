<?php
/**
 * Define the path to the beacon.
 *
 * @since 1.0.0.
 */
define( 'MCD_REPORT_URI', site_url( '/?mcd=report&nonce=' . wp_create_nonce( 'mcd-report-uri' ) ) );

/**
 * Define the Content Security Policy.
 *
 * This is set up as a global value to enable overriding in a config file. By setting up the `$mcd_policies` global
 * variable, a developer can override this value from wp-config.php.
 *
 * @since 1.1.0.
 */
global $mcd_policies;

// Only use the default if another policy is not set
if ( ! isset( $mcd_policies ) ) {
	$mcd_policies = array(
		'default-src' => "https: data: 'unsafe-inline' 'unsafe-eval'",
		'child-src'   => "https:",
		'connect-src' => "https:",
		'font-src'    => "https: data:",
		'img-src'     => "https: data:",
		'media-src'   => "https:",
		'object-src'  => "https:",
		'script-src'  => "https: 'unsafe-inline' 'unsafe-eval'",
		'style-src'   => "https: 'unsafe-inline'",
		'report-uri'  => MCD_REPORT_URI
	);
}

/**
 * Define the policies to monitor for.
 *
 * @since 1.0.0.
 */
if ( ! defined( 'MCD_POLICY' ) ) {
	define( 'MCD_POLICY', "default-src 'unsafe-inline' 'unsafe-eval' data: https:; report-uri " . MCD_REPORT_URI );
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