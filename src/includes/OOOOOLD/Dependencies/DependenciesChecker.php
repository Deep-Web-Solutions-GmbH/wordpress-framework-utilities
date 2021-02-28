<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

use DeepWebSolutions\Framework\Helpers\DataTypes\Strings;
use DeepWebSolutions\Framework\Helpers\FileSystem\FilesystemAwareTrait;
use DeepWebSolutions\Framework\Utilities\Dependencies\Container\DependenciesContainerAwareInterface;
use DeepWebSolutions\Framework\Utilities\Dependencies\Container\DependenciesContainerAwareTrait;
use DeepWebSolutions\Framework\Utilities\WordPress\Hooks\HooksHelpersTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Checks the status of a set of dependencies.
 *
 * @see     https://github.com/skyverge/wc-plugin-framework/blob/de7f429af153a17a0fd84cf9a1c56c6ac5ffbc08/woocommerce/class-sv-wc-plugin-dependencies.php
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies
 */
class DependenciesChecker implements DependenciesContainerAwareInterface {
	// region TRAITS

	use DependenciesContainerAwareTrait;
	use FilesystemAwareTrait;
	use HooksHelpersTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * A hash of the raw dependencies. Useful to cache the results of the checks.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     string
	 */
	protected string $hash;

	// endregion

	// region MAGIC METHODS

	/**
	 * DependenciesChecker constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   DependenciesContainer   $container      Dependencies to check for.
	 * @param   string                  $hash           Hash of the current dependencies.
	 */
	public function __construct( DependenciesContainer $container, string $hash ) {
		$this->set_dependencies_container( $container );
		$this->hash = $hash;
	}

	// endregion

	// region METHODS

	/**
	 * Returns a list of missing dependencies for the given optionality and type.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DependenciesOptionalityEnum
	 * @see     DependenciesEnum
	 *
	 * @param   string  $optionality    Optionality of the checked dependencies.
	 * @param   string  $type           Type of the checked dependencies.
	 *
	 * @return  array
	 */
	public function get_missing_dependencies( string $optionality, string $type ): array {
		switch ( $type ) {
			case DependenciesEnum::TYPE_PHP_EXTENSIONS:
				$result = $this->get_missing_php_extensions( $optionality );
				break;
			case DependenciesEnum::TYPE_PHP_FUNCTIONS:
				$result = $this->get_missing_php_functions( $optionality );
				break;
			case DependenciesEnum::TYPE_PHP_SETTINGS:
				$result = $this->get_incompatible_php_settings( $optionality );
				break;
			case DependenciesEnum::TYPE_WP_PLUGINS:
				$result = $this->get_missing_active_plugins( $optionality );
				break;
			default:
				$result = apply_filters( $this->get_hook_tag( 'missing-dependencies', array( $type ) ), array(), $this->get_dependencies_container(), $optionality ); // phpcs:ignore
		}

		return $result;
	}

	/**
	 * Returns a list of missing PHP extensions.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DependenciesOptionalityEnum
	 *
	 * @param   string  $optionality    Optionality of the checked dependencies.
	 *
	 * @return  array
	 */
	public function get_missing_php_extensions( string $optionality ): array {
		static $extensions = array();

		if ( ! isset( $extensions[ $this->hash ][ $optionality ] ) ) {
			$extensions[ $this->hash ][ $optionality ] = array_filter(
				array_map(
					function( $php_extension ) {
						return extension_loaded( $php_extension ) ? false : $php_extension;
					},
					$this->get_dependencies_container()->get_dependencies_by_optionality_and_type( $optionality, DependenciesEnum::TYPE_PHP_EXTENSIONS )
				)
			);
		}

		return $extensions[ $this->hash ][ $optionality ];
	}

