<?php
if ( ! class_exists( 'MCD_Command' ) ) :
/**
 * Define the "mcd" WP CLI command.
 *
 * @since 1.1.0.
 */
class MCD_Command extends WP_CLI_Command {
	/**
	 * List the violations.
	 *
	 * @since  1.1.0.
	 *
	 * @subcommand list
	 *
	 * @return void
	 */
	public function _list() {
		$data = mcd_get_violation_data();

		// Reformat data for table display
		$final_data = array();

		foreach ( $data as $key => $report ) {
			foreach ( $report as $subkey => $value ) {
				$final_data[ $key ][] = $value;
			}
		}

		wp_reset_postdata();

		// Display results
		$table = new \cli\Table();

		$table->setHeaders( array(
			__( 'Blocked URI', 'zdt-mdc' ),
			__( 'Document URI', 'zdt-mdc' ),
			__( 'Referrer', 'zdt-mdc' ),
			__( 'Violated Directive', 'zdt-mdc' ),
			__( 'Original Policy', 'zdt-mdc' ),
		) );

		$table->setRows( $final_data );
		$table->display();
	}
}
endif;

WP_CLI::add_command( 'mcd', 'MCD_Command' );