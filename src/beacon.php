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
		add_action( 'init', array( $this, 'register_post_type' ) );
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
			'menu_icon'           => null,
			'can_export'          => false,
			'delete_with_user'    => false,
			'hierarchical'        => false,
			'has_archive'         => false,
			'query_var'           => true,
			'rewrite'             => false,
			'supports'            => array(
				'title',
				'editor',
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
	 * Handle routing of the beacon request.
	 *
	 * This function identifies the beacon request and sets into motion the actions to record the beacon data.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function handle_report_uri() {
		if ( isset( $_GET['mcd'] ) && 'report' === $_GET['mcd'] ) {
			$contents = json_decode( file_get_contents( 'php://input' ), true );

			wp_insert_post( array(
				'post_type'    => 'csp-report',
				'post_title'    => $contents['csp-report']['blocked-uri'],
				'post_content' => $contents,
			) );

			exit();
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