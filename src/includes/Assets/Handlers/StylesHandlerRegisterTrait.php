<?php

namespace DeepWebSolutions\Framework\Utilities\Assets\Handlers;

use DeepWebSolutions\Framework\Foundations\Helpers\AssetsHelpersTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the styles-handler-register interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Assets\Handlers
 */
trait StylesHandlerRegisterTrait {
	// region TRAITS

	use AssetsHelpersTrait;

	// endregion

	// region METHODS

	/**
	 * Using classes should define their styles in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   StylesHandler       $styles_handler     Instance of the styles handler.
	 */
	abstract public function register_styles( StylesHandler $styles_handler ): void;

	// endregion
}
