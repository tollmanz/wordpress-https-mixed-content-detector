<?php

if ( ! function_exists( 'mcd_get_violation_wp_query' ) ) :
/**
 * Get the violations that are currently logged in the form of a WP_Query.
 *
 * @since  1.1.0.
 *
 * @param  int         $num    The number of violations to query.
 * @param  int         $id     Specific report to lookup.
 * @return WP_Query            The WP_Query containing the violations
 */
function mcd_get_violation_wp_query( $num = 999, $id = 0 ) {
	// Determine number of violations to display
	$num = ( ! empty( $num ) ) ? intval( $num ) : 999; // Use intval to allow -1 if desired

	$args = array(
		'post_type'      => 'csp-report',
		'posts_per_page' => $num,
		'no_found_rows'  => true,
	);

	// If a specific ID is queried, add it to the query
	if ( 0 !== $id ) {
		$args['p']              = absint( $id );
		$args['posts_per_page'] = 1;
	}

	return new WP_Query( $args );
}
endif;

if ( ! function_exists( 'mcd_get_violation_data' ) ) :
/**
 * Return CSP Report data in an easy to use manner.
 *
 * Note that none of the returned data is escaped. Since the data will need to be escaped depending on the
 * particular situation in which is it used, it is the responsibility of the caller to handle escaping.
 *
 * Returned data is in the form of:
 *
 *   array(
 *     'blocked-uri'        => '',
 *     'document-uri'       => '',
 *     'referrer'           => '',
 *     'violated-directive' => '',
 *     'original-policy'    => '',
 *     'resolved'           => '',
 *   )
 *
 * @since  1.1.0.
 *
 * @param  int      $num    The number of violations to get.
 * @param  int      $id     Specific report to lookup.
 * @return array            The data for the violations.
 */
function mcd_get_violation_data( $num = 999, $id = 0 ) {
	// Set a data collector
	$data = array();

	// Query for the violations
	$violation_wp_query = mcd_get_violation_wp_query( $num, $id );

	// Package up the important data
	if ( $violation_wp_query->have_posts() ) {
		while ( $violation_wp_query->have_posts() ) {
			$violation_wp_query->the_post();

			$referrer = get_post_meta( get_the_ID(), 'document-uri', true );
			$referrer = ( ! empty( $referrer ) ) ? $referrer : __( 'N/A', 'zdt-mcd' );

			$v_directive = get_post_meta( get_the_ID(), 'violated-directive', true );
			$v_directive = ( ! empty( $v_directive ) ) ? $v_directive : __( 'N/A', 'zdt-mcd' );

			$original_policy = get_post_meta( get_the_ID(), 'original-policy', true );
			$original_policy = ( ! empty( $original_policy ) ) ? $original_policy : __( 'N/A', 'zdt-mcd' );

			$location = get_post_meta( get_the_ID(), 'location', true );
			$location = ( ! empty( $location ) ) ? $location : __( 'N/A', 'zdt-mcd' );

			$valid_https_uri = get_post_meta( get_the_ID(), 'valid-https-uri', true );
			$valid_https_uri = ( '0' === $valid_https_uri || '1' === $valid_https_uri ) ? intval( $valid_https_uri ) :  -1;

			$data[ get_the_ID() ] = array(
				'id'                 => get_the_ID(),
				'blocked-uri'        => get_post_meta( get_the_ID(), 'blocked-uri', true ),
				'document-uri'       => get_post_meta( get_the_ID(), 'document-uri', true ),
				'referrer'           => $referrer,
				'violated-directive' => $v_directive,
				'original-policy'    => $original_policy,
				'location'           => $location,
				'resolved'           => absint( get_post_meta( get_the_ID(), 'resolved', true ) ),
				'valid-https-uri'    => $valid_https_uri,
			);
		}
	}

	wp_reset_postdata();

	return $data;
}
endif;

if ( ! function_exists( 'mcd_mark_all_violations_resolved' ) ) :
/**
 * Mark all CSP Reports as resolved.
 *
 * @since  1.1.0.
 *
 * @return int    The number of posts resolved.
 */
function mcd_mark_all_violations_resolved() {
	$violation_data = mcd_get_violation_data( -1 );
	$resolutions    = 0;

	foreach ( $violation_data as $post_id => $data ) {
		if ( false !== mcd_mark_violation_resolved( $post_id ) ) {
			$resolutions++;
		}
	}

	return $resolutions;
}
endif;

if ( ! function_exists( 'mcd_mark_violation_resolved' ) ) :
/**
 * Mark a single CSP Report as resolved.
 *
 * @since  1.1.0.
 *
 * @param  int                   $id    The ID of the report to resolve.
 * @return array|bool|WP_Post           The result of the resolution.
 */
function mcd_mark_violation_resolved( $id ) {
	return update_post_meta( $id, 'resolved', 1 );
}
endif;

if ( ! function_exists( 'mcd_mark_all_violations_unresolved' ) ) :
/**
 * Mark all CSP Reports as unresolved.
 *
 * @since  1.1.0.
 *
 * @return int    The number of posts unresolved.
 */
