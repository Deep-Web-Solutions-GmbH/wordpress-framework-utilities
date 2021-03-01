<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Handlers;

use DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;
use DeepWebSolutions\Framework\Helpers\DataTypes\Strings;

defined( 'ABSPATH' ) || exit;

/**
 * Checks whether a list of PHP settings is compatible with the current environment or not.
 *
 * @see     https://github.com/skyverge/wc-plugin-framework/blob/de7f429af153a17a0fd84cf9a1c56c6ac5ffbc08/woocommerce/class-sv-wc-plugin-dependencies.php
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Dependencies\Handlers
 */
class PHPIncompatibleSettingsHandler extends AbstractDependenciesHandler {
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
		return 'php_settings';
	}

	// endregion

	// region METHODS

	/**
	 * Returns a list of incompatible PHP settings.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 *
	 * @return  array
	 */
	public function get_missing_dependencies(): array {
		$missing = array();

		if ( function_exists( 'ini_get' ) ) {
			foreach ( $this->get_dependencies() as $php_setting => $expected_value ) {
				$environment_value = ini_get( $php_setting );
				if ( empty( $environment_value ) ) {
					continue;
				}

				if ( is_int( $expected_value ) ) {
					$is_size           = ! is_numeric( substr( $environment_value, -1 ) );
					$environment_value = $is_size ? Strings::letter_to_number( $environment_value ) : $environment_value;

					if ( $environment_value < $expected_value ) {
						$missing[ $php_setting ] = array(
							'expected'    => $is_size ? size_format( $expected_value ) : $expected_value,
							'environment' => $is_size ? size_format( $environment_value ) : $environment_value,
							'type'        => 'min',
						);
					}
				} elseif ( $environment_value !== $expected_value ) {
					$missing[ $php_setting ] = array(
						'expected'    => $expected_value,
						'environment' => $environment_value,
					);
				}
			}
		}

		return $missing;
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
		return is_array( $dependency ) && Arrays::has_string_keys( $dependency )
			? $dependency
			: null;
	}

	// endregion
}
