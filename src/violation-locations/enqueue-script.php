<?php

class MCD_Violation_Location_Enqueue_Script extends MCD_Violation_Location_Enqueue_Base {
	/**
	 * Indicate that this is intended to control the script enqueues.
	 *
	 * @since 1.2.0.
	 *
	 * @var   string    Note that this is the script enqueues.
	 */
	protected $_type = 'script';

	/**
	 * The ID of the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The ID for the violation location.
	 */
	public function get_location_id() {
		return 'enqueue-script';
	}

	/**
	 * The name of the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The name for the violation location.
	 */
	public function get_location_name() {
		return __( 'Enqueue Script', 'zdt-mcd' );
	}

	/**
	 * The hint for the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The hint for the violation location.
	 */
	public function get_location_hint() {
		return __( 'The violation report originated from a script source. This content may be added via a plugin or theme. To fix this issue, enqueue the script using a secure link to the script.', 'zdt-mcd' );
	}
}