function mcd_mark_all_violations_unresolved() {
	$violation_data        = mcd_get_violation_data( - 1 );
	$violations_unresolved = 0;

	foreach ( $violation_data as $post_id => $data ) {
		if ( false !== mcd_mark_violation_unresolved( $post_id ) ) {
			$violations_unresolved++;
		}
	}

	return $violations_unresolved;
}
endif;

if ( ! function_exists( 'mcd_mark_violation_unresolved' ) ) :
/**
 * Mark a single CSP Report as unresolved.
 *
 * @since  1.1.0.
 *
 * @param  int                   $id    The ID of the report to unresolve.
 * @return array|bool|WP_Post           The result of the action.
 */
function mcd_mark_violation_unresolved( $id ) {
	return delete_post_meta( $id, 'resolved' );
}
endif;

if ( ! function_exists( 'mcd_remove_all_violations' ) ) :
/**
 * Remove all CSP violation reports.
 *
 * @since  1.1.0.
 *
 * @return int    The number of reports removed.
 */
function mcd_remove_all_violations() {
	$violation_data = mcd_get_violation_data( -1 );
	$removals       = 0;

	foreach ( $violation_data as $post_id => $data ) {
		if ( false !== mcd_remove_violation( $post_id ) ) {
			$removals++;
		}
	}

	return $removals;
}
endif;

if ( ! function_exists( 'mcd_remove_violation' ) ) :
/**
 * Remove a single CSP report.
 *
 * @since  1.1.0.
 *
 * @param  int                   $id    The ID of the report to remove.
 * @return array|bool|WP_Post           The result of the removal.
 */
function mcd_remove_violation( $id ) {
	return wp_delete_post( $id, true );
}
endif;

if ( ! function_exists( 'mcd_is_valid_uri') ) :
/**
 * Determine if a URI can be reached.
 *
 * @since  1.1.0.
 *
 * @param  string    $uri    The URI to test.
 * @return bool              True if URI is connectable; false if it is not.
 */
function mcd_is_valid_uri( $uri ) {
	$response = wp_remote_get( $uri, array(
		/**
		 * Do a HEAD request for efficiency.
		 */
		'method'      => 'HEAD',

		/**
		 * HEAD requests will not redirect by default. It is important that redirection works in case the
		 * recorded URI is not the final URI. For instance, if the recorded URI is "google.com" when the actual
		 * URI is "www.google.com", we need to make sure the resolution works.
		 */
		'redirection' => 1,
	) );

	/**
	 * If the response is a WP_Error, a TCP connection cannot be made to the URI, suggesting that it is not valid. We
	 * base the validity of the URL on this result.
	 */
	return ( false === is_wp_error( $response ) );
}
endif;

if ( ! function_exists( 'mcd_uri_has_secure_version' ) ) :
/**
 * Determine if the secure version of the URI is reachable.
 *
 * @since  1.1.0.
 *
 * @param  string    $uri    The URI to test.
 * @return bool              True if URI is connectable; false if it is not.
 */
function mcd_uri_has_secure_version( $uri ) {
	$https_uri = set_url_scheme( $uri, 'https' );
	return mcd_is_valid_uri( $https_uri );
}
endif;

if ( ! function_exists( 'mcd_locate_all_violations' ) ) :
/**
 * Locate the source of all CSP Reports.
 *
 * @since  1.2.0.
 *
 * @return int    The number of reports located.
 */
function mcd_locate_all_violations() {
	$violation_data = mcd_get_violation_data( -1 );
	return mcd_handle_violation_locations( $violation_data );

}
endif;

if ( ! function_exists( 'mcd_locate_violation' ) ) :
/**
 * Locate the source of an individual CSP Report.
 *
 * @since  1.2.0.
 *
 * @param  int                   $id    The ID of the report to investigate.
 * @return array|bool|WP_Post           The result of the investigation.
 */
function mcd_locate_violation( $id ) {
	$violation_data = mcd_get_violation_data( 1, $id );
	return mcd_handle_violation_locations( $violation_data );
}
endif;

if ( ! function_exists( 'mcd_handle_violation_locations' ) ) :
/**
 * Handle the recording of 1 or more violation locations.
 *
 * @since  1.2.0.
 *
 * @param  WP_Query              $violation_data    A list of violation locations.
 * @return array|bool|WP_Post                       The result of the investigation.
 */
function mcd_handle_violation_locations( $violation_data ) {
	$violation_locations = mcd_get_mixed_content_detector()->violation_location_collector->get_all();
	$locations           = 0;

	foreach ( $violation_data as $post_id => $data ) {
		$found = false;

		foreach ( $violation_locations as $violation_location ) {
			if ( true === $violation_location->match( $data ) ) {
				$location = $violation_location->get_location_id();
				$locations++;
				$found = true;
				break;
			}
		}

		if ( false === $found ) {
			$location = __( 'unknown', 'zdt-mcd' );
		}

		update_post_meta( $post_id, 'location', $location );
	}

	return $locations;
}
endif;