<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Container;

use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesContainer;

/**
 * Describes a dependencies-container-aware instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies\Container
 */
interface DependenciesContainerAwareInterface {
	/**
	 * Gets the current dependencies container instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DependenciesContainer
	 */
	public function get_dependencies_container(): DependenciesContainer;

	/**
	 * Sets a dependencies container instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DependenciesContainer       $container      Dependencies container instance to use from now on.
	 */
	public function set_dependencies_container( DependenciesContainer $container ): void;
}
