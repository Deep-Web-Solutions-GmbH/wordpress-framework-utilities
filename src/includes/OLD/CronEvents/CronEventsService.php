<?php

namespace DeepWebSolutions\Framework\Utilities\CronEvents;

use DeepWebSolutions\Framework\Foundations\Actions\Resettable\ResetFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Resettable\ResettableTrait;
use DeepWebSolutions\Framework\Foundations\Actions\ResettableInterface;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunnableTrait;
use DeepWebSolutions\Framework\Foundations\Actions\RunnableInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Helpers\WordPress\Misc;
use DeepWebSolutions\Framework\Utilities\CronEvents\Handlers\ActionSchedulerHandler;
use DeepWebSolutions\Framework\Utilities\CronEvents\Handlers\WordPressHandler;
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

\defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for crons.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\CronEvents
 */
class CronEventsService implements HooksServiceAwareInterface, LoggingServiceAwareInterface, PluginAwareInterface, RunnableInterface, ResettableInterface {
	// region TRAITS

	use HooksServiceAwareTrait;
	use HooksServiceRegisterTrait;
	use LoggingServiceAwareTrait;
	use PluginAwareTrait;
	use RunnableTrait;
	use ResettableTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * Cron events handlers to run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     CronEventsHandlerInterface[]
	 */
	protected array $handlers;

	// endregion

	// region MAGIC METHODS

