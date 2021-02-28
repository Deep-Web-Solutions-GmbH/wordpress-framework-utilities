<?php

namespace DeepWebSolutions\Framework\Utilities\WordPress\Runnables\Shortcodes;

defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of a shortcodes-handler-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\WordPress\Runnables\Shortcodes
 */
trait ShortcodesHandlerAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Shortcodes handler for registering shortcodes.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     ShortcodesHandler
	 */
	protected ShortcodesHandler $shortcodes_handler;

	// endregion

	// region GETTERS

	/**
	 * Gets the current shortcodes handler instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  ShortcodesHandler
	 */
	public function get_shortcodes_handler(): ShortcodesHandler {
		return $this->shortcodes_handler;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets a shortcodes handler instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   ShortcodesHandler       $shortcodes_handler     Shortcodes handler instance to use from now on.
	 */
	public function set_shortcodes_handler( ShortcodesHandler $shortcodes_handler ): void {
		$this->shortcodes_handler = $shortcodes_handler;
	}

	// endregion
}
