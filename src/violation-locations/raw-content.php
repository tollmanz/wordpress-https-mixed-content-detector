<?php

class MCD_Violation_Location_Raw_Content extends MCD_Violation_Location_Base {
	public function get_location_id() {
		return 'mcd-raw-content';
	}

	public function get_location_name() {
		return __( 'Raw Content', 'zdt-mcd' );
	}

	public function search( $blocked_uri, $type ) {

	}
}