<?php

namespace DeepWebSolutions\Framework\Utilities\States\Activeable;

use DeepWebSolutions\Framework\Foundations\Exceptions\NotImplementedException;
use DeepWebSolutions\Framework\Foundations\PluginComponent\PluginComponentInterface;
use DeepWebSolutions\Framework\Foundations\States\Activeable\ActiveableExtensionTrait;
use DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesService;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Abstract trait for dependent activation of instances with dependencies.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Foundations\WordPress
 */
trait ActiveDependenciesTrait {
	// region TRAITS

	use ActiveableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * If the using class is IsActiveExtensionTrait, prevent its activation if required dependencies are not fulfilled.
	 * Optional dependencies can be marked by including the word 'optional' in the key of the result.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @throws  NotImplementedException     Thrown when using this function in an unsupported context.
	 *
	 * @return  bool
	 */
	public function is_active_dependencies(): bool {
		$checker_name = ( $this instanceof PluginComponentInterface ) ? $this->get_instance_id() : \get_class( $this );

		if ( $this instanceof DependenciesServiceAwareInterface ) {
			$are_deps_fulfilled = $this->get_dependencies_service()->are_dependencies_fulfilled( $checker_name );
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$are_deps_fulfilled = $this->get_container()->get( DependenciesService::class )->are_dependencies_fulfilled( $checker_name );
		} else {
			throw new NotImplementedException( 'Dependency checking scenario not supported' );
		}

		if ( \is_array( $are_deps_fulfilled ) ) {
			if ( \is_array( \reset( $are_deps_fulfilled ) ) ) {
				foreach ( $are_deps_fulfilled as $dependencies_status ) {
					$required_status = $this->is_active_required_dependencies( $dependencies_status );
					if ( false === $required_status ) {
						$are_deps_fulfilled = false;
						break;
					}
				}

				$are_deps_fulfilled = \is_array( $are_deps_fulfilled );
			} else {
				$are_deps_fulfilled = $this->is_active_required_dependencies( $are_deps_fulfilled );
			}
		}

		return $are_deps_fulfilled;
	}

	// endregion

	// region HELPERS

	/**
	 * Returns whether an array of booleans denoting dependencies status evaluates to 'true' as far as required dependencies
	 * are concerned. Optional dependencies should be marked by including the work 'optional' in the key of the boolean.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool[]      $dependencies_status    Array to evaluate.
	 *
	 * @return  bool    Whether all required dependencies are true or not.
	 */
	protected function is_active_required_dependencies( array $dependencies_status ): bool {
		$unfulfilled        = Arrays::search_values( $dependencies_status, false, false );
		$are_deps_fulfilled = \is_null( $unfulfilled );

		if ( ! $are_deps_fulfilled && Arrays::has_string_keys( $dependencies_status ) ) {
			$optional_unfulfilled = Arrays::search_keys(
				$unfulfilled,
				true,
				true,
				function( string $key ) {
					return strpos( $key, 'optional' ) !== false;
				}
			) ?? array();
			$are_deps_fulfilled   = ( \count( $unfulfilled ) === \count( $optional_unfulfilled ) );
		}

		return $are_deps_fulfilled;
	}

	// endregion
}