	/**
	 * CronService constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   PluginInterface                 $plugin             Instance of the plugin.
	 * @param   LoggingService                  $logging_service    Instance of the logging service.
	 * @param   HooksService                    $hooks_service      Instance of the hooks service.
	 * @param   CronEventsHandlerInterface[]    $handlers           Cron events handlers to run.
	 */
	public function __construct( PluginInterface $plugin, LoggingService $logging_service, HooksService $hooks_service, array $handlers = array() ) {
		$this->set_plugin( $plugin );
		$this->set_logging_service( $logging_service );

		$this->set_hooks_service( $hooks_service );
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
	 * @return  CronEventsHandlerInterface[]
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
	 * @return  CronEventsService
	 */
	public function set_handlers( array $handlers ): CronEventsService {
		$this->handlers = array();

		foreach ( $handlers as $handler ) {
			if ( $handler instanceof CronEventsHandlerInterface ) {
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
		$hooks_service->add_action( 'init', $this, 'run', PHP_INT_MAX );
	}

	/**
	 * Register the cron events.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RunFailureException|null
	 */
	public function run(): ?RunFailureException {
		if ( \is_null( $this->is_run ) ) {
			$this->run_result = null;

			foreach ( $this->get_handlers() as $handler ) {
				$result = $handler->run();
				if ( ! \is_null( $result ) ) {
					$this->run_result = $result;
					break;
				}
			}

			$this->is_run = \is_null( $this->run_result );
		} else {
			/* @noinspection PhpIncompatibleReturnTypeInspection */
			return $this->log_event_and_doing_it_wrong_and_return_exception(
				__FUNCTION__,
				'The cron events service has already been run.',
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

	/**
	 * Removes the registered cron events.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  ResetFailureException|null
	 */
	public function reset(): ?ResetFailureException {
		if ( \is_null( $this->is_reset ) ) {
			$this->reset_result = null;

			foreach ( $this->get_handlers() as $handler ) {
				$result = $handler->reset();
				if ( ! \is_null( $result ) ) {
					$this->reset_result = $result;
					break;
				}
			}

			$this->is_reset = \is_null( $this->reset_result );
		} else {
			/* @noinspection PhpIncompatibleReturnTypeInspection */
			return $this->log_event_and_doing_it_wrong_and_return_exception(
				__FUNCTION__,
				'The cron events service has already been reset.',
				'1.0.0',
				ResetFailureException::class,
				null,
				LogLevel::NOTICE,
				'framework'
			);
		}

		if ( $this->reset_result instanceof ResetFailureException ) {
			$this->log_event( LogLevel::ERROR, $this->reset_result->getMessage(), 'framework' );
		}

		return $this->reset_result;
	}

	// endregion

	// region METHODS

	/**
	 * Adds a handler to the list of handlers to run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   CronEventsHandlerInterface      $handler    Handler to add.
	 *
	 * @return  CronEventsService
	 */
	public function register_handler( CronEventsHandlerInterface $handler ): CronEventsService {
		if ( $handler instanceof PluginAwareInterface ) {
			$handler->set_plugin( $this->get_plugin() );
		}
		if ( $handler instanceof LoggingServiceAwareInterface ) {
			$handler->set_logging_service( $this->get_logging_service() );
		}
		if ( $handler instanceof HooksServiceRegisterInterface ) {
			$handler->register_hooks( $this->get_hooks_service() );
		}

		$this->handlers[ $handler->get_type() ] = $handler;
		return $this;
	}

	/**
	 * Returns the handler for a specific type of cron events.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $type   Type of cron events.
	 *
	 * @return  CronEventsHandlerInterface|null
	 */
	public function get_handler( string $type ): ?CronEventsHandlerInterface {
		return $this->handlers[ $type ] ?? null;
	}

	/**
	 * Registers a new cron single event to be scheduled on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $handler        Handler to use.
	 * @param   string      $hook           Hook to call on cron event.
	 * @param   int|null    $timestamp      UNIX timestamp of when the event should take place. Defaults to next midnight.
	 * @param   array       $args           Arguments to pass on to the event.
	 *
	 * @return  bool
	 */
	public function schedule_single_event( string $handler, string $hook, ?int $timestamp = null, array $args = array() ): bool {
		$timestamp = \is_null( $timestamp ) ? Misc::get_midnight_unix_timestamp() : $timestamp;

		$handler = $this->get_handler( $handler );
		if ( \is_null( $handler ) ) {
			return false;
		}

		$handler->schedule_single_event( $hook, $timestamp, $args );
		return true;
	}

	/**
	 * Removes a cron single event from being scheduled on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $handler        Handler to use.
	 * @param   string      $hook           The hook which the event calls.
	 * @param   int         $timestamp      The timestamp at which the event should be executed.
	 * @param   array       $args           The arguments passed to the hook to be removed.
	 *
	 * @return  bool
	 */
	public function unschedule_single_event( string $handler, string $hook, int $timestamp, array $args = array() ): bool {
		$handler = $this->get_handler( $handler );
		if ( \is_null( $handler ) ) {
			return false;
		}

		$handler->unschedule_single_event( $hook, $timestamp, $args );
		return true;
	}

	/**
	 * Registers a new cron recurring event to be schedule on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $handler        Handler to use.
	 * @param   string      $hook           Hook to call on cron event.
	 * @param   mixed       $recurrence     Recurrence value to pass on to the handler.
	 * @param   int|null    $timestamp      UNIX timestamp of when the first event should take place. Defaults to next midnight.
	 * @param   array       $args           Arguments to pass on to the event.
	 *
	 * @return  bool
	 */
	public function schedule_recurring_event( string $handler, string $hook, $recurrence = CronIntervalsEnum::HOURS_24, ?int $timestamp = null, array $args = array() ): bool {
		$timestamp = \is_null( $timestamp ) ? Misc::get_midnight_unix_timestamp() : $timestamp;

		$handler = $this->get_handler( $handler );
		if ( \is_null( $handler ) ) {
			return false;
		}

		$handler->schedule_recurring_event( $hook, $recurrence, $timestamp, $args );
		return true;
	}

	/**
	 * Removes a cron recurring event from being scheduled on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $handler        Handler to use.
	 * @param   string      $hook           The hook which the event calls.
	 * @param   int         $timestamp      The timestamp at which the event should be next executed.
	 * @param   array       $args           The arguments passed to the hook to be removed.
	 *
	 * @return  bool
	 */
	public function unschedule_recurring_event( string $handler, string $hook, int $timestamp, array $args = array() ): bool {
		$handler = $this->get_handler( $handler );
		if ( \is_null( $handler ) ) {
			return false;
		}

		$handler->unschedule_recurring_event( $hook, $timestamp, $args );
		return true;
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
			$handlers += array( $container->get( WordPressHandler::class ), $container->get( ActionSchedulerHandler::class ) );
		} else {
			$handlers += array( new WordPressHandler(), new ActionSchedulerHandler() );
		}

		$this->set_handlers( $handlers );
	}

	// endregion
}
