<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Helpers;

use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Foundations\PluginComponent\PluginComponentInterface;
use DeepWebSolutions\Framework\Foundations\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesHandlerInterface;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesService;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesServiceAwareInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the dependencies-helpers-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies\Helpers
 */
trait DependenciesHelpersTrait {
	/**
	 * Returns a generated handler ID based on dependencies context.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string|null     $context    The context of the dependencies handler.
	 *
	 * @return  string
	 */
	public function get_dependencies_handler_id( ?string $context = null ): string {
		switch ( $context ) {
			case DependenciesContextsEnum::ACTIVE_STATE:
				$handler_id = 'active_%s';
				break;
			case DependenciesContextsEnum::DISABLED_STATE:
				$handler_id = 'disabled_%s';
				break;
			default:
				$handler_id = '%s';
		}

		if ( $this instanceof PluginComponentInterface ) {
			$handler_id = \sprintf( $handler_id, $this->get_id() );
		} elseif ( $this instanceof PluginInterface ) {
			$handler_id = \sprintf( $handler_id, $this->get_plugin_slug() );
		} else {
			$handler_id = \sprintf( $handler_id, \get_class( $this ) );
		}

		return $handler_id;
	}

	/**
	 * Tries to automagically return an instance of a dependencies handler registered with the dependencies service.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string|null     $context    The context of the dependencies handler.
	 *
	 * @return  DependenciesHandlerInterface|null
	 */
	protected function get_dependencies_handler( ?string $context = null ): ?DependenciesHandlerInterface {
		$handler_id = $this->get_dependencies_handler_id( $context );
		$handler    = null;

		if ( $this instanceof DependenciesServiceAwareInterface ) {
			$handler = $this->get_dependencies_service()->get_handler( $handler_id );
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$handler = $this->get_container()->get( DependenciesService::class )->get_handler( $handler_id );
		}

		return $handler;
	}

	/**
	 * Converts an array of dependency fulfillment status into a simple boolean value.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool|array      $are_deps_fulfilled     The result of the dependencies checking.
	 * @param   callable|null   $func                   Optional function to run the status through for custom results.
	 *
	 * @return  bool
	 */
	protected function check_fulfillment_status( $are_deps_fulfilled, ?callable $func = null ): bool {
		if ( \is_bool( $are_deps_fulfilled ) ) {
			$are_deps_fulfilled = \is_null( $func ) ? $are_deps_fulfilled : \call_user_func( $func, $are_deps_fulfilled );
		} elseif ( \is_array( \reset( $are_deps_fulfilled ) ) ) { // MultiCheckerHandler
			foreach ( $are_deps_fulfilled as $dependencies_status ) {
				if ( \is_null( $func ) ) {
					$unfulfilled = Arrays::search_values( $dependencies_status, false, null, false );
					if ( ! \is_null( $unfulfilled ) ) {
						$are_deps_fulfilled = false;
						break;
					}
				} else {
					$fulfilled = \call_user_func( $func, $dependencies_status );
					if ( false === $fulfilled ) {
						$are_deps_fulfilled = false;
						break;
					}
				}
			}

			$are_deps_fulfilled = \is_array( $are_deps_fulfilled );
		} else { // SingleCheckerHandler
			$are_deps_fulfilled = \is_null( $func )
				? \reset( $are_deps_fulfilled )
				: \call_user_func( $func, $are_deps_fulfilled );
		}

		return $are_deps_fulfilled;
	}
}
