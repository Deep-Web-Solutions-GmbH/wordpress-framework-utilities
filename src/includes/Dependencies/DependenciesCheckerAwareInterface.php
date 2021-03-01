<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

/**
 * Describes a dependencies-checker-aware instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies
 */
interface DependenciesCheckerAwareInterface {
	/**
	 * Gets the current dependencies checker instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  DependenciesCheckerInterface
	 */
	public function get_dependencies_checker(): DependenciesCheckerInterface;

	/**
	 * Sets a dependencies checker instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DependenciesCheckerInterface    $checker        Dependencies checker instance to use from now on.
	 */
	public function set_dependencies_checker( DependenciesCheckerInterface $checker ): void;
}
