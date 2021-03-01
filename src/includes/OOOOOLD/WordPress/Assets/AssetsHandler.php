<?php

namespace DeepWebSolutions\Framework\Utilities\WordPress\Assets;

use DeepWebSolutions\Framework\Helpers\FileSystem\Files;
use DeepWebSolutions\Framework\Helpers\FileSystem\FilesystemAwareTrait;
use DeepWebSolutions\Framework\Helpers\WordPress\Assets;
use DeepWebSolutions\Framework\Helpers\WordPress\Request;
use DeepWebSolutions\Framework\Helpers\WordPress\RequestTypesEnum;
use DeepWebSolutions\Framework\Utilities\General\Interfaces\Exceptions\ResetFailure;
use DeepWebSolutions\Framework\Utilities\General\Interfaces\Exceptions\RunFailure;
use DeepWebSolutions\Framework\Utilities\General\Interfaces\RunnableInterface;
use DeepWebSolutions\Framework\Utilities\WordPress\Hooks\HooksHandler;
use DeepWebSolutions\Framework\Utilities\WordPress\Hooks\HooksHandlerAwareInterface;
use DeepWebSolutions\Framework\Utilities\WordPress\Hooks\HooksHandlerUtilityTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for assets.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Handlers
 */
class AssetsHandler implements HooksHandlerAwareInterface, RunnableInterface {
	// region TRAITS

	use FilesystemAwareTrait;
	use HooksHandlerUtilityTrait;

	// endregion

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
	protected array $styles = array();

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

	/**
	 * The scripts to be registered with WordPress when the handler runs.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array       $scripts
	 */
	protected array $scripts = array();

	/**
	 * The JS content to be added inline to enqueued scripts.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array
	 */
	protected array $scripts_inline = array();

	/**
	 * The localization objects for scripts to be registered with WordPress when the handler runs.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array       $scripts
	 */
	protected array $scripts_localization = array();

	// endregion

	// region MAGIC METHODS

	/**
	 * AssetsMediator constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   HooksHandler $hooks_mediator Instance of the hooks mediator.
	 */
	public function __construct( HooksHandler $hooks_mediator ) {
		$this->reset();
		$this->register_hooks( $hooks_mediator );
	}

	// endregion

	// region INHERITED FUNCTIONS

	/**
	 * Register hooks with the hooks mediator.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   HooksHandler $hooks_mediator Instance of the hooks handler.
	 *
	 * @see     HooksServiceRegisterTrait::register_hooks()
	 */
	protected function register_hooks( HooksHandler $hooks_mediator ): void {
		if ( Request::is_type( RequestTypesEnum::FRONTEND_REQUEST ) ) {
			// Register assets on public side of the website.
			$hooks_mediator->add_action( 'wp_enqueue_scripts', $this, 'run', PHP_INT_MAX );
		} elseif ( Request::is_type( RequestTypesEnum::ADMIN_REQUEST ) ) {
			// Register assets on the admin-side of the website.
			$hooks_mediator->add_action( 'admin_enqueue_scripts', $this, 'run', PHP_INT_MAX );
		}
	}

	/**
	 * Registers and enqueues the styles and scripts with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RunFailure|null
	 */
	public function run(): ?RunFailure {
		$this->styles = Request::is_type( RequestTypesEnum::FRONTEND_REQUEST ) ? $this->styles['public'] : $this->styles['admin'];
		foreach ( $this->styles['register'] as $style ) {
			wp_register_style(
				$style['handle'],
				$style['src'],
				$style['deps'],
				$style['ver'],
				$style['media'],
			);
		}
		foreach ( $this->styles['enqueue'] as $style ) {
			wp_enqueue_style(
				$style['handle'],
				$style['src'],
				$style['deps'],
				$style['ver'],
				$style['media'],
			);
		}
		foreach ( $this->styles_inline as $style ) {
			wp_add_inline_style(
				$style['handle'],
				$style['data']
			);
		}

		$this->scripts = Request::is_type( RequestTypesEnum::FRONTEND_REQUEST ) ? $this->scripts['public'] : $this->scripts['admin'];
		foreach ( $this->scripts['register'] as $script ) {
			wp_register_script(
				$script['handle'],
				$script['src'],
				$script['deps'],
				$script['ver'],
				$script['in_footer'],
			);
		}
		foreach ( $this->scripts['enqueue'] as $script ) {
			wp_enqueue_script(
				$script['handle'],
				$script['src'],
				$script['deps'],
				$script['ver'],
				$script['in_footer'],
			);
		}
		foreach ( $this->scripts_inline as $script ) {
			wp_add_inline_script(
				$script['handle'],
				$script['data'],
				$script['position']
			);
		}
		foreach ( $this->scripts_localization as $localization ) {
			wp_localize_script(
				$localization['handle'],
				$localization['object_name'],
				$localization['object']
			);
		}

		$this->reset();
		return null;
	}

