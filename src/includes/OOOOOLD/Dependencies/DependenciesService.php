<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

use DeepWebSolutions\Framework\Utilities\Dependencies\CheckerFactory\DependenciesCheckerFactoryAwareInterface;
use DeepWebSolutions\Framework\Utilities\Dependencies\CheckerFactory\DependenciesCheckerFactoryAwareTrait;
use DeepWebSolutions\Framework\Utilities\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Utilities\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Utilities\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Utilities\WordPress\Hooks\HooksHelpersTrait;

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
	use HooksHelpersTrait;
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
	 * Returns a boolean matrix indexed first by optionality, then by type of whether the dependencies are fulfilled or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $dependencies   Matrix of dependencies indexed first by optionality, then by type.
	 *
	 * @return  array
	 */
	public function get_dependencies_status( array $dependencies ): array {
		$missing_dependencies = $this->get_missing_dependencies( $dependencies );

		$dependencies_status = array();
		foreach ( $missing_dependencies as $optionality => $missing_by_type ) {
			$dependencies_status[ $optionality ] = array();
			foreach ( $missing_by_type as $type => $missing ) {
				$dependencies_status[ $optionality ][ $type ] = empty( $missing );
			}
		}

		return apply_filters( $this->get_hook_tag( 'dependencies-status' ), $dependencies_status, $missing_dependencies, $this ); // phpcs:ignore
	}

	/**
	 * Returns a matrix of unfulfilled dependencies indexed first by optionality, then by type.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $dependencies   Matrix of dependencies indexed first by optionality, then by type.
	 *
	 * @return  array
	 */
	public function get_missing_dependencies( array $dependencies ): array {
		$deps_checker = $this->get_dependencies_checker_factory()->get_checker( $dependencies );
		$dependencies = $deps_checker->get_dependencies_container()->get_dependencies();

		$missing_dependencies = array();
		foreach ( $dependencies as $optionality => $deps_by_type ) {
			$missing_dependencies[ $optionality ] = array();

			foreach ( array_keys( $deps_by_type ) as $type ) {
				$missing_dependencies[ $optionality ][ $type ] = $deps_checker->get_missing_dependencies( $optionality, $type );
			}
		}

		return apply_filters( $this->get_hook_tag( 'missing-dependencies' ), $missing_dependencies, $dependencies, $this ); // phpcs:ignore
	}

	// endregion
}
