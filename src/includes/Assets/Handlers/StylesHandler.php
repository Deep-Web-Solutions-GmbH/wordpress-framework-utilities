<?php

namespace DeepWebSolutions\Framework\Utilities\Assets\Handlers;

use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunFailureException;
use DeepWebSolutions\Framework\Helpers\FileSystem\Files;
use DeepWebSolutions\Framework\Helpers\WordPress\Request;
use DeepWebSolutions\Framework\Helpers\WordPress\RequestTypesEnum;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for styles.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Assets\Handlers
 */
class StylesHandler extends AbstractAssetsHandler {
	// region FIELDS AND CONSTANTS

	/**
	 * The styles to be registered with WordPress when the handler runs.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array       $styles
	 */
	protected array $styles = array(
		'public' => array(
			'register' => array(),
			'enqueue'  => array(),
		),
		'admin'  => array(
			'register' => array(),
			'enqueue'  => array(),
		),
	);

	/**
	 * The CSS content to be added inline to enqueued styles.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array
	 */
	protected array $styles_inline = array();

	// endregion

	// region GETTERS

	/**
	 * Returns the list of styles registered with WP by this handler instance on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_styles(): array {
		return $this->styles;
	}

	/**
	 * Returns the list of inline styles registered with WP by this handler instance on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_inline_styles(): array {
		return $this->styles_inline;
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * Registers and enqueues the styles with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RunFailureException|null
	 */
	public function run(): ?RunFailureException {
		if ( is_null( $this->is_run ) ) {
			$this->styles = Request::is_type( RequestTypesEnum::FRONTEND_REQUEST ) ? $this->styles['public'] : $this->styles['admin'];
			array_walk( $this->styles[]['register'], array( $this, 'array_walk_register_style' ) );
			array_walk( $this->styles[]['enqueue'], array( $this, 'array_walk_enqueue_style' ) );
			array_walk( $this->styles_inline, array( $this, 'array_walk_add_inline_style' ) );

			$this->is_run     = true;
			$this->run_result = null;
		} else {
			return new RunFailureException( 'The styles handler has already been run.' );
		}

		return $this->run_result;
	}

	// endregion

	// region METHODS

	/**
	 * Registers a public-facing stylesheet.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle                 A string that should uniquely identify the CSS asset.
	 * @param   string  $relative_path          The path to the CSS file relative to WP's root directory.
	 * @param   string  $fallback_version       The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array   $deps                   Array of dependent CSS handles that should be loaded first.
	 * @param   string  $media                  The media query that the CSS asset should be active at.
	 * @param   string  $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a minified state.
	 */
	public function register_public_style( string $handle, string $relative_path, string $fallback_version, array $deps = array(), string $media = 'all', string $constant_name = 'SCRIPT_DEBUG' ): void {
		$this->styles['public']['register'] = $this->add_style( $this->styles['public']['register'], $handle, $relative_path, $fallback_version, $deps, $media, $constant_name );
	}

	/**
	 * Removes a style from the list of assets that should be registered publicly.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     The handle of the asset that should be removed.
	 */
	public function deregister_public_style( string $handle ) {
		$this->styles['public']['register'] = $this->remove( $this->styles['public']['register'], $handle );
	}

	/**
	 * Enqueues a public-facing stylesheet.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle                 A string that should uniquely identify the CSS asset.
	 * @param   string  $relative_path          The path to the CSS file relative to WP's root directory.
	 * @param   string  $fallback_version       The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array   $deps                   Array of dependent CSS handles that should be loaded first.
	 * @param   string  $media                  The media query that the CSS asset should be active at.
	 * @param   string  $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a minified state.
	 */
	public function enqueue_public_style( string $handle, string $relative_path = '', string $fallback_version = '', array $deps = array(), string $media = 'all', string $constant_name = 'SCRIPT_DEBUG' ): void {
		$this->styles['public']['enqueue'] = $this->add_style( $this->styles['public']['enqueue'], $handle, $relative_path, $fallback_version, $deps, $media, $constant_name );
	}

	/**
	 * Removes a style from the list of assets that should be enqueued publicly.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     The handle of the asset that should be removed.
	 */
	public function dequeue_public_style( string $handle ) {
		$this->styles['public']['enqueue'] = $this->remove( $this->styles['public']['enqueue'], $handle );
	}

