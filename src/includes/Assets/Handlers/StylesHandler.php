<?php

namespace DeepWebSolutions\Framework\Utilities\Assets\Handlers;

use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunFailureException;
use DeepWebSolutions\Framework\Helpers\DataTypes\Booleans;
use DeepWebSolutions\Framework\Helpers\WordPress\Request;
use DeepWebSolutions\Framework\Helpers\WordPress\RequestTypesEnum;
use DeepWebSolutions\Framework\Utilities\Assets\AbstractAssetsHandler;

\defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for styles.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
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

	// region MAGIC METHODS

	/**
	 * StylesHandler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name   Unique name of the handler.
	 */
	public function __construct( string $name = 'default-styles' ) { // phpcs:ignore
		parent::__construct( $name );
	}

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
	protected function run_local(): ?RunFailureException {
		$this->styles = Request::is_type( RequestTypesEnum::FRONTEND_REQUEST ) ? $this->styles['public'] : $this->styles['admin'];
		\array_walk( $this->styles['register'], array( $this, 'array_walk_register_style' ) );
		\array_walk( $this->styles['enqueue'], array( $this, 'array_walk_enqueue_style' ) );
		\array_walk( $this->styles_inline, array( $this, 'array_walk_add_inline_style' ) );

		return null;
	}

	// endregion

	// region METHODS

	/**
	 * Registers a public-facing stylesheet.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string          $handle                 A string that should uniquely identify the CSS asset.
	 * @param   string          $relative_path          The path to the CSS file relative to WP's root directory.
	 * @param   string|null     $fallback_version       The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array           $deps                   Array of dependent CSS handles that should be loaded first.
	 * @param   string          $media                  The media query that the CSS asset should be active at.
	 * @param   callable|null   $conditions             Function that should evaluate to true for the style to be registered.
	 * @param   string          $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a non-minified state.
	 */
	public function register_public_style( string $handle, string $relative_path, ?string $fallback_version, array $deps = array(), string $media = 'all', ?callable $conditions = null, string $constant_name = 'SCRIPT_DEBUG' ): void {
		$this->styles['public']['register'] = $this->add_style( $this->styles['public']['register'], $handle, $relative_path, $fallback_version, $deps, $media, null, $conditions, $constant_name );
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
	 * @param   string          $handle                 A string that should uniquely identify the CSS asset.
	 * @param   string          $relative_path          The path to the CSS file relative to WP's root directory.
	 * @param   string|null     $fallback_version       The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array           $deps                   Array of dependent CSS handles that should be loaded first.
	 * @param   string          $media                  The media query that the CSS asset should be active at.
	 * @param   callable|null   $conditions             Function that should evaluate to true for the style to be enqueued.
	 * @param   string          $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a non-minified state.
	 */
	public function enqueue_public_style( string $handle, string $relative_path, ?string $fallback_version, array $deps = array(), string $media = 'all', ?callable $conditions = null, string $constant_name = 'SCRIPT_DEBUG' ): void {
		$this->styles['public']['enqueue'] = $this->add_style( $this->styles['public']['enqueue'], $handle, $relative_path, $fallback_version, $deps, $media, null, $conditions, $constant_name );
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
	 * @param   string          $handle                 A string that should uniquely identify the CSS asset.
	 * @param   string          $relative_path          The path to the CSS file relative to WP's root directory.
	 * @param   string|null     $fallback_version       The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array           $deps                   Array of dependent CSS handles that should be loaded first.
	 * @param   string          $media                  The media query that the CSS asset should be active at.
	 * @param   array|null      $hook_suffixes          The admin pages to enqueue on. Null to enqueue everywhere.
	 * @param   callable|null   $conditions             Function that should evaluate to true for the style to be registered.
	 * @param   string          $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a non-minified state.
	 */
	public function register_admin_style( string $handle, string $relative_path, ?string $fallback_version, array $deps = array(), string $media = 'all', ?array $hook_suffixes = null, ?callable $conditions = null, string $constant_name = 'SCRIPT_DEBUG' ): void {
		$this->styles['admin']['register'] = $this->add_style( $this->styles['admin']['register'], $handle, $relative_path, $fallback_version, $deps, $media, $hook_suffixes, $conditions, $constant_name );
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
	 * @param   string          $handle                 A string that should uniquely identify the CSS asset.
	 * @param   string          $relative_path          The path to the CSS file relative to WP's root directory.
	 * @param   string|null     $fallback_version       The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array           $deps                   Array of dependent CSS handles that should be loaded first.
	 * @param   string          $media                  The media query that the CSS asset should be active at.
	 * @param   array|null      $hook_suffixes          The admin pages to enqueue on. Null to enqueue everywhere.
	 * @param   callable|null   $conditions             Function that should evaluate to true for the style to be enqueued.
	 * @param   string          $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a non-minified state.
	 */
	public function enqueue_admin_style( string $handle, string $relative_path, ?string $fallback_version, array $deps = array(), string $media = 'all', ?array $hook_suffixes = null, ?callable $conditions = null, string $constant_name = 'SCRIPT_DEBUG' ): void {
		$this->styles['admin']['enqueue'] = $this->add_style( $this->styles['admin']['enqueue'], $handle, $relative_path, $fallback_version, $deps, $media, $hook_suffixes, $conditions, $constant_name );
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
	 * @param   array           $assets             Array of style assets to append the new style to.
	 * @param   string          $handle             A string that should uniquely identify the CSS asset.
	 * @param   string          $relative_path      The path to the CSS file relative to WP's root directory.
	 * @param   string|null     $fallback_version   The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array           $deps               Array of dependent CSS handles that should be loaded first.
	 * @param   string          $media              The media query that the CSS asset should be active at.
	 * @param   array|null      $hook_suffixes      The admin pages to enqueue on. Null for public pages.
	 * @param   callable|null   $conditions         Function that should evaluate to true for the style to be enqueued/registered.
	 * @param   string          $constant_name      The name of the constant to check for truthful values in case the assets should be loaded in a non-minified state.
	 *
	 * @return  array
	 */
	protected function add_style( array $assets, string $handle, string $relative_path, ?string $fallback_version, array $deps, string $media, ?array $hook_suffixes, ?callable $conditions, string $constant_name ): array {
		$absolute_path = $this->resolve_absolute_file_path( $relative_path, $constant_name );

		if ( ! \is_null( $absolute_path ) ) {
			$assets[] = array(
				'handle'        => $handle,
				'src'           => $relative_path,
				'deps'          => $deps,
				'ver'           => $this->maybe_generate_mtime_version_string( $absolute_path, $fallback_version ),
				'media'         => $media,
				'hook_suffixes' => $hook_suffixes,
				'conditions'    => $conditions,
			);
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
	 * @return  bool|null   Whether registration was successful or not.
	 */
	protected function array_walk_register_style( array $style ): ?bool {
		if ( \is_null( $style['hook_suffixes'] ) || \in_array( $GLOBALS['hook_suffix'] ?? '', $style['hook_suffixes'], true ) ) {
			if ( ! \is_callable( $style['conditions'] ) || Booleans::maybe_cast( \call_user_func( $style['conditions'], $style ), false ) ) {
				return \wp_register_style( $style['handle'], $style['src'], $style['deps'], $style['ver'], $style['media'] );
			}
		}

		return null;
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
		if ( \is_null( $style['hook_suffixes'] ) || \in_array( $GLOBALS['hook_suffix'] ?? '', $style['hook_suffixes'], true ) ) {
			if ( ! \is_callable( $style['conditions'] ) || Booleans::maybe_cast( \call_user_func( $style['conditions'], $style ), false ) ) {
				\wp_enqueue_style( $style['handle'], $style['src'], $style['deps'], $style['ver'], $style['media'] );
			}
		}
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
		return \wp_add_inline_style( $style['handle'], $style['data'] );
	}

	// endregion
}
