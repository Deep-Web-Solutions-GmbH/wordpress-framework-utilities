<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

/**
 * Basic implementation of the dependencies-checker-factory-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies
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
	public function set_dependencies_checker_factory( DependenciesCheckerFactory $checker_factory ) {
		$this->deps_checker_factory = $checker_factory;
	}

	// endregion

	// region METHODS

	/**
	 * Wrapper around the factory's own method.
	 *
	 * @param   string  $name   The name of the checker.
	 *
	 * @return  DependenciesCheckerInterface
	 */
	public function get_dependencies_checker( string $name ): DependenciesCheckerInterface {
		return $this->get_dependencies_checker_factory()->get_checker( $name );
	}

	// endregion
}
