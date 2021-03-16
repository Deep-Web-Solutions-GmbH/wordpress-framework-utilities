<?php

namespace DeepWebSolutions\Framework\Utilities\Assets\Handlers;

\defined( 'ABSPATH' ) || exit;

/**
 * Describes a styles handler utility instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Assets\Handlers
 */
interface StylesHandlerRegisterInterface {
	/**
	 * Using classes should define their styles in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   StylesHandler       $styles_handler     Instance of the styles handler.
	 */
	public function register_styles( StylesHandler $styles_handler ): void;
}
