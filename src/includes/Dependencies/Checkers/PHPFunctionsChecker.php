<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Checkers;

use DeepWebSolutions\Framework\Utilities\Dependencies\AbstractDependenciesChecker;

\defined( 'ABSPATH' ) || exit;

/**
 * Checks whether a list of PHP functions is present or not.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Dependencies\Checkers
 */
class PHPFunctionsChecker extends AbstractDependenciesChecker {
	// region GETTERS

	/**
	 * Returns the type of dependencies the object checks for.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_type(): string {
		return 'php_functions';
	}

	// endregion

	// region METHODS

	/**
	 * Returns a list of missing PHP functions.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_missing_dependencies(): array {
		return \array_filter(
			\array_map(
				function( $php_function ) {
					return \function_exists( $php_function ) ? false : $php_function;
				},
				$this->get_dependencies()
			)
		);
	}

	// endregion

	// region HELPERS

	/**
	 * Checks whether the dependency is valid for the current handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $dependency     Dependency to check.
	 *
	 * @return  bool
	 */
	protected function is_dependency_valid( $dependency ): bool {
		return \is_string( $dependency );
	}

	// endregion
}
