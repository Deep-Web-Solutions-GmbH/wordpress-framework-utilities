<?php

namespace DeepWebSolutions\Framework\Utilities\Hooks\Handlers;

defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the scoped-hooks-handler-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Hooks\Handlers
 */
trait ScopedHooksHandlerAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Scoped hooks handler instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     ScopedHooksHandler
	 */
	protected ScopedHooksHandler $scoped_hooks_handler;

	// endregion

	// region GETTERS

	/**
	 * Gets the current scoped hooks handler instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  ScopedHooksHandler
	 */
	public function get_scoped_hooks_handler(): ScopedHooksHandler {
		return $this->scoped_hooks_handler;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets a scoped hooks handler instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   ScopedHooksHandler      $scoped_hooks_handler       Scoped hooks handler instance to use from now on.
	 */
	public function set_scoped_hooks_handler( ScopedHooksHandler $scoped_hooks_handler ): void {
		$this->scoped_hooks_handler = $scoped_hooks_handler;
	}

	// endregion
}
