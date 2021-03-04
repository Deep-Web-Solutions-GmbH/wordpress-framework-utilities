<?php

namespace DeepWebSolutions\Framework\Utilities\Assets;

use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunnableTrait;
use DeepWebSolutions\Framework\Foundations\Actions\RunnableInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Helpers\WordPress\Request;
use DeepWebSolutions\Framework\Helpers\WordPress\RequestTypesEnum;
use DeepWebSolutions\Framework\Utilities\Assets\Handlers\ScriptsHandler;
use DeepWebSolutions\Framework\Utilities\Assets\Handlers\StylesHandler;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterTrait;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingService;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareTrait;
use Psr\Log\LogLevel;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for assets.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Assets
 */
class AssetsService implements LoggingServiceAwareInterface, PluginAwareInterface, RunnableInterface {
	// region TRAITS

	use HooksServiceRegisterTrait;
	use LoggingServiceAwareTrait;
	use PluginAwareTrait;
	use RunnableTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * Assets handlers to run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     AssetsHandlerInterface[]
	 */
	protected array $handlers;

	// endregion

	// region MAGIC METHODS

	/**
	 * AssetsService constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   PluginInterface             $plugin             Instance of the plugin.
	 * @param   LoggingService              $logging_service    Instance of the logging service.
	 * @param   HooksService                $hooks_service      Instance of the hooks service.
	 * @param   AssetsHandlerInterface[]    $handlers           Assets handlers to run.
	 */
	public function __construct( PluginInterface $plugin, LoggingService $logging_service, HooksService $hooks_service, array $handlers = array() ) {
		$this->set_plugin( $plugin );
		$this->set_logging_service( $logging_service );

		$this->register_hooks( $hooks_service );
		$this->set_default_handlers( $handlers );
	}

	// endregion

	// region GETTERS

	/**
	 * Returns the list of handlers registered to run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  AssetsHandlerInterface[]
	 */
	public function get_handlers(): array {
		return $this->handlers;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets the list of handlers to run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $handlers   Collection of handlers to run.
	 *
	 * @return  $this
	 */
	public function set_handlers( array $handlers ): AssetsService {
		$this->handlers = array();

		foreach ( $handlers as $handler ) {
			if ( $handler instanceof AssetsHandlerInterface ) {
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
		$hook = Request::is_type( RequestTypesEnum::FRONTEND_REQUEST ) ? 'wp_enqueue_scripts' : 'admin_enqueue_scripts';
		$hooks_service->add_action( $hook, $this, 'run', PHP_INT_MAX );
	}

	/**
	 * Registers and enqueues the assets with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RunFailureException|null
	 */
	public function run(): ?RunFailureException {
		if ( is_null( $this->is_run ) ) {
			$this->run_result = null;

			foreach ( $this->get_handlers() as $handler ) {
				$result = $handler->run();
				if ( ! is_null( $result ) ) {
					$this->run_result = $result;
					break;
				}
			}

			$this->is_run = is_null( $this->run_result );
		} else {
			/* @noinspection PhpIncompatibleReturnTypeInspection */
			return $this->log_event_and_doing_it_wrong_and_return_exception(
				__FUNCTION__,
				'The assets service has already been run.',
				'1.0.0',
				RunFailureException::class,
				null,
				LogLevel::NOTICE,
				'framework'
			);
		}

		if ( $this->run_result instanceof RunFailureException ) {
			$this->log_event( LogLevel::ERROR, $this->run_result->getMessage(), 'framework' );
		}

		return $this->run_result;
	}

	// endregion

	// region METHODS

	/**
	 * Adds a handler to the list of handlers to run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AssetsHandlerInterface    $handler    Handler to add.
	 *
	 * @return  $this
	 */
	public function register_handler( AssetsHandlerInterface $handler ): AssetsService {
		$this->handlers[ $handler->get_name() ] = $handler;
		return $this;
	}

	/**
	 * Returns a given handler from the list of registered ones.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name   Unique name of the handler to retrieve.
	 *
	 * @return  AssetsHandlerInterface|null
	 */
	public function get_handler( string $name ): ?AssetsHandlerInterface {
		return $this->get_handlers()[ $name ] ?? null;
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
	protected function set_default_handlers( array $handlers ) {
		$plugin = $this->get_plugin();
		if ( $plugin instanceof ContainerAwareInterface ) {
			$container = $plugin->get_container();
			$handlers += array( $container->get( StylesHandler::class ), $container->get( ScriptsHandler::class ) );
		} else {
			$handlers += array( new StylesHandler(), new ScriptsHandler() );
		}

		$this->set_handlers( $handlers );
	}

	// endregion
}
