<?php

namespace DeepWebSolutions\Framework\Utilities\WordPress\Runnables\Hooks;

defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the hooks-handler-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\WordPress\Runnables\Hooks
 */
trait HooksHandlerAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Hooks handler for registering filters and actions.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     HooksHandler
	 */
	protected HooksHandler $hooks_handler;

	// endregion

	// region GETTERS

	/**
	 * Gets the hooks handler instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  HooksHandler
	 */
	public function get_hooks_handler(): HooksHandler {
		return $this->hooks_handler;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets the hooks handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   HooksHandler    $hooks_handler  Instance of the hooks handler.
	 */
	public function set_hooks_handler( HooksHandler $hooks_handler ): void {
		$this->hooks_handler = $hooks_handler;
	}

	// endregion
}
