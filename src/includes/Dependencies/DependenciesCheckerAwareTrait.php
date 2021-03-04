<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

/**
 * Basic implementation of the dependencies-checker-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies
 */
trait DependenciesCheckerAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Dependencies checker instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     DependenciesCheckerInterface
	 */
	protected DependenciesCheckerInterface $dependencies_checker;

	// endregion

	// region GETTERS

	/**
	 * Gets the current dependencies checker instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DependenciesCheckerInterface
	 */
	public function get_dependencies_checker(): DependenciesCheckerInterface {
		return $this->dependencies_checker;
	}

	/**
	 * Wrapper around the checker's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_dependencies(): array {
		return $this->get_dependencies_checker()->get_dependencies();
	}

	// endregion

	// region SETTERS

	/**
	 * Sets a dependencies checker instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DependenciesCheckerInterface    $checker        Dependencies checker instance to use from now on.
	 */
	public function set_dependencies_checker( DependenciesCheckerInterface $checker ) {
		$this->dependencies_checker = $checker;
	}

	// endregion

	// region METHODS

	/**
	 * Wrapper around the checker's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_missing_dependencies(): array {
		return $this->get_dependencies_checker()->get_missing_dependencies();
	}

	/**
	 * Wrapper around the checker's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  mixed
	 */
	public function are_dependencies_fulfilled() {
		return $this->get_dependencies_checker()->are_dependencies_fulfilled();
	}

	// endregion
}
