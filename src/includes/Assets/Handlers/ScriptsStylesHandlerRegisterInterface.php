<?php

namespace DeepWebSolutions\Framework\Utilities\Assets\Handlers;

\defined( 'ABSPATH' ) || exit;

/**
 * Describes a scripts handler and a styles handler utility instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Assets\Handlers
 */
interface ScriptsStylesHandlerRegisterInterface {
	/**
	 * Using classes should define their scripts and assets in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   ScriptsHandler      $scripts_handler    Instance of the scripts handler.
	 * @param   StylesHandler       $styles_handler     Instance of the styles handler.
	 */
	public function register_scripts_and_styles( ScriptsHandler $scripts_handler, StylesHandler $styles_handler ): void;
}
