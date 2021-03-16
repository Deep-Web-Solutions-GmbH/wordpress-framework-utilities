<?php

namespace DeepWebSolutions\Framework\Utilities\Hooks\Handlers;

\defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the hooks-handler-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Hooks\Handlers
 */
trait HooksHandlerAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Hooks handler instance.
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
	 * Gets the current hooks handler instance set on the object.
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
	 * Sets a hooks handler instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   HooksHandler    $hooks_handler      Hooks handler instance to use from now on.
	 */
	public function set_hooks_handler( HooksHandler $hooks_handler ) {
		$this->hooks_handler = $hooks_handler;
	}

	// endregion
}
