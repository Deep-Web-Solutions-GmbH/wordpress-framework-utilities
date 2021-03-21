<?php

namespace DeepWebSolutions\Framework\Utilities\Assets\Handlers;

use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunFailureException;
use DeepWebSolutions\Framework\Helpers\FileSystem\Files;
use DeepWebSolutions\Framework\Helpers\WordPress\Request;
use DeepWebSolutions\Framework\Helpers\WordPress\RequestTypesEnum;
use DeepWebSolutions\Framework\Utilities\Assets\AbstractAssetsHandler;

\defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for scripts.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Assets\Handlers
 */
class ScriptsHandler extends AbstractAssetsHandler {
	// region FIELDS AND CONSTANTS

	/**
	 * The scripts to be registered with WordPress when the handler runs.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array       $scripts
	 */
	protected array $scripts = array(
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
	 * ScriptsHandler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name   Unique name of the handler.
	 */
	public function __construct( string $name = 'default-scripts' ) { // phpcs:ignore
		parent::__construct( $name );
	}

	// endregion

	// region GETTERS

	/**
	 * Returns the list of scripts registered with WP by this handler instance on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_scripts(): array {
		return $this->scripts;
	}

	/**
	 * Returns the list of inline scripts registered with WP by this handler instance on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_inline_scripts(): array {
		return $this->scripts_inline;
	}

	/**
	 * Returns the list of localization objects registered with WP by this instance on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_localization_objects(): array {
		return $this->scripts_localization;
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * Registers and enqueues the scripts with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RunFailureException|null
	 */
	public function run(): ?RunFailureException {
		if ( \is_null( $this->is_run ) ) {
			$this->scripts = Request::is_type( RequestTypesEnum::FRONTEND_REQUEST ) ? $this->scripts['public'] : $this->scripts['admin'];
			\array_walk( $this->scripts['register'], array( $this, 'array_walk_register_script' ) );
			\array_walk( $this->scripts['enqueue'], array( $this, 'array_walk_enqueue_script' ) );
			\array_walk( $this->scripts_inline, array( $this, 'array_walk_add_inline_script' ) );
			\array_walk( $this->scripts_localization, array( $this, 'array_walk_localize_script' ) );

			$this->is_run     = true;
			$this->run_result = null;
		} else {
			return new RunFailureException( 'The scripts handler has already been run.' );
		}

		return $this->run_result;
	}

	// endregion

	// region METHODS

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
	 * @param   string  $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a non-minified state.
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
	 * @param   string  $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a non-minified state.
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
	 * @param   string  $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a non-minified state.
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
	 * @param   string  $constant_name          The name of the constant to check for truthful values in case the assets should be loaded in a non-minified state.
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
	 * @param   string  $constant_name      The name of the constant to check for truthful values in case the assets should be loaded in a non-minified state.
	 *
	 * @return  array
	 */
	protected function add_script( array $assets, string $handle, string $relative_path, string $fallback_version, array $deps, bool $in_footer, string $constant_name ): array {
		$absolute_path = $this->resolve_absolute_file_path( $relative_path, $constant_name );

		if ( ! \is_null( $absolute_path ) ) {
			$assets[] = array(
				'handle'    => $handle,
				'src'       => $relative_path,
				'deps'      => $deps,
				'ver'       => $this->maybe_generate_mtime_version_string( $absolute_path, $fallback_version ),
				'in_footer' => $in_footer,
			);
		}

		return $assets;
	}

	/**
	 * Registers a script with WP.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $script     Script to register.
	 *
	 * @return  bool    Whether registration was successful or not.
	 */
	protected function array_walk_register_script( array $script ): bool {
		return \wp_register_script(
			$script['handle'],
			$script['src'],
			$script['deps'],
			$script['ver'],
			$script['in_footer'],
		);
	}

	/**
	 * Enqueues a script with WP.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $script     Script to register.
	 */
	protected function array_walk_enqueue_script( array $script ): void {
		\wp_enqueue_script(
			$script['handle'],
			$script['src'],
			$script['deps'],
			$script['ver'],
			$script['in_footer'],
		);
	}

	/**
	 * Adds extra code to a registered script.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $script     Script to register.
	 *
	 * @return  bool
	 */
	protected function array_walk_add_inline_script( array $script ): bool {
		return \wp_add_inline_script(
			$script['handle'],
			$script['data'],
			$script['position']
		);
	}

	/**
	 * Localize a script.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $localization     Localization data.
	 *
	 * @return  bool    Whether registration was successful or not.
	 */
	protected function array_walk_localize_script( array $localization ): bool {
		return \wp_localize_script(
			$localization['handle'],
			$localization['object_name'],
			$localization['object']
		);
	}

	// endregion
}
