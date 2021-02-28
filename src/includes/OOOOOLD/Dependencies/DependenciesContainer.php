<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

use DeepWebSolutions\Framework\Helpers\WordPress\Misc;

defined( 'ABSPATH' ) || exit;

/**
 * Container for a set of dependencies.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies
 */
class DependenciesContainer {
	// region FIELDS AND CONSTANTS

	/**
	 * Matrix of dependencies indexed first by optionality, then by type.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array[]
	 */
	protected array $dependencies = array();

	// endregion

	// region MAGIC METHODS

	/**
	 * DependenciesContainer constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $dependencies   Matrix of dependencies indexed first by optionality, then by type.
	 */
	public function __construct( array $dependencies ) {
		$this->dependencies = Misc::wp_parse_args_recursive( $dependencies, $this->get_dependencies_defaults() );
	}

	// endregion

	// region GETTERS

	/**
	 * Returns the parsed dependencies matrix.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array[]
	 */
	public function get_dependencies(): array {
		return $this->dependencies;
	}

	/**
	 * Returns a default, empty dependencies matrix.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  \array[][]
	 */
	public function get_dependencies_defaults(): array {
		$defaults_types = array(
			DependenciesEnum::TYPE_PHP_EXTENSIONS => array(),
			DependenciesEnum::TYPE_PHP_FUNCTIONS  => array(),
			DependenciesEnum::TYPE_PHP_SETTINGS   => array(),
			DependenciesEnum::TYPE_WP_PLUGINS     => array(),
		);

		return array(
			DependenciesEnum::OPTIONALITY_REQUIRED => $defaults_types,
			DependenciesEnum::OPTIONALITY_OPTIONAL => $defaults_types,
		);
	}

	// endregion

	// region METHODS

	/**
	 * Returns all defined dependencies for a given optionality and type.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DependenciesOptionalityEnum
	 * @see     DependenciesEnum
	 *
	 * @param   string  $optionality    Optionality of the returned dependencies.
	 * @param   string  $type           Type of the returned dependencies.
	 *
	 * @return  array
	 */
	public function get_dependencies_by_optionality_and_type( string $optionality, string $type ): array {
		return $this->get_dependencies_by_optionality( $optionality )[ $type ] ?? array();
	}

	/**
	 * Returns all defined dependencies for a given optionality.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DependenciesOptionalityEnum
	 *
	 * @param   string  $optionality    Optionality of the returned dependencies.
	 *
	 * @return  array
	 */
	public function get_dependencies_by_optionality( string $optionality ): array {
		return $this->dependencies[ $optionality ] ?? array();
	}

	/**
	 * Returns all defined dependencies for a given type.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DependenciesEnum
	 *
	 * @param   string  $type           Type of the returned dependencies.
	 *
	 * @return  array
	 */
	public function get_dependencies_by_type( string $type ): array {
		return array_combine(
			array_keys( $this->dependencies ),
			array_map(
				function( array $dependencies ) use ( $type ) {
					return $dependencies[ $type ] ?? array();
				},
				$this->dependencies
			),
		);
	}

	// endregion
}
