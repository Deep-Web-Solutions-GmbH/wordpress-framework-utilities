<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Checks the status of a set of dependencies.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies
 */
class DependenciesService implements DependenciesCheckerFactoryAwareInterface, PluginAwareInterface {
	// region TRAITS

	use DependenciesCheckerFactoryAwareTrait;
	use PluginAwareTrait;

	// endregion

	// region MAGIC METHODS

	/**
	 * DependenciesService constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   PluginInterface             $plugin                 Plugin instance.
	 * @param   DependenciesCheckerFactory  $deps_checker_factory   Dependency checker factory instance.
	 */
	public function __construct( PluginInterface $plugin, DependenciesCheckerFactory $deps_checker_factory ) {
		$this->set_plugin( $plugin );
		$this->set_dependencies_checker_factory( $deps_checker_factory );
	}

	// endregion

	// region METHODS

	/**
	 * Returns the dependencies checked by a given checker.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $checker_name   The checker to retrieve the dependencies from.
	 *
	 * @return  array
	 */
	public function get_dependencies( string $checker_name ): array {
		return $this->get_dependencies_checker( $checker_name )->get_dependencies();
	}

	/**
	 * Returns the missing dependencies of a given checker.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $checker_name   The checker to retrieve the missing dependencies from.
	 *
	 * @return  array
	 */
	public function get_missing_dependencies( string $checker_name ): array {
		return $this->get_dependencies_checker( $checker_name )->get_missing_dependencies();
	}

	/**
	 * Returns the dependencies status of a given checker.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $checker_name   The checker to retrieve the dependencies status from.
	 *
	 * @return  mixed
	 */
	public function are_dependencies_fulfilled( string $checker_name ) {
		return $this->get_dependencies_checker( $checker_name )->are_dependencies_fulfilled();
	}

	// endregion
}
