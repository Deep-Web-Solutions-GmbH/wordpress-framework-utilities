<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Handlers;

use DeepWebSolutions\Framework\Utilities\AdminNotices\AbstractAdminNoticesHandler;
use DeepWebSolutions\Framework\Utilities\AdminNotices\Notices\Notice;

\defined( 'ABSPATH' ) || exit;

/**
 * Handles simple notices.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Handlers
 */
class NoticesHandler extends AbstractAdminNoticesHandler {
	/**
	 * Returns the ID of the handler. Since there should be only one handler per type of admin notices,
	 * this is safe.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_id(): string {
		return Notice::class;
	}
}
