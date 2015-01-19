<?php

if ( ! class_exists( 'MCD_Beacon' ) ) :
/**
 * Bootstrap the beacon functionality.
 *
 * This class will set up the beacon URL for the plugin. This receives the JSON reports sent to the report-uri defined
 * in the header.
 *
 * @since 1.0.0.
 */
class MCD_Beacon {
	/**
	 * The one instance of MCD_Beacon.
	 *
	 * @since 1.0.0.
	 *
	 * @var   MCD_Beacon
	 */
	private static $instance;

	/**
	 * Instantiate or return the one MCD_Beacon instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return MCD_Beacon
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Create a new section.
	 *
	 * @since  1.0.0.
	 *
	 * @return MCD_Beacon
	 */
	public function __construct() {
		// Register and customize the CPT to hold the logs
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'manage_edit-csp-report_columns', array( $this, 'manage_edit_csp_report_columns' ) );
		add_action( 'manage_csp-report_posts_custom_column' , array( $this, 'manage_csp_report_posts_custom_column' ), 10, 2 );

		add_action( 'init', array( $this, 'handle_report_uri' ) );
	}

	/**
	 * Register the post type to hold the CSP reports.
	 *
	 * The CSP report only header will be passing information about assets to the beacon URL. The data sent to the
	 * beacon will be held in a CPT. This function sets up that CPT.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function register_post_type() {
		$args = array(
			'description'         => __( 'Holds Content Security Policy violation logs.', 'zdt-mcd' ),
			'public'              => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => false,
			'menu_position'       => 25,
			'menu_icon'           => 'dashicons-flag',
			'can_export'          => false,
			'delete_with_user'    => false,
			'hierarchical'        => false,
			'has_archive'         => false,
			'query_var'           => true,
			'rewrite'             => false,
			'supports'            => array(
				'title',
			),
			'labels'              => array(
				'name'               => __( 'Content Security Policy Report', 'zdt-mcd' ),
				'singular_name'      => __( 'Content Security Policy Report', 'zdt-mcd' ),
				'menu_name'          => __( 'Content Security Policy Reports', 'zdt-mcd' ),
				'name_admin_bar'     => __( 'Content Security Policy Reports', 'zdt-mcd' ),
				'add_new'            => __( 'Add New', 'zdt-mcd' ),
				'add_new_item'       => __( 'Add New Content Security Policy Report', 'zdt-mcd' ),
				'edit_item'          => __( 'Edit Content Security Policy Report', 'zdt-mcd' ),
				'new_item'           => __( 'New Content Security Policy Report', 'zdt-mcd' ),
				'view_item'          => __( 'View Content Security Policy Report', 'zdt-mcd' ),
				'search_items'       => __( 'Search Content Security Policy Reports', 'zdt-mcd' ),
				'not_found'          => __( 'No Content Security Policy Reports found', 'zdt-mcd' ),
				'not_found_in_trash' => __( 'No Content Security Policy Reports found in trash', 'zdt-mcd' ),
				'all_items'          => __( 'All Content Security Policy Reports', 'zdt-mcd' ),
				'parent_item'        => __( 'Parent Content Security Policy Report', 'zdt-mcd' ),
				'parent_item_colon'  => __( 'Parent Content Security Policy Report:', 'zdt-mcd' ),
				'archive_title'      => __( 'Content Security Policy Reports', 'zdt-mcd' ),
			)
		);

		register_post_type( 'csp-report', $args );
	}

	/**
	 * Register new columns for the CSP Report list table.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $columns    The current list of columns.
	 * @return array                Modified list of columns.
	 */
	public function manage_edit_csp_report_columns( $columns ) {
		unset( $columns['title'] );
		unset( $columns['date'] );

		$columns['blocked-uri']        = __( 'Blocked URI', 'zdt-mcd' );
		$columns['document-uri']       = __( 'Document URI', 'zdt-mcd' );
		$columns['referrer']           = __( 'Referrer', 'zdt-mcd' );
		$columns['violated-directive'] = __( 'Violated Directive', 'zdt-mcd' );
		$columns['report-date']        = __( 'Date', 'zdt-mcd' );
		$columns['location']           = __( 'Location', 'zdt-mcd' );
		$columns['resolve-status']     = __( 'Resolved', 'zdt-mcd' );
		$columns['secure-status']      = __( 'Secure URI', 'zdt-mcd' );

		return $columns;
	}

	/**
	 * Print content for the custom columns.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $column     The column identifier.
	 * @param  int       $post_id    The post ID for the current item.
	 * @return void
	 */
	public function manage_csp_report_posts_custom_column( $column, $post_id ) {
		switch ( $column ) {
			case 'blocked-uri' :
				echo esc_url( get_post_meta( $post_id , 'blocked-uri' , true ) );
				break;

			case 'document-uri' :
				echo esc_url( get_post_meta( $post_id , 'document-uri' , true ) );
				break;

			case 'referrer' :
				$referrer = get_post_meta( $post_id , 'document-uri' , true );
				echo ( ! empty( $referrer ) ) ? esc_url( $referrer ) : __( 'N/A', 'zdt-mcd' );
				break;

			case 'violated-directive' :
				$v_directive = get_post_meta( $post_id , 'violated-directive' , true );
				echo ( ! empty( $v_directive ) ) ? esc_html( wp_strip_all_tags( $v_directive ) ) : __( 'N/A', 'zdt-mcd' );
				break;

			case 'report-date' :
				echo human_time_diff( get_the_date( 'U', get_the_ID() ) );
				break;

			case 'location':
				$location_id = get_post_meta( get_the_ID(), 'location', true );
				$collector = mcd_get_mixed_content_detector()->violation_location_collector;
				$location = $collector->get_item( $location_id );

				if ( method_exists( $location, 'get_location_name' ) ) {
					echo esc_html( $location->get_location_name() );
				} else if ( 'unknown' === $location_id ) {
					echo __( 'Unknown', 'zdt-mcd' );
				} else {
					echo __( 'N/A', 'zdt-mcd' );
				}

				break;

			case 'resolve-status':
				echo ( 1 === (int) get_post_meta( get_the_ID(), 'resolved', true ) ) ? __( 'Yes', 'zdt-mcd' ) : __( 'No', 'zdt-mcd' );
				break;

			case 'secure-status':
				$status = (int) get_post_meta( get_the_ID(), 'valid-https-uri', true );

				if ( 1 === $status ) {
					$message = __( 'Yes', 'zdt-mcd' );
				} elseif ( 0 === $status ) {
					$message = __( 'No', 'zdt-mcd' );
				} else {
					$message = __( 'Unknown', 'zdt-mcd' );
				}

				echo $message;
				break;
		}
	}

	/**
	 * Handle routing of the beacon request.
	 *
	 * This function identifies the beacon request and sets into motion the actions to record the beacon data.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function handle_report_uri() {
		// Check to make sure the a beacon request has been made
		if ( ! isset( $_GET['mcd'] ) || 'report' !== $_GET['mcd'] ) {
			return;
		}

		// Authenticate the request for either sampling mode or auth mode
		if ( true === MCD_SAMPLE_MODE && ! is_user_logged_in() ) {
			/**
			 * To accept every MCD_SAMPLE_FREQUENCY percent of requests in sample mode, pull a random number between
			 * 1 and the percentage of requests we should accept. If that number is 1, accept the request. This is a
			 * simple method to only allow a certain number of requests.
			 */
			$max_range     = ceil( 100 / (float) MCD_SAMPLE_FREQUENCY );
			$random_number = rand( 1, $max_range );

			if ( 1 !== $random_number ) {
				return;
			}
		} else {
			// If you can turn on the plugin, the beacon should work for you
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}
		}

		// Verify the nonce is set
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'mcd-report-uri' ) ) {
			return;
		}

		// Grab the contents of the request
		$contents = json_decode( file_get_contents( 'php://input' ), true );

		// Make sure the expected data is sent with the request
		if ( ! isset( $contents['csp-report'] ) ) {
			return;
		}

		$clean_data = array();

		// Cycle through each field in the report to make sure it is whitelisted
		foreach ( $contents['csp-report'] as $field => $value ) {
			// Verify that the field is valid
			if ( in_array( $field, array_keys( $this->whitelisted_fields() ) ) ) {
				$fields = $this->whitelisted_fields();

				// Make sure that the sanitize callback is legit
				if ( is_callable( $fields[ $field ]['sanitize_callback'] ) ) {
					// Sanitize the value
					$clean_data[ $field ] = call_user_func_array(
						$fields[ $field ]['sanitize_callback'],
						array(
							$value
						)
					);
				}
			}
		}

		// Add a post for the report
		$post_id = (int) wp_insert_post( array(
			'post_type'   => 'csp-report',
			'post_status' => 'publish',
			'post_title'  => $this->sanitize_blocked_uri( $contents['csp-report']['blocked-uri'] ),
		) );

		// If the post was successfully inserted, add the metadata
		if ( $post_id > 0 ) {
			foreach ( $clean_data as $key => $value ) {
				update_post_meta( $post_id, $key, $value );
			}
		}

		// Determine the location of the violation
		mcd_locate_violation( $post_id );

		// Check if the domain supports HTTPS
		if ( isset( $clean_data['blocked-uri'] ) ) {
			$uri = $clean_data['blocked-uri'];

			/**
			 * When checking the blocked URI, we are only interested in full URI. Relative URIs will not be checked.
			 * These are marked as -1 to represent an "unknown" status.
			 */
			if ( false !== strpos( $uri, 'http', 0 ) ) {
				$result = ( true === mcd_uri_has_secure_version( $uri ) ) ? 1 : 0;
			} else {
				$result = -1;
			}

			update_post_meta( $post_id, 'valid-https-uri', $result );
		}

		exit();
	}

	/**
	 * Return the allowed fields for a CSP report.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    The list of allowed fields.
	 */
	public function whitelisted_fields() {
		return array(
			'blocked-uri'        => array(
				'sanitize_callback' => array( $this, 'sanitize_blocked_uri' ),
			),
			'document-uri'       => array(
				'sanitize_callback' => 'esc_url',
			),
			'original-policy'    => array(
				'sanitize_callback' => array( $this, 'sanitize_original_policy' ),
			),
			'referrer'           => array(
				'sanitize_callback' => 'esc_url',
			),
			'status-code'        => array(
				'sanitize_callback' => 'absint',
			),
			'violated-directive' => array(
				'sanitize_callback' => array( $this, 'sanitize_violated_directive' ),
			),
		);
	}

	/**
	 * Sanitize the blocked URI passed from the callback.
	 *
	 * The values passed as blocked URI are generally URLs; however, there is at least one special case, "data", which
	 * represents a data URI. In this case, do not sanitize the value as a URL; just return "data".
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $value    The unsanitized blocked URI value.
	 * @return string              The sanitized value.
	 */
	public function sanitize_blocked_uri( $value ) {
		if ( 'data' === trim( $value ) ) { // Data URI
			return 'data';
		} elseif ( '' === trim( $value ) ) { // The root document
			return site_url();
		} else {
			return esc_url( $value );
		}
	}

	/**
	 * Sanitize the original policy passed from the callback.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $value    The unsanitized policy value.
	 * @return string              The sanitized value.
	 */
	public function sanitize_original_policy( $value ) {
		if ( mcd_get_policy()->get_full_policy() === $value ) {
			return $value;
		} else {
			return '';
		}
	}

	/**
	 * Sanitize the directive passed from the callback.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $value    The unsanitized directive value.
	 * @return string              The sanitized value.
	 */
	public function sanitize_violated_directive( $value ) {
		// Grab the whitelisted policy values
		$whitelisted_values = mcd_get_policy()->get_policies();

		if ( in_array( $value, $whitelisted_values ) ) {
			return $value;
		} else {
			return '';
		}
	}
}
endif;

/**
 * Get the one instance of the MCD_Beacon.
 *
 * @since  1.0.0.
 *
 * @return MCD_Beacon
 */
function mcd_get_beacon() {
	return MCD_Beacon::instance();
}

mcd_get_beacon();