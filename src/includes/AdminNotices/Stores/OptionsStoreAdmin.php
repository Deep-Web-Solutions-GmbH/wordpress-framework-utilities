<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Stores;

use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticeInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesStoreInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Store for admin notices stored in the options table.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Stores
 */
class OptionsStoreAdmin implements PluginAwareInterface, AdminNoticesStoreInterface {
	// region TRAITS

	use PluginAwareTrait;

	// endregion

	// region MAGIC METHODS

	/**
	 * OptionsStore constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   PluginInterface     $plugin     Instance of the plugin.
	 */
	public function __construct( PluginInterface $plugin ) {
		$this->set_plugin( $plugin );
	}

	// endregion

	// region GETTERS

	/**
	 * Returns the store's type.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_type(): string {
		return 'options';
	}

	/**
	 * Returns all the stored notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   array   $params     NOT USED BY THIS STORE.
	 *
	 * @return  AdminNoticeInterface[]
	 */
	public function get_notices( array $params = array() ): array {
		return (array) get_option( $this->generate_notices_option_key(), array() );
	}

	// endregion

	// region METHODS

	/**
	 * Adds one or more notices to the store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   array                $params     NOT USED BY THIS STORE.
	 * @param   AdminNoticeInterface ...$notices Notice(s) to add.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function add_notice( array $params, AdminNoticeInterface ...$notices ): bool {
		$existing_notices = $this->get_notices();

		foreach ( $notices as $notice ) {
			$existing_notices[ $notice->get_handle() ] = $notice;
		}

		return update_option(
			$this->generate_notices_option_key(),
			$existing_notices
		);
	}

	/**
	 * Removes a notice from the store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   string  $handle     Handle of the notice to remove.
	 * @param   array   $params     NOT USED BY THIS STORE.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function remove_notice( string $handle, array $params = array() ): bool {
		$notices = $this->get_notices();

		if ( isset( $notices[ $handle ] ) ) {
			unset( $notices[ $handle ] );

			return empty( $notices )
				? delete_option( $this->generate_notices_option_key() )
				: update_option(
					$this->generate_notices_option_key(),
					$notices
				);
		}

		return false;
	}

	/**
	 * Returns the number of notices in the store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   array   $params     NOT USED BY THIS STORE.
	 *
	 * @return  int
	 */
	public function count_notices( array $params = array() ): int {
		return count( $this->get_notices() );
	}

	// endregion

	// region HELPERS

	/**
	 * Generates the key under which the notices are stored in the options table.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	protected function generate_notices_option_key(): string {
		return sanitize_key( '_dws_admin_notices_' . $this->get_plugin()->get_plugin_safe_slug() );
	}

	// endregion
}
