<?php

namespace DeepWebSolutions\Framework\Utilities\Handlers\Traits;

use DeepWebSolutions\Framework\Utilities\Handlers\AssetsHandler;
use DeepWebSolutions\Framework\Utilities\Traits\Helpers\Assets as AssetsHelpers;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for working with the assets handler.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Handlers\Traits
 */
trait Assets {
	use AssetsHelpers;

	// region FIELDS AND CONSTANTS

	/**
	 * Assets handler for registering CSS and JS assets.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     AssetsHandler
	 */
	protected AssetsHandler $assets_handler;

	// endregion

	// region GETTERS

	/**
	 * Gets the assets handler instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  AssetsHandler
	 */
	protected function get_assets_handler(): AssetsHandler {
		return $this->assets_handler;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets the assets handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AssetsHandler     $assets_handler   Instance of the assets handler.
	 */
	public function set_assets_handler( AssetsHandler $assets_handler ): void {
		$this->assets_handler = $assets_handler;
	}

	// endregion

	// region METHODS

	/**
	 * Using classes should define their assets in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AssetsHandler   $assets_handler      Instance of the hooks handler.
	 */
	abstract protected function enqueue_assets( AssetsHandler $assets_handler ): void;

	// endregion
}
