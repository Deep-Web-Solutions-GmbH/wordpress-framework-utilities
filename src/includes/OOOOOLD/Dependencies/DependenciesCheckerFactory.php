<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

defined( 'ABSPATH' ) || exit;

/**
 * Dependencies checker factory to decouple checkers from their using objects.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies
 */
class DependenciesCheckerFactory {
	// region FIELDS AND CONSTANTS

	/**
	 * Collection of instantiated checkers.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     DependenciesChecker[]
	 */
	protected array $checkers = array();

	// endregion

	// region METHODS

	/**
	 * Returns a dependencies checker instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $dependencies   Matrix of dependencies indexed first by optionality, then by type.
	 *
	 * @return  DependenciesChecker
	 */
	public function get_checker( array $dependencies ): DependenciesChecker {
		$deps_hash = hash( 'sha512', wp_json_encode( $dependencies ) );

		if ( ! isset( $this->checkers[ $deps_hash ] ) ) {
			$deps_container               = new DependenciesContainer( $dependencies );
			$this->checkers[ $deps_hash ] = new DependenciesChecker( $deps_container, $deps_hash );
		}

		return $this->checkers[ $deps_hash ];
	}

	// endregion
}

