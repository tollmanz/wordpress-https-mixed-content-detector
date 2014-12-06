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