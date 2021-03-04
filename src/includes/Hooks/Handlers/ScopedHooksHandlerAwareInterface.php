<?php

namespace DeepWebSolutions\Framework\Utilities\Hooks\Handlers;

defined( 'ABSPATH' ) || exit;

/**
 * Describes a hooks-handler-aware instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Hooks\Handlers
 */
interface ScopedHooksHandlerAwareInterface {
	/**
	 * Gets the current scoped hooks handler instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  ScopedHooksHandler
	 */
	public function get_scoped_hooks_handler(): ScopedHooksHandler;

	/**
	 * Sets a scoped hooks handler instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   ScopedHooksHandler      $scoped_hooks_handler       Scoped hooks handler instance to use from now on.
	 *
	 * @return  mixed
	 */
	public function set_scoped_hooks_handler( ScopedHooksHandler $scoped_hooks_handler );
}
