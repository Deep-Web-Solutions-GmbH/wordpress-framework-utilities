<?php

namespace DeepWebSolutions\Framework\Utilities\CronEvents;

use DeepWebSolutions\Framework\Foundations\Actions\ResettableInterface;
use DeepWebSolutions\Framework\Foundations\Actions\RunnableInterface;
use DeepWebSolutions\Framework\Foundations\Logging\LoggingService;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Foundations\Utilities\Handlers\HandlerInterface;
use DeepWebSolutions\Framework\Foundations\Utilities\Services\AbstractMultiHandlerService;
use DeepWebSolutions\Framework\Foundations\Utilities\Services\Actions\ResettableHandlerServiceTrait;
use DeepWebSolutions\Framework\Foundations\Utilities\Services\Actions\RunnableHandlerServiceTrait;
use DeepWebSolutions\Framework\Helpers\WordPress\Misc;
use DeepWebSolutions\Framework\Utilities\CronEvents\Handlers\DefaultCronEventsHandler;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceAwareTrait;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterTrait;

\defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for crons.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\CronEvents
 */
class CronEventsService extends AbstractMultiHandlerService implements HooksServiceAwareInterface, RunnableInterface, ResettableInterface {
	// region TRAITS

	use HooksServiceAwareTrait;
	use HooksServiceRegisterTrait;
	use RunnableHandlerServiceTrait;
	use ResettableHandlerServiceTrait;

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
		$this->set_hooks_service( $hooks_service );
		parent::__construct( $plugin, $logging_service, $handlers );

		$this->register_hooks( $hooks_service );
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * Returns the instance of a given handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handler_id     The ID of the handler to retrieve.
	 *
	 * @return  CronEventsHandlerInterface|null
	 */
	public function get_handler( string $handler_id ): ?CronEventsHandlerInterface { // phpcs:ignore
		/* @noinspection PhpIncompatibleReturnTypeInspection */
		return parent::get_handler( $handler_id );
	}

	/**
	 * Registers a new handler with the service.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   HandlerInterface        $handler    The new handler to register with the service.
	 *
	 * @return  CronEventsService
	 */
	public function register_handler( HandlerInterface $handler ): CronEventsService {
		parent::register_handler( $handler );

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
		$hooks_service->add_action( 'init', $this, 'run', PHP_INT_MAX );
	}

	// endregion

	// region METHODS

	/**
	 * Registers a new cron single event to be scheduled on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $hook           Hook to call on cron event.
	 * @param   int|null    $timestamp      UNIX timestamp of when the event should take place. Defaults to next midnight.
	 * @param   array       $args           Arguments to pass on to the event.
	 * @param   string      $handler_id     The ID of the handler to use.
	 *
	 * @return  bool
	 */
	public function schedule_single_event( string $hook, ?int $timestamp = null, array $args = array(), string $handler_id = 'default' ): bool {
		$timestamp = \is_null( $timestamp ) ? Misc::get_midnight_unix_timestamp() : $timestamp;

		$handler = $this->get_handler( $handler_id );
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
	 * @param   string      $hook           The hook which the event calls.
	 * @param   int         $timestamp      The timestamp at which the event should be executed.
	 * @param   array       $args           The arguments passed to the hook to be removed.
	 * @param   string      $handler_id     The ID of the handler to use.
	 *
	 * @return  bool
	 */
	public function unschedule_single_event( string $hook, int $timestamp, array $args = array(), string $handler_id = 'default' ): bool {
		$handler = $this->get_handler( $handler_id );
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
	 * @param   string      $hook           Hook to call on cron event.
	 * @param   mixed       $recurrence     Recurrence value to pass on to the handler.
	 * @param   int|null    $timestamp      UNIX timestamp of when the first event should take place. Defaults to next midnight.
	 * @param   array       $args           Arguments to pass on to the event.
	 * @param   string      $handler_id     The ID of the handler to use.
	 *
	 * @return  bool
	 */
	public function schedule_recurring_event( string $hook, $recurrence = CronIntervalsEnum::HOURS_24, ?int $timestamp = null, array $args = array(), string $handler_id = 'default' ): bool {
		$timestamp = \is_null( $timestamp ) ? Misc::get_midnight_unix_timestamp() : $timestamp;

		$handler = $this->get_handler( $handler_id );
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
	 * @param   string      $hook           The hook which the event calls.
	 * @param   int         $timestamp      The timestamp at which the event should be next executed.
	 * @param   array       $args           The arguments passed to the hook to be removed.
	 * @param   string      $handler_id     The ID of the handler to use.
	 *
	 * @return  bool
	 */
	public function unschedule_recurring_event( string $hook, int $timestamp, array $args = array(), string $handler_id = 'default' ): bool {
		$handler = $this->get_handler( $handler_id );
		if ( \is_null( $handler ) ) {
			return false;
		}

		$handler->unschedule_recurring_event( $hook, $timestamp, $args );
		return true;
	}

	// endregion

	// region HELPERS

	/**
	 * Returns a list of what the default handlers actually are for the inheriting service.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	protected function get_default_handlers_classes(): array {
		return array( DefaultCronEventsHandler::class );
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
		return CronEventsHandlerInterface::class;
	}

	// endregion
}
