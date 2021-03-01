<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Handlers;

use DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;
use DeepWebSolutions\Framework\Helpers\FileSystem\FilesystemAwareTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Checks whether a list of WP Plugins is present or not.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Dependencies\Handlers
 */
class WPPluginsHandler extends AbstractDependenciesHandler {
	// region TRAITS

	use FilesystemAwareTrait;

	// endregion

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
		return 'active_plugins';
	}

	// endregion

	// region METHODS

	/**
	 * Returns a list of missing active plugins.
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

		foreach ( $this->get_dependencies() as $active_plugin => $active_plugin_config ) {
			if ( isset( $active_plugin_config['active_checker'] ) && is_callable( $active_plugin_config['active_checker'] ) ) {
				$is_active = boolval( call_user_func( $active_plugin_config['active_checker'] ) );
			} else {
				$is_active = in_array( $active_plugin, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ); // phpcs:ignore
				if ( is_multisite() && ! $is_active ) {
					$is_active = isset( get_site_option( 'active_sitewide_plugins', array() )[ $active_plugin ] );
				}
			}

			if ( ! $is_active ) {
				$missing[ $active_plugin ] = $active_plugin_config;
			} elseif ( isset( $active_plugin_config['min_version'] ) ) {
				if ( isset( $active_plugin_config['version_checker'] ) && is_callable( $active_plugin_config['version_checker'] ) ) {
					$version = call_user_func( $active_plugin_config['version_checker'] );
				} else {
					$wp_filesystem = $this->get_wp_filesystem();
					$version       = '0.0.0';

					if ( $wp_filesystem ) {
						$plugin_data = get_file_data( trailingslashit( $wp_filesystem->wp_plugins_dir() ) . $active_plugin, array( 'Version' => 'Version' ) );
						if ( isset( $plugin_data['Version'] ) ) {
							$version = $plugin_data['Version'];
						}
					}
				}

				if ( version_compare( $active_plugin_config['min_version'], $version, '>' ) ) {
					$missing[ $active_plugin ] = $active_plugin_config + array( 'version' => $version );
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
