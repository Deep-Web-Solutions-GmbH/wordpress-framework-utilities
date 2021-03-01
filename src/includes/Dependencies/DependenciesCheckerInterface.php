<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

defined( 'ABSPATH' ) || exit;

/**
 * Describes a dependencies checker instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies
 */
interface DependenciesCheckerInterface {
	// region GETTERS

	/**
	 * Returns the dependencies checked for.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_dependencies(): array;

	// endregion

	// region METHODS

	/**
	 * Returns the unfulfilled dependencies.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_missing_dependencies(): array;

	/**
	 * Returns whether the dependencies are fulfilled or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  mixed
	 */
	public function are_dependencies_fulfilled();

	// endregion
}
