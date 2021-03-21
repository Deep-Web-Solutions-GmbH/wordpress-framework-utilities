<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices;

use DeepWebSolutions\Framework\Foundations\Actions\OutputtableInterface;
use DeepWebSolutions\Framework\Foundations\Logging\LoggingService;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Foundations\Utilities\Handlers\HandlerInterface;
use DeepWebSolutions\Framework\Foundations\Utilities\Services\AbstractMultiHandlerService;
use DeepWebSolutions\Framework\Foundations\Utilities\Services\Actions\OutputtableHandlerServiceTrait;
use DeepWebSolutions\Framework\Foundations\Utilities\Storage\StoreInterface;
use DeepWebSolutions\Framework\Foundations\Utilities\Storage\Stores\MemoryStore;
use DeepWebSolutions\Framework\Foundations\Utilities\Storage\Stores\OptionsStore;
use DeepWebSolutions\Framework\Foundations\Utilities\Storage\Stores\UserMetaStore;
use DeepWebSolutions\Framework\Utilities\AdminNotices\Handlers\DismissibleNoticesHandler;
use DeepWebSolutions\Framework\Utilities\AdminNotices\Handlers\NoticesHandler;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceAwareTrait;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for admin notices.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices
 */
class AdminNoticesService extends AbstractMultiHandlerService implements HooksServiceAwareInterface, OutputtableInterface {
	// region TRAITS

	use HooksServiceAwareTrait;
	use HooksServiceRegisterTrait;
	use OutputtableHandlerServiceTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * Instance of the admin notices stores store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     MemoryStore
	 */
	protected MemoryStore $admin_notices_stores;

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
	 * @param   HooksService                    $hooks_service      Instance of the hooks service.
	 * @param   StoreInterface[]                $stores             Stores containing admin notices.
	 * @param   AdminNoticesHandlerInterface[]  $handlers           Admin notices handlers to output.
	 */
	public function __construct( PluginInterface $plugin, LoggingService $logging_service, HooksService $hooks_service, array $stores = array(), array $handlers = array() ) {
		$this->set_hooks_service( $hooks_service );
		parent::__construct( $plugin, $logging_service, $handlers );

		$this->set_default_stores( $stores );
		$this->register_hooks( $hooks_service );
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * Registers a new handler with the service.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   HandlerInterface    $handler    The new handler to register with the service.
	 *
	 * @return  AdminNoticesService
	 */
	public function register_handler( HandlerInterface $handler ): AdminNoticesService {
		parent::register_handler( $handler );

		if ( $handler instanceof AdminNoticesHandlerInterface ) {
			$handler->set_store( $this->admin_notices_stores );
		}
		if ( $handler instanceof HooksServiceRegisterInterface ) {
			$handler->register_hooks( $this->get_hooks_service() );
		}

		return $this;
	}

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

	// endregion

	// region METHODS

	/**
	 * Returns a given admin notices store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $store_id   The ID of the admin notices store to retrieve.
	 *
	 * @return  StoreInterface
	 */
	public function get_admin_notices_store( string $store_id ): StoreInterface {
		/* @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->admin_notices_stores->get( $store_id );
	}

	/**
	 * Adds a notice notices to a given store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticeInterface    $notice     Notice to add to the store.
	 * @param   string                  $store      Name of the store to add the notice to.
	 *
	 * @return  bool
	 */
	public function add_notice( AdminNoticeInterface $notice, string $store = 'dynamic' ): bool {
		try {
			$result = $this->get_admin_notices_store( $store )->add( $notice );
			return \is_null( $result ) || \boolval( $result );
		} catch ( ContainerExceptionInterface $exception ) {
			return false;
		}
	}

	/**
	 * Retrieves a notice from the given store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle     Handle of the notice to retrieve.
	 * @param   string  $store      Name of the store to add the notice to.
	 *
	 * @return  AdminNoticeInterface|null
	 */
	public function get_notice( string $handle, string $store = 'dynamic' ): ?AdminNoticeInterface {
		try {
			return $this->get_admin_notices_store( $store )->get( $handle );
		} catch ( ContainerExceptionInterface $exception ) {
			return null;
		}
	}

	/**
	 * Updates (or adds if it doesn't exist) a notice to the given store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticeInterface    $notice     Notice to add to the store.
	 * @param   string                  $store      Name of the store to add the notice to.
	 *
	 * @return  bool
	 */
	public function update_notice( AdminNoticeInterface $notice, string $store = 'dynamic' ): bool {
		try {
			$result = $this->get_admin_notices_store( $store )->update( $notice );
			return \is_null( $result ) || \boolval( $result );
		} catch ( ContainerExceptionInterface $exception ) {
			return false;
		}
	}

	/**
	 * Removes a notice from a given store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handle         Handle of the notice to remove.
	 * @param   string  $store          The name of the store to remove the notice from.
	 *
	 * @return  bool    Whether the operation was successful or not.
	 */
	public function remove_notice( string $handle, string $store = 'dynamic' ): bool {
		try {
			$result = $this->get_admin_notices_store( $store )->remove( $handle );
			return \is_null( $result ) || \boolval( $result );
		} catch ( NotFoundExceptionInterface $exception ) {
			return true;
		} catch ( ContainerExceptionInterface $exception ) {
			return false;
		}
	}

	// endregion

	// region HELPERS

	/**
	 * Register the stores passed on in the constructor together with the default stores.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   StoreInterface[]    $stores     Custom stores passed on through the constructor.
	 */
	protected function set_default_stores( array $stores ): void {
		$database_key   = '_dws_admin_notices_' . $this->get_plugin()->get_plugin_safe_slug();
		$default_stores = array(
			new MemoryStore( 'dynamic' ),
			new OptionsStore( 'options', $database_key ),
			new UserMetaStore( 'user-meta', $database_key ),
		);

		foreach ( array_merge( $default_stores, $stores ) as $store ) {
			$this->admin_notices_stores->update( $store );
		}
	}

	/**
	 * Returns a list of what the default handlers actually are for the inheriting service.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	protected function get_default_handlers_classes(): array {
		return array( NoticesHandler::class, DismissibleNoticesHandler::class );
	}

	/**
	 * Returns the class name of the used handler for better type-checking.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	protected function get_handler_class(): string {
		return AdminNoticesHandlerInterface::class;
	}

	// endregion
}
