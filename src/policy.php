<?php

if ( ! class_exists( 'MCD_Policy' ) ) :
/**
 * Bootstrap functionality for the plugin.
 *
 * @since 1.0.0.
 */
class MCD_Policy {
	/**
	 * The one instance of MCD_Policy.
	 *
	 * @since 1.0.0.
	 *
	 * @var   MCD_Policy
	 */
	private static $instance;

	/**
	 * Instantiate or return the one MCD_Policy instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return MCD_Policy
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
	 * @return MCD_Policy
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
		$monitor_admin     = defined( 'MCD_MONITOR_ADMIN' ) && true === MCD_MONITOR_ADMIN && is_admin();
		$monitor_front_end = defined( 'MCD_MONITOR_FRONT_END' ) && true === MCD_MONITOR_FRONT_END && ! is_admin();

		if ( $monitor_admin || $monitor_front_end ) {
			header( $this->get_cps_header() );
		}
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
		return 'Content-Security-Policy-Report-Only: ' . $this->get_full_policy();
	}

	/**
	 * Create the policy from the individual policies.
	 *
	 * @since  1.0.0.
	 *
	 * @return string    The full policy
	 */
	public function get_full_policy() {
		return implode( '; ', $this->get_policies() );
	}

	/**
	 * Return an array of policies for CSP.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    Array of CSP policies.
	 */
	public function get_policies() {
		return MCD_POLICIES;
	}

	/**
	 * Get the full report URI.
	 *
	 * @since  1.0.0.
	 *
	 * @return string    The full URL for the report.
	 */
	public function get_report_url() {
		return MCD_REPORT_URI . '&nonce=' . wp_create_nonce( 'mcd-report-uri' );
	}
}
endif;

/**
 * Get the one instance of the MCD_Policy.
 *
 * @since  1.0.0.
 *
 * @return MCD_Policy
 */
function mcd_get_policy() {
	return MCD_Policy::instance();
}

mcd_get_policy();