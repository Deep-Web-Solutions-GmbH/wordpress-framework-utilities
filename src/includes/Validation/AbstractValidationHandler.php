<?php

namespace DeepWebSolutions\Framework\Utilities\Validation;

use DeepWebSolutions\Framework\Foundations\PluginUtilities\Handlers\AbstractHandler;

\defined( 'ABSPATH' ) || exit;

/**
 * Template for encapsulating some of the most often needed functionality of a validation handler.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Validation
 */
abstract class AbstractValidationHandler extends AbstractHandler implements ValidationHandlerInterface {
	// region INHERITED METHODS

	/**
	 * Returns the type of the handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_type(): string {
		return 'validation';
	}

	// endregion
}