	/**
	 * Returns a list of missing PHP function.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DependenciesOptionalityEnum
	 *
	 * @param   string  $optionality    Optionality of the checked dependencies.
	 *
	 * @return  array
	 */
	public function get_missing_php_functions( string $optionality ): array {
		static $functions = array();

		if ( ! isset( $functions[ $this->hash ][ $optionality ] ) ) {
			$functions[ $this->hash ][ $optionality ] = array_filter(
				array_map(
					function( $php_function ) {
						return function_exists( $php_function ) ? false : $php_function;
					},
					$this->get_dependencies_container()->get_dependencies_by_optionality_and_type( $optionality, DependenciesEnum::TYPE_PHP_FUNCTIONS )
				)
			);
		}

		return $functions[ $this->hash ][ $optionality ];
	}

	/**
	 * Returns a list of incompatible PHP settings.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 *
	 * @see     DependenciesOptionalityEnum
	 *
	 * @param   string  $optionality    Optionality of the checked dependencies.
	 *
	 * @return  array
	 */
	public function get_incompatible_php_settings( string $optionality ): array {
		static $settings = array();

		if ( ! isset( $settings[ $this->hash ][ $optionality ] ) ) {
			$settings[ $this->hash ][ $optionality ] = array();

			if ( function_exists( 'ini_get' ) ) {
				$dependencies = $this->get_dependencies_container()->get_dependencies_by_optionality_and_type( $optionality, DependenciesEnum::TYPE_PHP_SETTINGS );
				foreach ( $dependencies as $php_setting => $expected_value ) {
					$environment_value = ini_get( $php_setting );
					if ( empty( $environment_value ) ) {
						continue;
					}

					if ( is_int( $expected_value ) ) {
						$is_size           = ! is_numeric( substr( $environment_value, -1 ) );
						$environment_value = $is_size ? Strings::letter_to_number( $environment_value ) : $environment_value;

						if ( $environment_value < $expected_value ) {
							$settings[ $this->hash ][ $optionality ][ $php_setting ] = array(
								'expected'    => $is_size ? size_format( $expected_value ) : $expected_value,
								'environment' => $is_size ? size_format( $environment_value ) : $environment_value,
								'type'        => 'min',
							);
						}
					} elseif ( $environment_value !== $expected_value ) {
						$settings[ $this->hash ][ $optionality ][ $php_setting ] = array(
							'expected'    => $expected_value,
							'environment' => $environment_value,
						);
					}
				}
			}
		}

		return $settings[ $this->hash ][ $optionality ];
	}

	/**
	 * Returns a list of missing active plugins.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 *
	 * @see     DependenciesOptionalityEnum
	 *
	 * @param   string  $optionality    Optionality of the checked dependencies.
	 *
	 * @return  array
	 */
	public function get_missing_active_plugins( string $optionality ): array {
		static $plugins = array();

		if ( ! isset( $plugins[ $this->hash ][ $optionality ] ) ) {
			$plugins[ $this->hash ][ $optionality ] = array();

			$dependencies = $this->get_dependencies_container()->get_dependencies_by_optionality_and_type( $optionality, DependenciesEnum::TYPE_WP_PLUGINS );
			foreach ( $dependencies as $active_plugin => $active_plugin_config ) {
				if ( isset( $active_plugin_config['active_checker'] ) && is_callable( $active_plugin_config['active_checker'] ) ) {
					$is_active = boolval( call_user_func( $active_plugin_config['active_checker'] ) );
				} else {
					$is_active = in_array( $active_plugin, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ); // phpcs:ignore
					if ( is_multisite() && ! $is_active ) {
						$is_active = isset( get_site_option( 'active_sitewide_plugins', array() )[ $active_plugin ] );
					}
				}

				if ( ! $is_active ) {
					$plugins[ $this->hash ][ $optionality ][ $active_plugin ] = $active_plugin_config;
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
						$plugins[ $this->hash ][ $optionality ][ $active_plugin ] = $active_plugin_config + array( 'version' => $version );
					}
				}
			}
		}

		return $plugins[ $this->hash ][ $optionality ];
	}

	// endregion
}
