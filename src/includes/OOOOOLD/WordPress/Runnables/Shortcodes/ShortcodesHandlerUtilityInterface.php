<?php

namespace DeepWebSolutions\Framework\Utilities\WordPress\Runnables\Shortcodes;

defined( 'ABSPATH' ) || exit;

/**
 * Describes a shortcodes handler utility instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\WordPress\Runnables\Shortcodes
 */
interface ShortcodesHandlerUtilityInterface {
	/**
	 * Using classes should define their shortcodes in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   ShortcodesHandler   $shortcodes_handler     Instance of the shortcodes handler.
	 */
	public function register_shortcodes( ShortcodesHandler $shortcodes_handler ): void;
}
