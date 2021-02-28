<?php

namespace DeepWebSolutions\Framework\Utilities\WordPress\Runnables\Hooks;

defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the hooks handler utility interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\WordPress\Runnables\Hooks
 */
trait HooksHandlerUtilityTrait {
	// region TRAITS

	use HooksHelpersTrait;

	// endregion

	// region METHODS

	/**
	 * Using classes should define their hooks in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   HooksHandler    $hooks_handler  Instance of the hooks handler.
	 */
	abstract public function register_hooks( HooksHandler $hooks_handler ): void;

	// endregion
}