	/**
	 * Registers an admin-side stylesheet.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle                 A string that should uniquely identify the CSS asset.
	 * @param   string  $relative_path          The path to the CSS file relative to WP's root directory.
	 * @param   string  $fallback_version       The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array   $deps                   Array of dependent CSS handles that should be loaded first.
	 * @param   string  $media                  The media query that the CSS asset should be active at.
	 * @param   string  $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a minified state.
	 */
	public function register_admin_style( string $handle, string $relative_path, string $fallback_version, array $deps = array(), string $media = 'all', string $constant_name = 'SCRIPT_DEBUG' ): void {
		$this->styles['admin']['register'] = $this->add_style( $this->styles['admin']['register'], $handle, $relative_path, $fallback_version, $deps, $media, $constant_name );
	}

	/**
	 * Removes a style from the list of assets that should be registered on the admin-side.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     The handle of the asset that should be removed.
	 */
	public function deregister_admin_style( string $handle ) {
		$this->styles['admin']['register'] = $this->remove( $this->styles['admin']['register'], $handle );
	}

	/**
	 * Registers an admin-side stylesheet.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle                 A string that should uniquely identify the CSS asset.
	 * @param   string  $relative_path          The path to the CSS file relative to WP's root directory.
	 * @param   string  $fallback_version       The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array   $deps                   Array of dependent CSS handles that should be loaded first.
	 * @param   string  $media                  The media query that the CSS asset should be active at.
	 * @param   string  $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a minified state.
	 */
	public function enqueue_admin_style( string $handle, string $relative_path, string $fallback_version, array $deps = array(), string $media = 'all', string $constant_name = 'SCRIPT_DEBUG' ): void {
		$this->styles['admin']['enqueue'] = $this->add_style( $this->styles['admin']['enqueue'], $handle, $relative_path, $fallback_version, $deps, $media, $constant_name );
	}

	/**
	 * Removes a style from the list of assets that should be enqueued on the admin-side.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     The handle of the asset that should be removed.
	 */
	public function dequeue_admin_style( string $handle ) {
		$this->styles['admin']['enqueue'] = $this->remove( $this->styles['admin']['enqueue'], $handle );
	}

	/**
	 * Registers CSS code that should be outputted after a specific handle whenever that style is enqueued.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     The handle of the asset that the inline data should be outputted after.
	 * @param   string  $data       The data to output inline.
	 */
	public function add_inline_style( string $handle, string $data ) {
		$this->styles_inline[] = array(
			'handle' => $handle,
			'data'   => $data,
		);
	}

	// endregion

	// region HELPERS

	/**
	 * Adds a properly formatted style asset to a list of other style assets.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $assets             Array of style assets to append the new style to.
	 * @param   string  $handle             A string that should uniquely identify the CSS asset.
	 * @param   string  $relative_path      The path to the CSS file relative to WP's root directory.
	 * @param   string  $fallback_version   The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array   $deps               Array of dependent CSS handles that should be loaded first.
	 * @param   string  $media              The media query that the CSS asset should be active at.
	 * @param   string  $constant_name      The name of the constant to check for truthful values in case the assets should be loaded in a minified state.
	 *
	 * @return  array
	 */
	protected function add_style( array $assets, string $handle, string $relative_path, string $fallback_version, array $deps, string $media, string $constant_name ): array {
		$wp_filesystem = $this->get_wp_filesystem();

		if ( $wp_filesystem ) {
			$relative_path = $this->maybe_switch_to_minified_file( $relative_path, $constant_name );
			$absolute_path = Files::generate_full_path( $wp_filesystem->abspath(), $relative_path );

			if ( $wp_filesystem->is_file( $absolute_path ) ) {
				$assets[] = array(
					'handle' => $handle,
					'src'    => $relative_path,
					'deps'   => $deps,
					'ver'    => $this->maybe_generate_mtime_version_string( $absolute_path, $fallback_version ),
					'media'  => $media,
				);
			}
		}

		return $assets;
	}

	/**
	 * Registers a style with WP.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $style  Style to register.
	 *
	 * @return  bool    Whether registration was successful or not.
	 */
	protected function array_walk_register_style( array $style ): bool {
		return wp_register_style(
			$style['handle'],
			$style['src'],
			$style['deps'],
			$style['ver'],
			$style['media'],
		);
	}

	/**
	 * Enqueues a style with WP.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $style  Style to register.
	 */
	protected function array_walk_enqueue_style( array $style ): void {
		wp_enqueue_style(
			$style['handle'],
			$style['src'],
			$style['deps'],
			$style['ver'],
			$style['media'],
		);
	}

	/**
	 * Add extra CSS styles to a registered stylesheet.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $style  Style to register.
	 *
	 * @return  bool
	 */
	protected function array_walk_add_inline_style( array $style ): bool {
		return wp_add_inline_style(
			$style['handle'],
			$style['data']
		);
	}

	// endregion
}
