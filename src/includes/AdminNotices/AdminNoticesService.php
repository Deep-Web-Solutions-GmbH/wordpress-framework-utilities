<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices;

use DeepWebSolutions\Framework\Foundations\Actions\Outputtable\OutputFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Outputtable\OutputtableTrait;
use DeepWebSolutions\Framework\Foundations\Actions\OutputtableInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for admin notices.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices
 */
class AdminNoticesService implements AdminNoticesStoreFactoryAwareInterface, PluginAwareInterface, OutputtableInterface {
	// region TRAITS

	use AdminNoticesStoreFactoryAwareTrait;
	use HooksServiceRegisterTrait;
	use PluginAwareTrait;
	use OutputtableTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * Admin notices handlers to output.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     AdminNoticesHandlerInterface[]
	 */
	protected array $handlers;

	// endregion

	// region MAGIC METHODS

	/**
	 * AdminNoticesService constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   PluginInterface                     $plugin             Instance of the plugin.
	 * @param   AdminNoticesStoreFactory            $store_factory      Instance of the admin notices store factory.
	 * @param   HooksService                        $hooks_service      Instance of the hooks service.
	 * @param   AdminNoticesHandlerInterface[]      $handlers           Admin notices handlers to output.
	 */
	public function __construct( PluginInterface $plugin, AdminNoticesStoreFactory $store_factory, HooksService $hooks_service, array $handlers = array() ) {
		$this->set_plugin( $plugin );
		$this->set_admin_notices_store_factory( $store_factory );

		$this->register_hooks( $hooks_service );
		$this->set_handlers( $handlers );
	}

	// endregion

	// region GETTERS

	/**
	 * Returns the list of handlers registered to output.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  AdminNoticesHandlerInterface[]
	 */
	public function get_handlers(): array {
		return $this->handlers;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets the list of handlers to output.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $handlers   Collection of handlers to output.
	 *
	 * @return  $this
	 */
	public function set_handlers( array $handlers ): AdminNoticesService {
		$this->handlers = array();

		foreach ( $handlers as $handler ) {
			if ( $handler instanceof AdminNoticesHandlerInterface ) {
				if ( $handler instanceof PluginAwareInterface ) {
					$handler->set_plugin( $this->get_plugin() );
				}
				$this->handlers[ $handler->get_notices_type() ] = $handler;
			}
		}

		return $this;
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * Registers hooks with the hooks service.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   HooksService    $hooks_service      Instance of the hooks service.
	 */
	public function register_hooks( HooksService $hooks_service ): void {
		$hooks_service->add_action( 'admin_notices', $this, 'output', PHP_INT_MAX );
	}

	/**
	 * Output the registered admin notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  OutputFailureException|null
	 */
	public function output(): ?OutputFailureException {
		if ( is_null( $this->is_outputted ) ) {
			$this->output_result = null;

			foreach ( $this->get_handlers() as $handler ) {
				$result = $handler->output();
				if ( ! is_null( $result ) ) {
					$this->output_result = $result;
					break;
				}
			}

			$this->is_outputted = is_null( $this->output_result );
		} else {
			return new OutputFailureException( 'The admin notices service has already been outputted.' );
		}

		return $this->output_result;
	}

	// endregion

	// region METHODS

	/**
	 * Adds a handler to the list of handlers to output.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesHandlerInterface    $handler    Handler to add.
	 *
	 * @return  $this
	 */
	public function register_handler( AdminNoticesHandlerInterface $handler ): AdminNoticesService {
		$this->handlers[ $handler->get_notices_type() ] = $handler;
		return $this;
	}

	/**
	 * Returns the handler for a specific type of notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $notice_type    Class name of an AdminNoticeInterface implementation.
	 *
	 * @return  AdminNoticesHandlerInterface|null
	 */
	public function get_handler( string $notice_type ): ?AdminNoticesHandlerInterface {
		return $this->handlers[ $notice_type ] ?? null;
	}

	/**
	 * Adds a notice notices to a given store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticeInterface    $notice     Notice to add to the store.
	 * @param   string                  $store      Name of the store to add the notice to.
	 * @param   array                   $params     Any parameters required to insert the notice into the store.
	 *
	 * @return  bool
	 */
	public function add_notice( AdminNoticeInterface $notice, string $store = 'dynamic', array $params = array() ): bool {
		return $this->get_admin_notices_store( $store )->add_notice( $params, $notice );
	}

	/**
	 * Removes a notice from a given store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle         Handle of the notice to remove.
	 * @param   string  $store          The name of the store to remove the notice from.
	 * @param   array   $params         Any parameters needed to remove the notice.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function remove_notice( string $handle, string $store = 'dynamic', array $params = array() ): bool {
		return $this->get_admin_notices_store( $store )->remove_notice( $handle, $params );
	}

	/**
	 * Returns the number of notices in a given store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $store      The name of the store from which to count the notices.
	 * @param   array   $params     Any parameters needed to count the notices.
	 *
	 * @return  int|null
	 */
	public function count_notices( string $store = 'dynamic', array $params = array() ): ?int {
		return $this->get_admin_notices_store( $store )->count_notices( $params );
	}

	// endregion
}
