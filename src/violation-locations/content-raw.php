<?php

class MCD_Violation_Location_Content_Raw extends MCD_Violation_Location_Content_Base {
	/**
	 * The ID of the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The ID for the violation location.
	 */
	public function get_location_id() {
		return 'content-raw';
	}

	/**
	 * The name of the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The name for the violation location.
	 */
	public function get_location_name() {
		return __( 'Content Raw', 'zdt-mcd' );
	}

	/**
	 * The hint for the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The hint for the violation location.
	 */
	public function get_location_hint() {
		return __( 'The violation report originated from raw post content. This content was entered directly in the post\'s content field. It can be corrected by editing the post.', 'zdt-mcd' );
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
		return $this->_get_raw_post_content( $id );
	}
}