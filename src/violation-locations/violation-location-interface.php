<?php

interface MCD_Violation_Location {
	/**
	 * Get the name of the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The name of the violation location.
	 */
	public function get_location_name();

	/**
	 * Get the ID of the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The ID of the violation location.
	 */
	public function get_location_id();

	/**
	 * Get the hint for the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @return string    The hint for the violation location.
	 */
	public function get_location_hint();

	/**
	 * Get the name of the violation location.
	 *
	 * @since  1.2.0.
	 *
	 * @param  array    $violation    The violation data.
	 * @return bool                   True if the found; false if not.
	 */
	public function match( $violation );
}