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
	public function set_dependencies_checker( DependenciesCheckerInterface $checker ): void {
		$this->dependencies_checker = $checker;
	}

	// endregion
}
