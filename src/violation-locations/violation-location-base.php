<?php

abstract class MCD_Violation_Location_Base implements MCD_Violation_Location {
	/**
	 * Get an individual piece of violation information from the list of violation parts.
	 *
	 * @since  1.2.0.
	 *
	 * @param  array     $violation    The list of violation parts.
	 * @param  string    $part         The part to get.
	 * @return string                  The violation part. Empty string if it doesn't exist.
	 */
	protected function _get_violation_part( $violation, $part ) {
		return ( isset( $violation[ $part ] ) ) ? $violation[ $part ] : '';
	}

	/**
	 * Get the different possible versions of the URI to search for it in content.
	 *
	 * @since  1.2.0.
	 *
	 * @param  string    $uri    The URI to generate variants for.
	 * @return array             A list of variants to search for.
	 */
	protected function _get_uri_variants( $uri ) {
		$variants = array(
			'http'              => set_url_scheme( $uri, 'http' ),
			'https'             => set_url_scheme( $uri, 'https' ),
			'protocol-relative' => str_replace( 'http:', '', set_url_scheme( $uri, 'http' ) ),
		);

		// Get just the path without the protocol or host name
		$parts = parse_url( $uri );

		$pre_path  = ( isset( $parts['scheme'] ) ) ? $parts['scheme'] . '://' : '';
		$pre_path .= ( isset( $parts['host'] ) ) ? $parts['host'] : '';

		// Add path to the variants
		$variants['path'] = str_replace( $pre_path, '', $uri );

		return $variants;
	}
}