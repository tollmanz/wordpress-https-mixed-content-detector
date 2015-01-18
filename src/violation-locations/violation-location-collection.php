<?php

class MCD_Violation_Location_Collection {
	/**
	 * Holds the collection of objects.
	 *
	 * @since 1.2.0.
	 *
	 * @var array    The collection of objects.
	 */
	private $_items = array();

	/**
	 * Add a new Violation Location to the collector.
	 *
	 * @since  1.2.0.
	 *
	 * @param  MCD_Violation_Location    $item    An Violation Location object to store.
	 * @return void
	 */
	public function add( $item ) {
		$this->_items[ $item->get_location_id() ] = $item;
	}

	/**
	 * Retrieve a Violation Location object from the collector
	 *
	 * @since  1.2.0.
	 *
	 * @param  string                         $id    The ID for a stored object.
	 * @return MCD_Violation_Location|bool           An Violation Location object to store or `false` if not found.
	 */
	public function get_item( $id ) {
		if ( isset( $this->_items[ $id ] ) ) {
			return $this->_items[ $id ];
		} else {
			return false;
		}
	}

	/**
	 * Return all of the Violation Location objects.
	 *
	 * @since  1.2.0.
	 *
	 * @return array    The list of Violation Location objects.
	 */
	public function get_all() {
		return $this->_items;
	}
}

