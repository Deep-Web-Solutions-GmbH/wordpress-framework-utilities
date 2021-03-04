<?php

namespace DeepWebSolutions\Framework\Utilities\Assets\Handlers;

use DeepWebSolutions\Framework\Foundations\Helpers\AssetsHelpersTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the scripts-handler-register interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Assets\Handlers
 */
trait ScriptsHandlerRegisterTrait {
	// region TRAITS

	use AssetsHelpersTrait;

	// endregion

	// region METHODS

	/**
	 * Using classes should define their scripts in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   ScriptsHandler      $scripts_handler    Instance of the scripts handler.
	 */
	abstract public function register_scripts( ScriptsHandler $scripts_handler ): void;

	// endregion
}
