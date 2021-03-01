<?php

namespace DeepWebSolutions\Framework\Utilities\Assets;

defined( 'ABSPATH' ) || exit;

/**
 * Describes an assets-service-aware instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Assets
 */
interface AssetsServiceAwareInterface {
	/**
	 * Gets the current assets service instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  AssetsService
	 */
	public function get_assets_service(): AssetsService;

	/**
	 * Sets an assets service instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AssetsService       $assets_service     Assets service instance to use from now on.
	 */
	public function set_assets_service( AssetsService $assets_service ): void;
}
