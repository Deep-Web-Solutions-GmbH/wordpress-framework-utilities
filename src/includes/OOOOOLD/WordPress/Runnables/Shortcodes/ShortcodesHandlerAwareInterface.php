<?php

namespace DeepWebSolutions\Framework\Utilities\WordPress\Runnables\Shortcodes;

defined( 'ABSPATH' ) || exit;

/**
 * Describes a shortcodes-handler-aware instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\WordPress\Runnables\Shortcodes
 */
interface ShortcodesHandlerAwareInterface {
	/**
	 * Gets the current shortcodes handler instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  ShortcodesHandler
	 */
	public function get_shortcodes_handler(): ShortcodesHandler;

	/**
	 * Sets a shortcodes handler instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   ShortcodesHandler       $shortcodes_handler     Shortcodes handler instance to use from now on.
	 */
	public function set_shortcodes_handler( ShortcodesHandler $shortcodes_handler ): void;
}
