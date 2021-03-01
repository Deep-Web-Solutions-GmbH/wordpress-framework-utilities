<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Handlers;

defined( 'ABSPATH' ) || exit;

/**
 * Checks whether a list of PHP extensions is present or not.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Dependencies\Handlers
 */
class PHPExtensionsHandler extends AbstractDependenciesHandler {
	// region GETTERS

	/**
	 * Returns the type of dependencies the object checks for.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_dependency_type(): string {
		return 'php_extensions';
	}

	// endregion

	// region METHODS

	/**
	 * Returns a list of missing PHP extensions.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_missing_dependencies(): array {
		return array_filter(
			array_map(
				function( $php_extension ) {
					return extension_loaded( $php_extension ) ? false : $php_extension;
				},
				$this->get_dependencies()
			)
		);
	}

	// endregion

	// region HELPERS

	/**
	 * Makes sure the dependency is valid. If that can't be ensured, return null.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $dependency     Dependency to parse.
	 *
	 * @return  array|null
	 */
	protected function parse_dependency( $dependency ): ?array {
		return is_string( $dependency )
			? array( $dependency )
			: null;
	}

	// endregion
}
