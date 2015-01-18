<?php

class MCD_Violation_Location_Content_Shortcode extends MCD_Violation_Location_Content_Base {
	/**
	 * The shortcode being investigated.
	 *
	 * @since  1.2.0.
	 *
	 * @var    string    The shortcode being investigated.
	 */
	private $_shortcode = '';

	/**
	 * Setup the object for a specific shortcode.
	 *
	 * @since  1.2.0.
	 *
	 * @param  string                                      $shortcode    The shortcode to investigate.
	 * @return MCD_Violation_Location_Shortcode_Content
	 */
	public function __construct( $shortcode ) {
		$this->_shortcode = $shortcode;
	}

	/**
	 * The ID of the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The ID for the violation location.
	 */
	public function get_location_id() {
		return 'content-shortcode' . sanitize_title_with_dashes( $this->_shortcode );
	}

	/**
	 * The name of the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The name for the violation location.
	 */
	public function get_location_name() {
		return __( 'Content Shortcode', 'zdt-mcd' );
	}

	/**
	 * The hint for the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The hint for the violation location.
	 */
	public function get_location_hint() {
		return sprintf(
			__(
				'The violation report originated from the "%1$s" shortcode altering the post content. The raw content does not contain the violation, but gains the violation once the "%1$s" shortcode is rendered against the content. It must be corrected by changing the "%1$s" shortcode callback.',
				'zdt-mcd'
			),
			esc_attr( wp_strip_all_tags( $this->_shortcode ) )
		);
	}
	/**
	 * Get a posts's unfiltered content.
	 *
	 * @since  1.2.0.
	 *
	 * @param  int       $id    The post ID.
	 * @return string           The post content.
	 */
	protected function _get_post_content( $id ) {
		$shortcoded_content = '';
		$content            = $this->_get_raw_post_content( $id );

		// Save the shortcode that we are investigating
		global $shortcode_tags;
		$shortcode_tags_original = $shortcode_tags;
		$shortcode_callable = ( isset( $shortcode_tags[ $this->_shortcode ] ) ) ? $shortcode_tags[ $this->_shortcode ] : '';

		if ( ! empty( $shortcode_callable ) ) {
			// Remove all shortcodes, then add just this one back
			remove_all_shortcodes();

			/**
			 * The embed shortcode has a nasty workaround. The initial shortcode is registered with the "__return_false"
			 * callback. There is some trick teasing of the shortcode to fool wpautop. Ultimately, the real callback
			 * is "shortcode" on the $wp_embed object. We need to apply this same workaround so that the shortcode is
			 * rendered using the right callback.
			 */
			if ( 'embed' === $this->_shortcode ) {
				global $wp_embed;
				$shortcode_callable = array( $wp_embed, 'shortcode' );
			}

			add_shortcode( $this->_shortcode, $shortcode_callable );

			// Now that we only have the single shortcode, render the content with it
			$shortcoded_content = do_shortcode( $content );

			// Restore the shortcodes to their original state
			$shortcode_tags = $shortcode_tags_original;
		}

		return $shortcoded_content;
	}

	/**
	 * Determine if content is searchable for a particular violation.
	 *
	 * @since  1.2.0.
	 *
	 * @param  array    $violation    Collection of violation data.
	 * @return bool                   True if content is searchable; false if it is not.
	 */
	protected function _is_content_searchable( $violation ) {
		$post_id = $this->_get_post_id( $this->_get_violation_part( $violation, 'document-uri' ) );
		$content = $this->_get_raw_post_content( $post_id );

		/**
		 * The content is only searchable if the post content has the shortcode and the conditions of the base class'
		 * "_is_content_searchable" method is true.
		 */
		return ( true === has_shortcode( $content, $this->_shortcode ) && parent::_is_content_searchable( $violation ) );
	}
}