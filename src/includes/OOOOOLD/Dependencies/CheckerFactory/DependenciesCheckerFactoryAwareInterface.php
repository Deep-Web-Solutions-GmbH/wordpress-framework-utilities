<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\CheckerFactory;

use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesCheckerFactory;

/**
 * Describes a dependencies-checker-factory-aware instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies\CheckerFactory
 */
interface DependenciesCheckerFactoryAwareInterface {
	/**
	 * Gets the current dependencies checker factory instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DependenciesCheckerFactory
	 */
	public function get_dependencies_checker_factory(): DependenciesCheckerFactory;

	/**
	 * Sets a dependencies checker factory instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DependenciesCheckerFactory     $checker_factory     Dependencies checker factory instance to use from now on.
	 */
	public function set_dependencies_checker_factory( DependenciesCheckerFactory $checker_factory ): void;
}
