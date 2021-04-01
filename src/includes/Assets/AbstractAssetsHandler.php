<?php

namespace DeepWebSolutions\Framework\Utilities\Assets;

use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunLocalTrait;
use DeepWebSolutions\Framework\Foundations\Actions\RunnableInterface;
use DeepWebSolutions\Framework\Foundations\Utilities\Handlers\AbstractHandler;
use DeepWebSolutions\Framework\Helpers\FileSystem\Files;
use DeepWebSolutions\Framework\Helpers\FileSystem\FilesystemAwareTrait;
use DeepWebSolutions\Framework\Helpers\WordPress\Assets;

\defined( 'ABSPATH' ) || exit;

/**
 * Encapsulates the most often-needed logic of an assets handler.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Assets
 */
abstract class AbstractAssetsHandler extends AbstractHandler implements AssetsHandlerInterface, RunnableInterface {
	// region TRAITS

	use FilesystemAwareTrait;
	use RunLocalTrait;

	// endregion

	// region INHERITED METHODS

	/**
	 * Returns the type of the handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_type(): string {
		return 'assets';
	}

	// endregion

	// region HELPERS

	/**
	 * Removes an asset from a list based on its handle.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $assets     The collection of assets to manipulate.
	 * @param   string  $handle     The handle of the asset that should be removed.
	 *
	 * @return  array
	 */
	protected function remove( array $assets, string $handle ): array {
		foreach ( $assets as $index => $asset_info ) {
			if ( $handle === $asset_info['handle'] ) {
				unset( $assets[ $index ] );
				break;
			}
		}

		return $assets;
	}

	/**
	 * Given a relative path to an assets file, tries to resolve any valid alternatives and returns the absolute path
	 * to the resolved file.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $relative_path  Relative path to the asset file.
	 * @param   string  $constant_name  The name of the constant to check for truthful values in case the assets should be loaded in a non-minified state.
	 *
	 * @return  string|null
	 */
	protected function resolve_absolute_file_path( string &$relative_path, string $constant_name ): ?string {
		if ( \filter_var( $relative_path, FILTER_VALIDATE_URL ) ) {
			return $relative_path;
		} else {
			$wp_filesystem = $this->get_wp_filesystem();

			if ( $wp_filesystem ) {
				$relative_path = $this->maybe_switch_to_minified_file( $relative_path, $constant_name );
				$absolute_path = Files::generate_full_path( $wp_filesystem->abspath(), $relative_path );

				if ( $wp_filesystem->is_file( $absolute_path ) ) {
					return $absolute_path;
				}
			}
		}

		return null;
	}

	/**
	 * Maybe updates the relative path such that it loads the minified version of the file, if it exists and minification
	 * enqueuing is active.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $relative_path          The relative path to WP's root directory.
	 * @param   string  $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a non-minified state.
	 *
	 * @return  string  The updated relative path.
	 */
	protected function maybe_switch_to_minified_file( string $relative_path, string $constant_name = 'SCRIPT_DEBUG' ): string {
		$suffix = Assets::maybe_get_minified_suffix( $constant_name );
		if ( ! empty( $suffix ) && strpos( $relative_path, $suffix ) === false ) {
			$wp_filesystem = $this->get_wp_filesystem();
			if ( ! \is_null( $wp_filesystem ) ) {
				$full_path = Files::generate_full_path( $wp_filesystem->abspath(), $relative_path );
				$extension = \pathinfo( $full_path, PATHINFO_EXTENSION );

				$minified_rel_path  = \str_replace( ".{$extension}", "{$suffix}.{$extension}", $relative_path );
				$minified_full_path = Files::generate_full_path( $wp_filesystem->abspath(), $minified_rel_path );

				if ( $wp_filesystem->is_file( $minified_full_path ) ) {
					$relative_path = $minified_rel_path;
				}
			}
		}

		return $relative_path;
	}

	/**
	 * Tries to generate an asset file's version based on its last modified time.
	 * If that fails, defaults to the fallback versioning.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string          $absolute_path      The absolute path to an asset file.
	 * @param   string|null     $fallback_version   The fallback version in case reading the mtime fails.
	 *
	 * @return  string|null
	 */
	protected function maybe_generate_mtime_version_string( string $absolute_path, ?string $fallback_version ): ?string {
		if ( \filter_var( $absolute_path, FILTER_VALIDATE_URL ) ) {
			return $fallback_version;
		} else {
			$wp_filesystem = $this->get_wp_filesystem();
			$version       = $wp_filesystem ? $wp_filesystem->mtime( $absolute_path ) : false;

			return ( empty( $version ) ) ? $fallback_version : \strval( $version );
		}
	}

	// endregion
}
