<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Notices;

use DeepWebSolutions\Framework\Foundations\Actions\Outputtable\OutputFailureException;
use DeepWebSolutions\Framework\Helpers\Security\Validation;
use DeepWebSolutions\Framework\Helpers\WordPress\Users;

\defined( 'ABSPATH' ) || exit;

/**
 * Definition of a regular notice.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Notices
 */
class Notice extends AbstractNotice {
	// region METHODS

	/**
	 * Checks whether the notice should be outputted or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool    Whether the notice should be outputted or not.
	 */
	public function should_output(): bool {
		if ( isset( $this->args['capability'] ) && \is_string( $this->args['capability'] ) ) {
			return Users::has_capabilities( (array) $this->args['capability'] );
		}

		return true;
	}

	/**
	 * Outputs the notice.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  OutputFailureException|null
	 */
	public function output(): ?OutputFailureException {
		$this->message = Validation::validate_boolean( $this->args['html'] ?? false, false )
			? $this->message : "<p>{$this->message}</p>";

		return parent::output();
	}

	// endregion
}
