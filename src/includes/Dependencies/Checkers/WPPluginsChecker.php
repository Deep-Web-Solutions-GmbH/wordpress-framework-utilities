<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Checkers;

use DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;
use DeepWebSolutions\Framework\Helpers\DataTypes\Booleans;
use DeepWebSolutions\Framework\Helpers\FileSystem\FilesystemAwareTrait;
use DeepWebSolutions\Framework\Utilities\Dependencies\AbstractDependenciesChecker;

\defined( 'ABSPATH' ) || exit;

/**
 * Checks whether a list of WP Plugins is present or not.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Dependencies\Checkers
 */
class WPPluginsChecker extends AbstractDependenciesChecker {
	// region TRAITS

	use FilesystemAwareTrait;

	// endregion

	// region GETTERS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_type(): string {
		return 'active_plugins';
	}

	// endregion

	// region METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_missing_dependencies(): array {
		$missing = array();

		foreach ( $this->get_dependencies() as $dependency ) {
			$is_active = $this->is_plugin_active( $dependency['plugin'], $dependency );

			if ( ! $is_active ) {
				$missing[ $dependency['plugin'] ] = $dependency;
			} elseif ( isset( $dependency['min_version'] ) ) {
				$version = $this->get_active_plugin_version( $dependency['plugin'], $dependency );
				if ( \version_compare( $version, $dependency['min_version'], '<' ) ) {
					$missing[ $dependency['plugin'] ] = $dependency + array( 'version' => $version );
				}
			}
		}

		return $missing;
	}

	// endregion

	// region HELPERS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	protected function is_dependency_valid( $dependency ): bool {
		return \is_array( $dependency ) && Arrays::has_string_keys( $dependency ) && isset( $dependency['plugin'] );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	protected function get_dependency_key(): string {
		return 'plugin';
	}

	/**
	 * Returns whether a given plugin is active or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $plugin             Plugin to check.
	 * @param   array   $plugin_config      Dependency configuration.
	 *
	 * @return  bool
	 */
	protected function is_plugin_active( string $plugin, array $plugin_config ): bool {
		if ( isset( $plugin_config['active_checker'] ) && \is_callable( $plugin_config['active_checker'] ) ) {
			$is_active = Booleans::maybe_cast( \call_user_func( $plugin_config['active_checker'] ), false );
		} else {
			$is_active = \in_array( $plugin, (array) \get_option( 'active_plugins', array() ), true );
			if ( \is_multisite() && ! $is_active ) {
				$is_active = isset( \get_site_option( 'active_sitewide_plugins', array() )[ $plugin ] );
			}
		}

		return $is_active;
	}

	/**
	 * Returns the version of an installed and active plugin.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $plugin             Plugin to check.
	 * @param   array   $plugin_config      Dependency configuration.
	 *
	 * @return  string
	 */
	protected function get_active_plugin_version( string $plugin, array $plugin_config ): string {
		if ( isset( $plugin_config['version_checker'] ) && \is_callable( $plugin_config['version_checker'] ) ) {
			$version = \call_user_func( $plugin_config['version_checker'] );
		} else {
			$wp_filesystem = $this->get_wp_filesystem();
			$version       = '0.0.0';

			if ( ! \is_null( $wp_filesystem ) ) {
				$plugin_data = \get_file_data( \trailingslashit( $wp_filesystem->wp_plugins_dir() ) . $plugin, array( 'Version' => 'Version' ) );
				$version     = $plugin_data['Version'] ?? $version;
			}
		}

		return $version;
	}

	// endregion
}
