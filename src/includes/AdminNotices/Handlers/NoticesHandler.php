<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Handlers;

use DeepWebSolutions\Framework\Utilities\AdminNotices\Notices\Notice;

defined( 'ABSPATH' ) || exit;

/**
 * Handles simple notices.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Handlers
 */
class NoticesHandler extends AbstractHandler {
	// region GETTERS

	/**
	 * Returns the type of notices the instance handles.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_notices_type(): string {
		return Notice::class;
	}

	// endregion
}
