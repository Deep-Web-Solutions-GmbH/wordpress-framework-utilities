<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\CheckerFactory;

use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesCheckerFactory;

/**
 * Basic implementation of the dependencies-checker-factory-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies\CheckerFactory
 */
trait DependenciesCheckerFactoryAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Dependencies checker factory instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     DependenciesCheckerFactory
	 */
	protected DependenciesCheckerFactory $deps_checker_factory;

	// endregion

	// region GETTERS

	/**
	 * Gets the current dependencies checker factory instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DependenciesCheckerFactory
	 */
	public function get_dependencies_checker_factory(): DependenciesCheckerFactory {
		return $this->deps_checker_factory;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets a dependencies checker factory instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DependenciesCheckerFactory      $checker_factory        Dependencies checker factory instance to use from now on.
	 */
	public function set_dependencies_checker_factory( DependenciesCheckerFactory $checker_factory ): void {
		$this->deps_checker_factory = $checker_factory;
	}

	// endregion
}
