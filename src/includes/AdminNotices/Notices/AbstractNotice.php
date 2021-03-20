<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Notices;

use DeepWebSolutions\Framework\Foundations\Actions\Outputtable\OutputFailureException;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Foundations\Utilities\Storage\AbstractStoreable;
use DeepWebSolutions\Framework\Helpers\Security\Validation;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticeInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticeTypesEnum;

\defined( 'ABSPATH' ) || exit;

/**
 * Encapsulates the most often needed functionality of a notice.
 *
 * @see     https://github.com/TypistTech/wp-admin-notices/blob/master/src/AbstractNotice.php
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Notices
 */
abstract class AbstractNotice extends AbstractStoreable implements AdminNoticeInterface {
	// region FIELDS AND CONSTANTS

	/**
	 * The message to display.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string
	 */
	protected string $message;

	/**
	 * The notice's type.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string
	 */
	protected string $type;

	/**
	 * Whether the notice is persistent or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     bool
	 */
	protected bool $is_persistent;

	/**
	 * Any other relevant arguments.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     array
	 */
	protected array $args;

	// endregion

	// region MAGIC METHODS

	/**
	 * AbstractNotice constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     A unique ID for the notice.
	 * @param   string  $message    The notice's content.
	 * @param   string  $type       The type of the notice.
	 * @param   array   $args       Other relevant arguments.
	 */
	public function __construct( string $handle, string $message, string $type = AdminNoticeTypesEnum::ERROR, array $args = array() ) {
		parent::__construct( $handle );

		$this->message       = $message;
		$this->type          = Validation::validate_allowed_value( $type, AdminNoticeTypesEnum::get_all(), AdminNoticeTypesEnum::ERROR );
		$this->is_persistent = Validation::validate_boolean( $args['persistent'] ?? false, false );
		$this->args          = $args;
	}

	// endregion

	// region GETTERS

	/**
	 * Returns whether the notice is persistent or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function is_persistent(): bool {
		return $this->is_persistent;
	}

	// endregion

	// region METHODS

	/**
	 * By default, a notice should always output.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function should_output(): bool {
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
		if ( $this->should_output() ) {
			echo \sprintf(
				'<div id="%1$s" data-handle="%1$s" class="%2$s">%3$s</div>',
				\esc_attr( $this->get_id() ),
				\esc_attr( \implode( ' ', $this->get_classes() ) ),
				\wp_kses_post( $this->message )
			);
		}

		return null;
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
		return array(
			'notice',
			'notice-' . $this->type,
			'dws-framework-notice',
		);
	}

	// endregion
}
