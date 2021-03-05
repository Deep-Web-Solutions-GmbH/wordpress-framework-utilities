<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the dependencies-service-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies
 */
trait DependenciesServiceAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Dependencies service instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     DependenciesService
	 */
	protected DependenciesService $dependencies_service;

	// endregion

	// region GETTERS

	/**
	 * Gets the current dependencies service instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DependenciesService
	 */
	public function get_dependencies_service(): DependenciesService {
		return $this->dependencies_service;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets a dependencies service instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DependenciesService    $service     Dependencies service instance to use from now on.
	 */
	public function set_dependencies_service( DependenciesService $service ) {
		$this->dependencies_service = $service;
	}

	// endregion

	// region METHODS

	/**
	 * Wrapper around the service's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $checker_name   The checker to retrieve the dependencies from.
	 *
	 * @return  array
	 */
	public function get_dependencies( string $checker_name ): array {
		return $this->get_dependencies_service()->get_dependencies( $checker_name );
	}

	/**
	 * Wrapper around the service's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $checker_name   The checker to retrieve the missing dependencies from.
	 *
	 * @return  array
	 */
	public function get_missing_dependencies( string $checker_name ): array {
		return $this->get_dependencies_service()->get_missing_dependencies( $checker_name );
	}

	/**
	 * Wrapper around the service's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $checker_name   The checker to retrieve the dependencies status from.
	 *
	 * @return  mixed
	 */
	public function are_dependencies_fulfilled( string $checker_name ) {
		return $this->get_dependencies_service()->are_dependencies_fulfilled( $checker_name );
	}

	// endregion
}
