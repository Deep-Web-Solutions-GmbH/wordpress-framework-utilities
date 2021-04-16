<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\States;

use DeepWebSolutions\Framework\Foundations\Exceptions\NotImplementedException;
use DeepWebSolutions\Framework\Foundations\States\Activeable\ActiveableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\States\ActiveableInterface;
use DeepWebSolutions\Framework\Foundations\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesService;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Dependencies\Helpers\DependenciesContextsEnum;
use DeepWebSolutions\Framework\Utilities\Dependencies\Helpers\DependenciesHelpersTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Abstract trait for dependent activation of instances with dependencies.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies\States
 */
trait ActiveDependenciesTrait {
	// region TRAITS

	use ActiveableExtensionTrait;
	use DependenciesHelpersTrait;

	// endregion

	// region METHODS

	/**
	 * If the using class is activeable, prevent its activation if required dependencies are not fulfilled.
	 * Optional dependencies can be marked by including the word 'optional' in the key of the result.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @throws  NotFoundExceptionInterface      Thrown if the container can't find an entry.
	 * @throws  ContainerExceptionInterface     Thrown if the container encounters any other error.
	 * @throws  NotImplementedException         Thrown when using this function in an unsupported context.
	 *
	 * @return  bool
	 */
	public function is_active_dependencies(): bool {
		$is_active = true;

		if ( $this instanceof ActiveableInterface ) {
			$handler = $this->get_dependencies_handler( DependenciesContextsEnum::ACTIVE_STATE );
			if ( \is_null( $handler ) ) {
				throw new NotImplementedException( 'Dependency checking scenario not supported' );
			}

			$are_deps_fulfilled = $handler->are_dependencies_fulfilled();
			$is_active          = $this->check_fulfillment_status( $are_deps_fulfilled, array( $this, 'is_active_required_dependencies' ) );
		}

		return $is_active;
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
	 * @return  bool    Whether all required dependencies are fulfilled or not.
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
