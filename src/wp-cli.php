<?php
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
		/**
		 * Colorizing the results leads to the table display incorrectly rendering the right hand border. This is due to
		 * a bug in WP CLI (https://github.com/wp-cli/php-cli-tools/issues/59). Colors are pretty important to communicate
		 * a failed checked, so this is implemented with a broken table display.
		 */
		$success = \cli\Colors::colorize( "%G✓%n", true );
		$failure = \cli\Colors::colorize( "%R✖%n", true );

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

WP_CLI::add_command( 'mcd', 'MCD_Command' );