<?php

abstract class MCD_Violation_Location_Enqueue_Base extends MCD_Violation_Location_Base {
	/**
	 * Indicate the type of enqueue.
	 *
	 * @since 1.2.0.
	 *
	 * @var   string    The type of enqueue being looked up.
	 */
	protected $_type = '';

	/**
	 * Determine if the blocked URI is present in the content.
	 *
	 * @since  1.2.0.
	 *
	 * @param  array    $violation    The violation data.
	 * @return bool                   True if the URI was found; false if it was not.
	 */
	public function match( $violation ) {
		$enqueues = $this->_get_enqueues( $this->_type );

		// Get the blocked URI
		$blocked_uri  = $this->_get_violation_part( $violation, 'blocked-uri' );
		$uri_variants = $this->_get_uri_variants( $blocked_uri );

		foreach ( $enqueues->registered as $handle => $data ) {
			if ( ! empty( $data->src ) ) {
				$source = $data->src;

				// Search for each variant. If any one variant matches, return true.
				foreach ( $uri_variants as $variant ) {
					if ( ! empty( $variant ) ) {
						/**
						 * The most accurate test seems to be looking for the variant in the source, as well as the
						 * source in the variant. This is particularly helpful for handling the version strings that are
						 * appended to some enqueues.
						 */
						if ( false !== strpos( $source, $variant ) || false !== strpos( $variant, $source ) ) {
							return true;
						}
					}
				}
			}
		}

		return false;
	}

	/**
	 * Get the list of script or style enqueues.
	 *
	 * @since  1.2.0.
	 *
	 * @param  string                        $type    The type of object to get.
	 * @return array|WP_Scripts|WP_Styles             The list of enqueues.
	 */
	protected function _get_enqueues( $type ) {
		$enqueues = array();

		/**
		 * We need to load up the enqueues to know what is actually enqueued. To simulate this, we need to run the
		 * "wp_head" and "wp_footer" actions. WP best practices dictate that scripts/styles are added on the
		 * "wp_enqueue_scripts" action, which occurs on the "wp_head" action. Hence, we need to fake this to load up
		 * the arrays.
		 */
		ob_start();
		do_action( 'wp_head' );
		do_action( 'wp_footer' );
		ob_end_clean();

		if ( 'script' === $type ) {
			global $wp_scripts;
			$enqueues = $wp_scripts;
		} else if ( 'style' === $type ) {
			global $wp_styles;
			$enqueues = $wp_styles;
		}

		return $enqueues;
	}
}