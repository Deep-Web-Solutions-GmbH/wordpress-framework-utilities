<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\States;

use DeepWebSolutions\Framework\Foundations\Exceptions\NotImplementedException;
use DeepWebSolutions\Framework\Foundations\PluginComponent\PluginComponentInterface;
use DeepWebSolutions\Framework\Foundations\States\Disableable\DisableableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\States\DisableableInterface;
use DeepWebSolutions\Framework\Foundations\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesService;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesServiceAwareInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Abstract trait for dependent disablement of instances with dependencies.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies\States
 */
trait DisabledDependenciesTrait {
	// region TRAITS

	use DisableableExtensionTrait;

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
	public function is_disabled_dependencies(): bool {
		$is_disabled = false;

		if ( $this instanceof DisableableInterface ) {
			$handler_id = ( $this instanceof PluginComponentInterface ? $this->get_id() : \get_class( $this ) ) . '_disabled';

			if ( $this instanceof DependenciesServiceAwareInterface ) {
				$are_deps_fulfilled = $this->get_dependencies_service()->are_dependencies_fulfilled( $handler_id );
			} elseif ( $this instanceof ContainerAwareInterface ) {
				$are_deps_fulfilled = $this->get_container()->get( DependenciesService::class )->are_dependencies_fulfilled( $handler_id );
			} else {
				throw new NotImplementedException( 'Dependency checking scenario not supported' );
			}

			if ( \is_array( \reset( $are_deps_fulfilled ) ) ) { // MultiCheckerHandler
				foreach ( $are_deps_fulfilled as $dependencies_status ) {
					$unfulfilled = Arrays::search_values( $dependencies_status, false, false );
					if ( false === \is_null( $unfulfilled ) ) {
						$are_deps_fulfilled = false;
						break;
					}
				}

				$is_disabled = ! \is_array( $are_deps_fulfilled );
			} else { // SingleCheckerHandler
				$is_disabled = ! \reset( $are_deps_fulfilled );
			}
		}

		return $is_disabled;
	}

	// endregion
}
