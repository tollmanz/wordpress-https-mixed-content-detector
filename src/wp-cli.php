<?php
if ( ! class_exists( 'MCD_Command' ) ) :
/**
 * Define the "mcd" WP CLI command.
 *
 * @since 1.1.0.
 */
class MCD_Command extends WP_CLI_Command {
	/**
	 * List the all of the violations logged.
	 *
	 * ## EXAMPLES
	 *
	 *     # Show all violations
	 *     wp mcd list
	 *
	 * @since  1.1.0.
	 *
	 * @subcommand list
	 *
	 * @return void
	 */
	public function _list() {
		// Set up check and x marks
		$resolved   = \cli\Colors::colorize( "%G✓%n", true );
		$unresolved = \cli\Colors::colorize( "%R✖%n", true );

		$data = mcd_get_violation_data();

		// Reformat data for table display
		$final_data = array();

		foreach ( $data as $key => $report ) {
			foreach ( $report as $subkey => $value ) {
				if ( 'original-policy' !== $subkey ) {
					if ( 'resolved' === $subkey ) {
						$value = ( 1 === $value ) ? $resolved : $unresolved;
					} else if ( 'valid-https-uri' === $subkey ) {
						if ( 1 === $value ) {
							$value = $resolved;
						} elseif ( 0 === $value ) {
							$value = $unresolved;
						} else {
							$value = __( 'N/A', 'zdt-mdc' );
						}
					}

					$final_data[ $key ][] = $value;
				}
			}
		}

		wp_reset_postdata();

		if ( count( $data ) > 0 ) {
			// Display results
			$table = new \cli\Table();

			$table->setHeaders( array(
				__( 'ID', 'zdt-mdc' ),
				__( 'Blocked URI', 'zdt-mdc' ),
				__( 'Document URI', 'zdt-mdc' ),
				__( 'Referrer', 'zdt-mdc' ),
				__( 'Violated Directive', 'zdt-mdc' ),
				__( 'Status', 'zdt-mdc' ),
				__( 'HTTPS', 'zdt-mdc' ),
			) );

			$table->setRows( $final_data );
			$table->display();
		} else {
			WP_CLI::warning( __( 'There are no CSP violations logged.', 'zdt-mdc' ) );
		}
	}

	/**
	 * Resolve a single or multiple CSP Reports.
	 *
	 * ## OPTIONS
	 *
	 * [<id>]
	 * : The ID of the CSP Report to resolve.
	 *
	 * [--all]
	 * : Resolve all CSP Reports.
	 *
	 * ## EXAMPLES
	 *
	 *     # Resolve CSP Report with post ID of 35
	 *     wp mcd resolve 35
	 *
	 *     # Remove all CSP Reports
	 *     wp mcd resolve --all
	 *
	 * @since 1.1.0.
	 *
	 * @subcommand resolve
	 *
	 * @param  array    $args          List of arguments passed to command.
	 * @param  array    $assoc_args    List of flags passed to command
	 * @return void
	 */
	public function _resolve( $args, $assoc_args ) {
		$id  = ( ! empty( $args[0] ) ) ? absint( $args[0] ) : 0;
		$all = ( isset( $assoc_args['all'] ) );

		$resolutions = 0;

		if ( 0 !== $id ) {
			if ( false !== mcd_mark_violation_resolved( $id ) ) {
				$resolutions++;
			}
		} elseif ( true === $all ) {
			$resolutions = mcd_mark_all_violations_resolved();
		}

		if ( 0 === $resolutions ) {
			$message = __( 'No reports marked as resolved', 'zdt-mcd' );
		} else {
			$message = sprintf(
				_n(
					'1 report marked as resolved',
					'%s reports marked as resolved',
					absint( $resolutions ),
					'zdt-mcd'
				),
				absint( $resolutions )
			);
		}

		WP_CLI::success( $message );
	}

	/**
	 * Unresolve a single or multiple CSP Reports.
	 *
	 * ## OPTIONS
	 *
	 * [<id>]
	 * : The ID of the CSP Report to unresolve.
	 *
	 * [--all]
	 * : Unresolve all CSP Reports.
	 *
	 * ## EXAMPLES
	 *
	 *     # Unresolve CSP Report with post ID of 35
	 *     wp mcd unresolve 35
	 *
	 *     # Unresolve all CSP Reports
	 *     wp mcd unresolve --all
	 *
	 * @since 1.1.0.
	 *
	 * @subcommand unresolve
	 *
	 * @param  array    $args          List of arguments passed to command.
	 * @param  array    $assoc_args    List of flags passed to command
	 * @return void
	 */
	public function _unresolve( $args, $assoc_args ) {
		$id  = ( ! empty( $args[0] ) ) ? absint( $args[0] ) : 0;
		$all = ( isset( $assoc_args['all'] ) );

		$violations_unresolved = 0;

		if ( 0 !== $id ) {
			if ( false !== mcd_mark_violation_unresolved( $id ) ) {
				$violations_unresolved++;
			}
		} elseif ( true === $all ) {
			$violations_unresolved = mcd_mark_all_violations_unresolved();
		}

		if ( 0 === $violations_unresolved ) {
			$message = __( 'No reports marked as unresolved', 'zdt-mcd' );
		} else {
			$message = sprintf(
				_n(
					'1 report marked as unresolved',
					'%s reports marked as unresolved',
					absint( $violations_unresolved ),
					'zdt-mcd'
				),
				absint( $violations_unresolved )
			);
		}

		WP_CLI::success( $message );
	}

	/**
	 * Remove a single or multiple CSP Reports.
	 *
	 * ## OPTIONS
	 *
	 * [<id>]
	 * : The ID of the CSP Report to remove.
	 *
	 * [--all]
	 * : Remove all CSP Reports.
	 *
	 * ## EXAMPLES
	 *
	 *     # Resolve CSP Report with post ID of 35
	 *     wp mcd remove 35
	 *
	 *     # Remove all CSP Reports
	 *     wp mcd remove --all
	 *
	 * @since 1.1.0.
	 *
	 * @subcommand remove
	 *
	 * @param  array    $args          List of arguments passed to command.
	 * @param  array    $assoc_args    List of flags passed to command
	 * @return void
	 */
	public function _remove( $args, $assoc_args ) {
		$id  = ( ! empty( $args[0] ) ) ? absint( $args[0] ) : 0;
		$all = ( isset( $assoc_args['all'] ) );

		$removals = 0;

		if ( 0 !== $id ) {
			if ( false !== mcd_remove_violation( $id ) ) {
				$removals++;
			}
		} elseif ( true === $all ) {
			$removals = mcd_remove_all_violations();
		}

		if ( 0 === $removals ) {
			$message = __( 'No reports removed', 'zdt-mcd' );
		} else {
			$message = sprintf(
				_n(
					'1 report removed',
					'%s reports removed',
					absint( $removals ),
					'zdt-mcd'
				),
				absint( $removals )
			);
		}

		WP_CLI::success( $message );
	}
}
endif;

WP_CLI::add_command( 'mcd', 'MCD_Command' );