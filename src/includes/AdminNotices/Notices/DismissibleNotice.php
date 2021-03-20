<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Notices;

\defined( 'ABSPATH' ) || exit;

/**
 * Definition of a dismissible notice.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Notices
 */
class DismissibleNotice extends Notice {
	// region FIELDS AND CONSTANTS

	/**
	 * Stores whether the notice is dismissed or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     bool
	 */
	protected bool $is_dismissed = false;

	// endregion

	// region GETTERS

	/**
	 * Returns whether the notice is dismissed or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function is_dismissed(): bool {
		return $this->is_dismissed;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets the notice's dismissed status.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool    $is_dismissed   Whether the notice is dismissed or not.
	 */
	public function set_dismissed( bool $is_dismissed ): void {
		$this->is_dismissed = $is_dismissed;
	}

	// endregion

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
		return parent::should_output() && ! $this->is_dismissed();
	}

	// endregion

	// region HELPERS

	/**
	 * Returns a list of CSS classes to include on the notice.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string[]
	 */
	protected function get_classes(): array {
		return \array_merge( parent::get_classes(), array( 'is-dismissible' ) );
	}

	// endregion
}
