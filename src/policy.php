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
		/**
		 * The CSP header is added should be added if we are in sample mode or if not in sample mode *and* logged in.
		 * Sample mode will set set a header for every request; however, the beacon will only accept
		 * MCD_SAMPLE_FREQUENCY percent of requests.
		 */
		if ( false === MCD_SAMPLE_MODE ) {
			if ( ! is_user_logged_in() ) {
				return;
			}
		}

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
	 * @since  1.1.0     Added support for the $mcd_policies array, while still respecting MCD_POLICY.
	 *
	 * @return string    The full policy
	 */
	public function get_full_policy() {
		global $mcd_policies;

		// Respect the global array of policies in version 1.1.x+
		if ( isset( $mcd_policies ) ) {
			$policy = $this->build_policy( $mcd_policies );

			// Respect the MCD_POLICY constant used in version 1.0.x
		} elseif ( defined( 'MCD_POLICY' ) ) {
			$policy = MCD_POLICY;

		// Default to the core list of policies
		} else {
			$policy = $this->build_policy( $this->get_default_policies() );
		}

		return $policy;
	}

	/**
	 * Get a list of policies set for the page load.
	 *
	 * Note that this function is intended to be used for breaking a CSP string into an array. It is primarily used for
	 * whitelisting policies sent to the beacon.
	 *
	 * @since  1.0.0.
	 * @since  1.1.0    Generate the list from the new default policies.
	 *
	 * @return array    A list of full policies.
	 */
	public function get_policies() {
		return explode( '; ', $this->get_full_policy() );
	}

	/**
	 * Return an array of policies for CSP.
	 *
	 * @since  1.0.0.
	 * @since  1.1.0    Support a passed array of policies, not just the plugin default policy.
	 *
	 * @return array    Array of CSP policies.
	 */
	public function build_policy( $policies ) {
		return implode( '; ', $this->build_full_individual_policies( $policies ) ) . '; report-uri ' . $this->get_report_url();
	}

	/**
	 * Get the full report URI.
	 *
	 * @since  1.0.0.
	 *
	 * @return string    The full URL for the report.
	 */
	public function get_report_url() {
		return MCD_REPORT_URI;
	}

	/**
	 * Based on the array of individual policies, create an array of full individual policies.
	 *
	 * Given the following array:
	 *
	 *   array(
	 *     'font-src'   => "https: data:",
	 *     'object-src' => "https:",
	 *   )
	 *
	 * The following array is returned:
	 *
	 *   array(
	 *     'font-src https: data:',
	 *     'object-src https:',
	 *   )
	 *
	 * @since  1.1.0.
	 *
	 * @param  array    $array    The list of policies with directive as key and policy as value.
	 * @return array              The list of policies with the full policy as value.
	 */
	public function build_full_individual_policies( $array ) {
		$full_individual_policies = array();

		foreach ( $array as $directive => $policy ) {
			$full_individual_policies[] = $directive . ' ' . $policy;
		}

		return $full_individual_policies;
	}

	/**
	 * Get the list of default policies.
	 *
	 * @since  1.1.0.
	 *
	 * @return array    List of individual policies.
	 */
	public function get_default_policies() {
		return array(
			'default-src' => "https: data: 'unsafe-inline' 'unsafe-eval'",
			'child-src'   => "https:",
			'connect-src' => "https:",
			'font-src'    => "https: data:",
			'img-src'     => "https: data:",
			'media-src'   => "https:",
			'object-src'  => "https:",
			'script-src'  => "https: 'unsafe-inline' 'unsafe-eval'",
			'style-src'   => "https: 'unsafe-inline'",
		);
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