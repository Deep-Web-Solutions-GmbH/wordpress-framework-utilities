<?php

namespace DeepWebSolutions\Framework\Utilities\Handlers\Traits;

use DeepWebSolutions\Framework\Utilities\Handlers\AdminNoticesHandler;
use DeepWebSolutions\Framework\Utilities\Interfaces\Resources\IdentityInterface;
use DeepWebSolutions\Framework\Utilities\Interfaces\Resources\PluginInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for working with the admin notices handler.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Handlers\Traits
 */
trait AdminNotices {
	// region FIELDS AND CONSTANTS

	/**
	 * Admin notices handler for registering admin notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     AdminNoticesHandler
	 */
	protected AdminNoticesHandler $admin_notices_handler;

	// endregion

	// region GETTERS

	/**
	 * Gets the admin notices handler instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  AdminNoticesHandler
	 */
	protected function get_admin_notices_handler(): AdminNoticesHandler {
		return $this->admin_notices_handler;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets the admin notices handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesHandler     $admin_notices_handler  Instance of the admin notices handler.
	 */
	public function set_admin_notices_handler( AdminNoticesHandler $admin_notices_handler ): void {
		$this->admin_notices_handler = $admin_notices_handler;
	}

	// endregion

	// region METHODS

	/**
	 * Using classes should define their admin notices in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesHandler     $admin_notices_handler      Instance of the admin notices handler.
	 */
	abstract protected function register_admin_notices( AdminNoticesHandler $admin_notices_handler ): void;

	// endregion

	// region HELPERS

	/**
	 * Returns a name for the notice-registering object, as descriptive as possible.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   object|null     $class  The instance that acts as the registrant.
	 *
	 * @return  string
	 */
	protected function get_registrant_name( ?object $class = null ): string {
		$class       = is_null( $class ) ? $this : $class;
		$plugin_name = $this->admin_notices_handler->get_plugin()->get_plugin_name();

		return ( $class instanceof IdentityInterface ) && ! ( $class instanceof PluginInterface )
			? sprintf( '%s: %s', $plugin_name, $class->get_instance_public_name() )
			: $plugin_name;
	}

	/**
	 * Returns a meaningful, hopefully unique, ID for a notice.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name   The actual descriptor of the notice's purpose.
	 * @param   array   $extra  Further descriptors of the notice's purpose.
	 * @param   string  $root   Prepended to all notice IDs inside the same class.
	 *
	 * @return  string
	 */
	public function get_notice_id( string $name, array $extra = array(), string $root = '' ): string {
		if ( $this instanceof IdentityInterface ) {
			$notice_id = array( $this->get_plugin()->get_plugin_slug(), $root ?: $this->get_instance_public_name(), $name ); // phpcs:ignore
		} elseif ( $this instanceof PluginInterface ) {
			$notice_id = array( $this->get_plugin_slug(), $root, $name );
		} else {
			$notice_id = array( $root, $name );
		}

		return str_replace(
			array( ' ', '/', '\\' ),
			array( '-', '', '_' ),
			strtolower(
				join(
					'_',
					array_filter(
						array_merge(
							$notice_id,
							$extra
						)
					)
				)
			)
		);
	}

	// endregion
}
