<?php

interface MCD_Violation_Location {
	public function get_location_name();
	public function get_location_id();
	public function search( $blocked_uri, $type );
}