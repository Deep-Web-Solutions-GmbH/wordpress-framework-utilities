<?php

namespace DeepWebSolutions\Framework\Utilities\Services\Traits\Dependencies;

use DeepWebSolutions\Framework\Utilities\Services\DependenciesService;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for working with the dependencies service.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Services\Traits\Dependencies
 */
trait DependenciesAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Instance of the dependencies checker service.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     DependenciesService|null
	 */
	protected ?DependenciesService $dependencies_service = null;

	// endregion

	// region GETTERS

	/**
	 * Gets the dependencies service instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DependenciesService
	 */
	public function get_dependencies_service(): DependenciesService {
		if ( is_null( $this->dependencies_service ) ) {
			$this->dependencies_service = new DependenciesService(
				array(
					'php_extensions' => array(
						'required' => $this->get_required_php_extensions(),
						'optional' => $this->get_optional_php_extensions(),
					),
					'php_functions'  => array(
						'required' => $this->get_required_php_functions(),
						'optional' => $this->get_optional_php_functions(),
					),
					'php_settings'   => array(
						'required' => $this->get_required_php_settings(),
						'optional' => $this->get_optional_php_settings(),
					),
					'active_plugins' => array(
						'required' => $this->get_required_active_plugins(),
						'optional' => $this->get_optional_active_plugins(),
					),
				)
			);
		}

		return $this->dependencies_service;
	}

	// endregion

	// region METHODS

	/**
	 * Wrapper around the service's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_dependencies_status(): array {
		return $this->get_dependencies_service()->get_dependencies_status();
	}

	/**
	 * The using class should define its dependent PHP extensions in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DependenciesService::__construct()
	 *
	 * @return  array
	 */
	protected function get_required_php_extensions(): array {
		return array();
	}

	/**
	 * The using class should define its optional PHP extensions in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DependenciesService::__construct()
	 *
	 * @return  array
	 */
	protected function get_optional_php_extensions(): array {
		return array();
	}

	/**
	 * The using class should define its dependent PHP functions in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DependenciesService::__construct()
	 *
	 * @return  array
	 */
	protected function get_required_php_functions(): array {
		return array();
	}

	/**
	 * The using class should define its optional PHP functions in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DependenciesService::__construct()
	 *
	 * @return  array
	 */
	protected function get_optional_php_functions(): array {
		return array();
	}

	/**
	 * The using class should define its dependent PHP settings in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DependenciesService::__construct()
	 *
	 * @return  array
	 */
	protected function get_required_php_settings(): array {
		return array();
	}

	/**
	 * The using class should define its optional PHP settings in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DependenciesService::__construct()
	 *
	 * @return  array
	 */
	protected function get_optional_php_settings(): array {
		return array();
	}

	/**
	 * The using class should define its dependent WP plugins in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DependenciesService::__construct()
	 *
	 * @return  array
	 */
	protected function get_required_active_plugins(): array {
		return array();
	}

	/**
	 * The using class should define its optional WP plugins in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DependenciesService::__construct()
	 *
	 * @return  array
	 */
	protected function get_optional_active_plugins(): array {
		return array();
	}

	// endregion
}
