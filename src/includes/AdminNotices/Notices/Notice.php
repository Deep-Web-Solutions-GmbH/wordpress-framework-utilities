<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Notices;

use DeepWebSolutions\Framework\Foundations\Actions\Outputtable\OutputFailureException;

defined( 'ABSPATH' ) || exit;

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
		if ( isset( $this->args['capability'] ) && is_string( $this->args['capability'] ) ) {
			return current_user_can( $this->args['capability'] );
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
		$args          = wp_parse_args( $this->args, array( 'html' => false ) );
		$this->message = boolval( $args['html'] ) ? $this->message : "<p>{$this->message}</p>";

		return parent::output();
	}

	// endregion
}
