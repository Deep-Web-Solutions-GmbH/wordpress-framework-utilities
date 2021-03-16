<?php

namespace DeepWebSolutions\Framework\Utilities\Hooks\Handlers;

\defined( 'ABSPATH' ) || exit;

/**
 * Describes a hooks-handler-aware instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Hooks\Handlers
 */
interface HooksHandlerAwareInterface {
	/**
	 * Gets the current hooks handler instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  HooksHandler
	 */
	public function get_hooks_handler(): HooksHandler;

	/**
	 * Sets a hooks handler instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   HooksHandler    $hooks_handler      Hooks handler instance to use from now on.
	 */
	public function set_hooks_handler( HooksHandler $hooks_handler );
}
