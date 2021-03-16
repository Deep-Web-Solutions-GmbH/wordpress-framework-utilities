<?php

namespace DeepWebSolutions\Framework\Utilities\Assets;

\defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the assets-service-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Assets
 */
trait AssetsServiceAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Assets service for registering CSS and JS.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     AssetsService
	 */
	protected AssetsService $assets_service;

	// endregion

	// region GETTERS

	/**
	 * Gets the assets service instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  AssetsService
	 */
	public function get_assets_service(): AssetsService {
		return $this->assets_service;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets the assets service.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AssetsService   $assets_service     Instance of the assets service.
	 */
	public function set_assets_service( AssetsService $assets_service ) {
		$this->assets_service = $assets_service;
	}

	// endregion
}
