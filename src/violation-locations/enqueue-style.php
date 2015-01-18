<?php

class MCD_Violation_Location_Enqueue_Style extends MCD_Violation_Location_Enqueue_Base {
	/**
	 * Indicate that this is intended to control the script enqueues.
	 *
	 * @since 1.2.0.
	 *
	 * @var   string    Note that this is the script enqueues.
	 */
	protected $_type = 'style';

	/**
	 * The ID of the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The ID for the violation location.
	 */
	public function get_location_id() {
		return 'enqueue-style';
	}

	/**
	 * The name of the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The name for the violation location.
	 */
	public function get_location_name() {
		return __( 'Enqueue Style', 'zdt-mcd' );
	}

	/**
	 * The hint for the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The hint for the violation location.
	 */
	public function get_location_hint() {
		return __( 'The violation report originated from a CSS source. This content may be added via a plugin or theme. To fix this issue, enqueue the stylesheet using a secure link to the stylesheet.', 'zdt-mcd' );
	}
}