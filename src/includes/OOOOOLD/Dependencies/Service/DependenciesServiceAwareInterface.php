<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Service;

use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesService;

/**
 * Describes a dependencies-service-aware instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies\Service
 */
interface DependenciesServiceAwareInterface {
	/**
	 * Gets the current dependencies service instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DependenciesService
	 */
	public function get_dependencies_service(): DependenciesService;

	/**
	 * Sets a dependencies service instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DependenciesService       $service      Dependencies service instance to use from now on.
	 */
	public function set_dependencies_service( DependenciesService $service ): void;
}
