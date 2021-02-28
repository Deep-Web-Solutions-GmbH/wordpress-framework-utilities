<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Checker;

use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesChecker;

/**
 * Basic implementation of the dependencies-checker-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies\Checker
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
	 * @var     DependenciesChecker
	 */
	protected DependenciesChecker $dependencies_checker;

	// endregion

	// region GETTERS

	/**
	 * Gets the current dependencies checker instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DependenciesChecker
	 */
	public function get_dependencies_checker(): DependenciesChecker {
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
	 * @param   DependenciesChecker     $checker        Dependencies checker instance to use from now on.
	 */
	public function set_dependencies_checker( DependenciesChecker $checker ): void {
		$this->dependencies_checker = $checker;
	}

	// endregion
}
