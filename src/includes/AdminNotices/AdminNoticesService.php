<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices;

use DeepWebSolutions\Framework\Foundations\Actions\Outputtable\OutputFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Outputtable\OutputtableTrait;
use DeepWebSolutions\Framework\Foundations\Actions\OutputtableInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\Handlers\DismissibleNoticesHandler;
use DeepWebSolutions\Framework\Utilities\AdminNotices\Handlers\NoticesHandler;
use DeepWebSolutions\Framework\Utilities\AdminNotices\Stores\DynamicStoreAdmin;
use DeepWebSolutions\Framework\Utilities\AdminNotices\Stores\OptionsStoreAdmin;
use DeepWebSolutions\Framework\Utilities\AdminNotices\Stores\UserMetaStoreAdmin;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceAwareTrait;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterTrait;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingService;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareTrait;
use Psr\Log\LogLevel;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for admin notices.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices
 */
class AdminNoticesService implements AdminNoticesStoresContainerAwareInterface, HooksServiceAwareInterface, LoggingServiceAwareInterface, PluginAwareInterface, OutputtableInterface {
	// region TRAITS

	use AdminNoticesStoresContainerAwareTrait;
	use HooksServiceAwareTrait;
	use HooksServiceRegisterTrait;
	use LoggingServiceAwareTrait;
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
	 * @param   PluginInterface                 $plugin             Instance of the plugin.
	 * @param   LoggingService                  $logging_service    Instance of the logging service.
	 * @param   AdminNoticesStoresContainer     $store_container    Instance of the admin notices store factory.
	 * @param   HooksService                    $hooks_service      Instance of the hooks service.
	 * @param   AdminNoticesHandlerInterface[]  $handlers           Admin notices handlers to output.
	 */
	public function __construct( PluginInterface $plugin, LoggingService $logging_service, AdminNoticesStoresContainer $store_container, HooksService $hooks_service, array $handlers = array() ) {
		$this->set_plugin( $plugin );
		$this->set_logging_service( $logging_service );
		$this->set_admin_notices_store_factory( $store_container );

		$this->set_hooks_service( $hooks_service );
		$this->register_hooks( $hooks_service );

		$this->set_default_stores( $store_container );
		$this->set_default_handlers( $handlers );
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
	 * @return  AdminNoticesService
	 */
	public function set_handlers( array $handlers ): AdminNoticesService {
		$this->handlers = array();

		foreach ( $handlers as $handler ) {
			if ( $handler instanceof AdminNoticesHandlerInterface ) {
				$this->register_handler( $handler );
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
			/* @noinspection PhpIncompatibleReturnTypeInspection */
			return $this->log_event_and_doing_it_wrong_and_return_exception(
				__FUNCTION__,
				'The admin notices service has already been outputted.',
				'1.0.0',
				OutputFailureException::class,
				null,
				LogLevel::NOTICE,
				'framework'
			);
		}

		if ( $this->output_result instanceof OutputFailureException ) {
			$this->log_event( LogLevel::ERROR, $this->output_result->getMessage(), 'framework' );
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
	 * @return  AdminNoticesService
	 */
	public function register_handler( AdminNoticesHandlerInterface $handler ): AdminNoticesService {
		if ( $handler instanceof PluginAwareInterface ) {
			$handler->set_plugin( $this->get_plugin() );
		}
		if ( $handler instanceof LoggingServiceAwareInterface ) {
			$handler->set_logging_service( $this->get_logging_service() );
		}
		if ( $handler instanceof AdminNoticesStoresContainerAwareInterface ) {
			$handler->set_admin_notices_store_factory( $this->get_admin_notices_store_factory() );
		}
		if ( $handler instanceof HooksServiceRegisterInterface ) {
			$handler->register_hooks( $this->get_hooks_service() );
		}

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
		return $this->get_admin_notices_store( $store )->add_notice( $notice, $params );
	}

	/**
	 * Retrieves a notice from the given store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     Handle of the notice to retrieve.
	 * @param   string  $store      Name of the store to add the notice to.
	 * @param   array   $params     Any parameters required to insert the notice into the store.
	 *
	 * @return  AdminNoticeInterface|null
	 */
	public function get_notice( string $handle, string $store = 'dynamic', array $params = array() ): ?AdminNoticeInterface {
		return $this->get_admin_notices_store( $store )->get_notice( $handle, $params );
	}

	/**
	 * Updates (or adds if it doesn't exist) a notice to the given store.
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
	public function update_notice( AdminNoticeInterface $notice, string $store = 'dynamic', array $params = array() ): bool {
		return $this->get_admin_notices_store( $store )->update_notice( $notice, $params );
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

	// endregion

	// region HELPERS

	/**
	 * Register the handlers passed on in the constructor together with the default handlers.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $handlers   Handlers passed on in the constructor.
	 */
	protected function set_default_handlers( array $handlers ): void {
		$plugin = $this->get_plugin();
		if ( $plugin instanceof ContainerAwareInterface ) {
			$container = $plugin->get_container();
			$handlers += array( $container->get( NoticesHandler::class ), $container->get( DismissibleNoticesHandler::class ) );
		} else {
			$handlers += array( new NoticesHandler(), new DismissibleNoticesHandler() );
		}

		$this->set_handlers( $handlers );
	}

	/**
	 * Register the stores supported by default with the store container.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesStoresContainer     $store_factory  Instance of the store factory.
	 */
	protected function set_default_stores( AdminNoticesStoresContainer $store_factory ): void {
		$stores = array(
			'dynamic'   => new DynamicStoreAdmin(),
			'options'   => new OptionsStoreAdmin( '_dws_admin_notices_' . $this->get_plugin()->get_plugin_safe_slug() ),
			'user-meta' => new UserMetaStoreAdmin( '_dws_admin_notices_' . $this->get_plugin()->get_plugin_safe_slug() ),
		);

		foreach ( $stores as $name => $store ) {
			$store_factory->register_store( $name, $store );
		}
	}

	// endregion
}
