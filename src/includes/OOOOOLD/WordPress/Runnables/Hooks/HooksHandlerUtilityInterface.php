<?php

namespace DeepWebSolutions\Framework\Utilities\WordPress\Runnables\Hooks;

defined( 'ABSPATH' ) || exit;

/**
 * Describes a hooks handler utility instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\WordPress\Runnables\Hooks
 */
interface HooksHandlerUtilityInterface {
	/**
	 * Using classes should define their hooks in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   HooksHandler    $hooks_handler  Instance of the hooks handler.
	 */
	public function register_hooks( HooksHandler $hooks_handler ): void;
}
