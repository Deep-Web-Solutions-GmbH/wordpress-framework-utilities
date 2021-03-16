<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

\defined( 'ABSPATH' ) || exit;

/**
 * Describes an object that checks whether a list of dependencies is present or not.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Dependencies
 */
interface DependenciesHandlerInterface extends DependenciesCheckerInterface {
	// region GETTERS

	/**
	 * Returns the type of dependencies the object checks for.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_dependency_type(): string;

	/**
	 * Returns the name of the handler to differentiate multiple ones of the same type.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_name(): string;

	// endregion

	// region METHODS

	/**
	 * Adds a dependency to the list of dependencies to check for.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $dependency     The dependency to check for.
	 *
	 * @return  bool    Whether the dependency was successfully registered or not.
	 */
	public function register_dependency( $dependency ): bool;

	/**
	 * Returns whether the dependencies are fulfilled or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function are_dependencies_fulfilled(): bool;

	// endregion
}
