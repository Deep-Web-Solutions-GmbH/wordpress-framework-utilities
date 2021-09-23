<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Helpers;

\defined( 'ABSPATH' ) || exit;

/**
 * Valid values for dependencies handlers contexts.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies\Helpers
 */
class DependenciesContextsEnum {
	/**
	 * Denotes that the handler is for checking the activation state.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  public
	 * @var     string
	 */
	public const ACTIVE_STATE = 'active';

	/**
	 * Denotes that the handler is for checking the disablement state.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  public
	 * @var     string
	 */
	public const DISABLED_STATE = 'disabled';

	/**
	 * Returns an array of all valid dependencies handlers contexts.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string[]
	 */
	public static function get_all(): array {
		return array( self::ACTIVE_STATE, self::DISABLED_STATE );
	}
}
