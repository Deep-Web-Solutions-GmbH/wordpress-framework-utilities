<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Helpers;

use DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;

\defined( 'ABSPATH' ) || exit;

/**
 * A collection of useful helpers for working with dependencies.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 */
final class Dependencies {
	/**
	 * Converts the result of a call to @see DependenciesHandlerInterface::are_dependencies_fulfilled to a simple boolean.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool|bool[]|bool[][]    $are_deps_fulfilled     The result of dependency checking.
	 * @param   callable|null           $callback               Optional callback to run the status through for custom results.
	 *
	 * @return  bool
	 */
	public static function status_to_boolean( $are_deps_fulfilled, ?callable $callback = null ): bool {
		if ( \is_bool( $are_deps_fulfilled ) ) {
			$are_deps_fulfilled = \is_null( $callback ) ? $are_deps_fulfilled : \call_user_func( $callback, $are_deps_fulfilled );
		} elseif ( \is_array( \reset( $are_deps_fulfilled ) ) ) { // MultiCheckerHandler
			foreach ( $are_deps_fulfilled as $deps_status ) {
				if ( \is_null( $callback ) ) {
					$unfulfilled = Arrays::search_values( $deps_status, false, null, false );
					if ( ! \is_null( $unfulfilled ) ) {
						$are_deps_fulfilled = false;
						break;
					}
				} else {
					$fulfilled = \call_user_func( $callback, $deps_status );
					if ( false === $fulfilled ) {
						$are_deps_fulfilled = false;
						break;
					}
				}
			}

			$are_deps_fulfilled = \is_array( $are_deps_fulfilled );
		} else { // SingleCheckerHandler
			$are_deps_fulfilled = \is_null( $callback )
				? \reset( $are_deps_fulfilled )
				: \call_user_func( $callback, $are_deps_fulfilled );
		}

		return $are_deps_fulfilled;
	}
}