	/**
	 * Sets the handler into a valid state.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  ResetFailure|null
	 */
	public function reset(): ?ResetFailure {
		$this->styles  = array(
			'public' => array(
				'register' => array(),
				'enqueue'  => array(),
			),
			'admin'  => array(
				'register' => array(),
				'enqueue'  => array(),
			),
		);
		$this->scripts = array(
			'public' => array(
				'register' => array(),
				'enqueue'  => array(),
			),
			'admin'  => array(
				'register' => array(),
				'enqueue'  => array(),
			),
		);

		$this->scripts_inline = array();
		$this->styles_inline  = array();

		$this->scripts_localization = array();

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

	/**
	 * Registers a public-facing script.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   string  $handle                 A string that should uniquely identify the CSS asset.
	 * @param   string  $relative_path          The path to the CSS file relative to WP's root directory.
	 * @param   string  $fallback_version       The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array   $deps                   Array of dependent CSS handles that should be loaded first.
	 * @param   bool    $in_footer              Whether the script asset should be loaded in the footer or the header of the page.
	 * @param   string  $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a minified state.
	 */
	public function register_public_script( string $handle, string $relative_path, string $fallback_version, array $deps = array(), bool $in_footer = true, string $constant_name = 'SCRIPT_DEBUG' ): void {
		$this->scripts['public']['register'] = $this->add_script( $this->scripts['public']['register'], $handle, $relative_path, $fallback_version, $deps, $in_footer, $constant_name );
	}

	/**
	 * Removes a script from the list of assets that should be registered publicly.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     The handle of the asset that should be removed.
	 */
	public function deregister_public_script( string $handle ): void {
		$this->scripts['public']['register'] = $this->remove( $this->scripts['public']['register'], $handle );
	}

	/**
	 * Enqueues a public-facing script.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   string  $handle                 A string that should uniquely identify the CSS asset.
	 * @param   string  $relative_path          The path to the CSS file relative to WP's root directory.
	 * @param   string  $fallback_version       The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array   $deps                   Array of dependent CSS handles that should be loaded first.
	 * @param   bool    $in_footer              Whether the script asset should be loaded in the footer or the header of the page.
	 * @param   string  $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a minified state.
	 */
	public function enqueue_public_script( string $handle, string $relative_path, string $fallback_version, array $deps = array(), bool $in_footer = true, string $constant_name = 'SCRIPT_DEBUG' ): void {
		$this->scripts['public']['enqueue'] = $this->add_script( $this->scripts['public']['enqueue'], $handle, $relative_path, $fallback_version, $deps, $in_footer, $constant_name );
	}

	/**
	 * Removes a script from the list of assets that should be enqueued publicly.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     The handle of the asset that should be removed.
	 */
	public function dequeue_public_script( string $handle ): void {
		$this->scripts['public']['enqueue'] = $this->remove( $this->scripts['public']['enqueue'], $handle );
	}

	/**
	 * Registers an admin-side script.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   string  $handle                 A string that should uniquely identify the CSS asset.
	 * @param   string  $relative_path          The path to the CSS file relative to WP's root directory.
	 * @param   string  $fallback_version       The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array   $deps                   Array of dependent CSS handles that should be loaded first.
	 * @param   bool    $in_footer              Whether the script asset should be loaded in the footer or the header of the page.
	 * @param   string  $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a minified state.
	 */
	public function register_admin_script( string $handle, string $relative_path, string $fallback_version, array $deps = array(), bool $in_footer = true, string $constant_name = 'SCRIPT_DEBUG' ): void {
		$this->scripts['admin']['register'] = $this->add_script( $this->scripts['admin']['register'], $handle, $relative_path, $fallback_version, $deps, $in_footer, $constant_name );
	}

	/**
	 * Removes a script from the list of assets that should be registered on the admin-side.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     The handle of the asset that should be removed.
	 */
	public function deregister_admin_script( string $handle ): void {
		$this->scripts['admin']['register'] = $this->remove( $this->scripts['admin']['register'], $handle );
	}

	/**
	 * Enqueues an admin-side script.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   string  $handle                 A string that should uniquely identify the CSS asset.
	 * @param   string  $relative_path          The path to the CSS file relative to WP's root directory.
	 * @param   string  $fallback_version       The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array   $deps                   Array of dependent CSS handles that should be loaded first.
	 * @param   bool    $in_footer              Whether the script asset should be loaded in the footer or the header of the page.
	 * @param   string  $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a minified state.
	 */
	public function enqueue_admin_script( string $handle, string $relative_path, string $fallback_version, array $deps = array(), bool $in_footer = true, string $constant_name = 'SCRIPT_DEBUG' ): void {
		$this->scripts['admin']['enqueue'] = $this->add_script( $this->scripts['admin']['enqueue'], $handle, $relative_path, $fallback_version, $deps, $in_footer, $constant_name );
	}

	/**
	 * Removes a script from the list of assets that should be enqueued on the admin-side.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     The handle of the asset that should be removed.
	 */
	public function dequeue_admin_script( string $handle ): void {
		$this->scripts['admin']['enqueue'] = $this->remove( $this->scripts['admin']['enqueue'], $handle );
	}

	/**
	 * Registers JS code that should be outputted before/after a specific handle whenever that script is enqueued.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     The handle of the asset that the inline data should be outputted after.
	 * @param   string  $data       The data to output inline.
	 * @param   string  $position   Whether to output the data after or before the actual script. Default after.
	 */
	public function add_inline_script( string $handle, string $data, string $position = 'after' ) {
		$this->scripts_inline[] = array(
			'handle'   => $handle,
			'data'     => $data,
			'position' => $position,
		);
	}

	/**
	 * Registers a JS variable that should be outputted before the script of a specific handle is outputted whenever enqueued.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle         The handle of the asset that the inline data should be outputted before.
	 * @param   string  $object_name    The name of the JS object holding the data.
	 * @param   array   $data           The data to assign to the outputted JS object.
	 */
	public function localize_script( string $handle, string $object_name, array $data ) {
		$this->scripts_localization[] = array(
			'handle'      => $handle,
			'object_name' => $object_name,
			'object'      => $data,
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
	 * Adds a properly formatted script asset to a list of other script assets.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $assets             Array of script assets to append the new script to.
	 * @param   string  $handle             A string that should uniquely identify the CSS asset.
	 * @param   string  $relative_path      The path to the CSS file relative to WP's root directory.
	 * @param   string  $fallback_version   The string to be used as a cache-busting fallback if everything else fails.
	 * @param   array   $deps               Array of dependent CSS handles that should be loaded first.
	 * @param   bool    $in_footer          Whether the script asset should be loaded in the footer or the header of the page.
	 * @param   string  $constant_name      The name of the constant to check for truthful values in case the assets should be loaded in a minified state.
	 *
	 * @return  array
	 */
	protected function add_script( array $assets, string $handle, string $relative_path, string $fallback_version, array $deps, bool $in_footer, string $constant_name ): array {
		$wp_filesystem = $this->get_wp_filesystem();

		if ( $wp_filesystem ) {
			$relative_path = $this->maybe_switch_to_minified_file( $relative_path, $constant_name );
			$absolute_path = Files::generate_full_path( $wp_filesystem->abspath(), $relative_path );

			if ( $wp_filesystem->is_file( $absolute_path ) ) {
				$assets[] = array(
					'handle'    => $handle,
					'src'       => $relative_path,
					'deps'      => $deps,
					'ver'       => $this->maybe_generate_mtime_version_string( $absolute_path, $fallback_version ),
					'in_footer' => $in_footer,
				);
			}
		}

		return $assets;
	}

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
	 * Maybe updates the relative path such that it loads the minified version of the file, if it exists and minification
	 * enqueuing is active.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $relative_path          The relative path to WP's root directory.
	 * @param   string  $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a minified state.
	 *
	 * @return  string  The updated relative path.
	 */
	protected function maybe_switch_to_minified_file( string $relative_path, string $constant_name = 'SCRIPT_DEBUG' ): string {
		$suffix = Assets::maybe_get_minified_suffix( $constant_name );
		if ( ! empty( $suffix ) && strpos( $relative_path, $suffix ) === false ) {
			$wp_filesystem = $this->get_wp_filesystem();
			if ( $wp_filesystem ) {
				$full_path = Files::generate_full_path( $wp_filesystem->abspath(), $relative_path );
				$extension = pathinfo( $full_path, PATHINFO_EXTENSION );

				$minified_rel_path  = str_replace( ".{$extension}", "{$suffix}.{$extension}", $relative_path );
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
	 * @param   string  $absolute_path      The absolute path to an asset file.
	 * @param   string  $fallback_version   The fallback version in case reading the mtime fails.
	 *
	 * @return  string
	 */
	protected function maybe_generate_mtime_version_string( string $absolute_path, string $fallback_version ): string {
		$wp_filesystem = $this->get_wp_filesystem();
		$version       = $wp_filesystem ? $wp_filesystem->mtime( $absolute_path ) : false;

		return ( empty( $version ) ) ? $fallback_version : strval( $version );
	}

	// endregion
}
