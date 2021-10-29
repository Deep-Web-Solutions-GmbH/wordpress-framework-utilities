<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Notices;

use DeepWebSolutions\Framework\Foundations\Actions\Outputtable\OutputFailureException;
use DeepWebSolutions\Framework\Helpers\DataTypes\Booleans;
use DeepWebSolutions\Framework\Helpers\Users;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AbstractAdminNotice;

\defined( 'ABSPATH' ) || exit;

/**
 * Models a regular notice.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Notices
 */
class SimpleAdminNotice extends AbstractAdminNotice {
	// region INHERITED METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function should_output(): bool {
		return Users::has_capabilities( $this->args['capability'] ?? array() );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function output(): ?OutputFailureException {
		$this->message = Booleans::maybe_cast( $this->args['html'] ?? false, false )
			? $this->message : "<p>$this->message</p>";

		return parent::output();
	}

	// endregion
}
