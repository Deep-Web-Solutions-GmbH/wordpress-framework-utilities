<?php

namespace DeepWebSolutions\Framework\Utilities\Assets\Handlers;

\defined( 'ABSPATH' ) || exit;

/**
 * Describes a scripts handler utility instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Assets\Handlers
 */
interface ScriptsHandlerRegisterInterface {
	/**
	 * Using classes should define their scripts in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   ScriptsHandler      $scripts_handler    Instance of the scripts handler.
	 */
	public function register_scripts( ScriptsHandler $scripts_handler ): void;
}
