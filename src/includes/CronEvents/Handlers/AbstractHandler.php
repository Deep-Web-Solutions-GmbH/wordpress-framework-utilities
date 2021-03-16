<?php

namespace DeepWebSolutions\Framework\Utilities\CronEvents\Handlers;

use DeepWebSolutions\Framework\Foundations\Actions\Resettable\ResettableTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunnableTrait;
use DeepWebSolutions\Framework\Utilities\CronEvents\CronEventsHandlerInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Encapsulates the most often needed functionality of a cron events handler.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package DeepWebSolutions\WP-Framework\Utilities\CronEvents\Handlers
 */
abstract class AbstractHandler implements CronEventsHandlerInterface {
	// region TRAITS

	use ResettableTrait;
	use RunnableTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * The single cron events to register after the handler runs.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array
	 */
	protected array $single_events = array();

	/**
	 * The recurring cron events to register after the handler runs.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array
	 */
	protected array $recurring_events = array();

	// endregion

	// region METHODS

	/**
	 * Add a new cron event to the collection of single events to be registered.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $hook           Hook to call on cron event.
	 * @param   int|null    $timestamp      UNIX timestamp of when the event should take place. Defaults to next midnight.
	 * @param   array       $args           Arguments to pass on to the event.
	 */
	public function schedule_single_event( string $hook, int $timestamp, array $args = array() ): void {
		$this->single_events[] = array(
			'hook'      => $hook,
			'timestamp' => $timestamp,
			'args'      => $args,
		);
	}

	/**
	 * Removes a cron event from the collection of single events to be registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $hook           The hook which the event calls.
	 * @param   int         $timestamp      The timestamp at which the event should be executed.
	 * @param   array       $args           The arguments passed to the hook to be removed.
	 */
	public function unschedule_single_event( string $hook, int $timestamp, array $args = array() ): void {
		$this->single_events = $this->remove( $this->single_events, $hook, $timestamp, $args );
	}

	/**
	 * Add a new cron event to the collection of recurring events to be registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $hook           Hook to call on cron event.
	 * @param   mixed       $recurrence     Slug of the cron interval to attach the event to.
	 * @param   int|null    $timestamp      UNIX timestamp of when the first event should take place. Defaults to next midnight.
	 * @param   array       $args           Arguments to pass on to the event.
	 */
	public function schedule_recurring_event( string $hook, $recurrence, int $timestamp, array $args = array() ): void {
		$this->recurring_events[] = array(
			'hook'       => $hook,
			'recurrence' => $recurrence,
			'timestamp'  => $timestamp,
			'args'       => $args,
		);
	}

	/**
	 * Removes a cron event from the collection of recurring events to be registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $hook       The hook which the event calls.
	 * @param   int         $timestamp  The timestamp at which the event should be next executed.
	 * @param   array       $args       The arguments passed to the hook to be removed.
	 */
	public function unschedule_recurring_event( string $hook, int $timestamp, array $args = array() ): void {
		$this->recurring_events = $this->remove( $this->recurring_events, $hook, $timestamp, $args );
	}

	// endregion

	// region HELPERS

	/**
	 * Removes an event from a given events collection.
	 *
	 * @param   array       $events         The collection of cron events to manipulate.
	 * @param   string      $hook           The hook on which the event is to be called.
	 * @param   int         $timestamp      The timestamp at which the event should be next executed.
	 * @param   array       $args           Arguments to pass on to the event.
	 *
	 * @return  array
	 */
	protected function remove( array $events, string $hook, int $timestamp, array $args ): array {
		foreach ( $events as $key => $event ) {
			if ( $event['hook'] === $hook && $event['timestamp'] === $timestamp && md5( serialize( $args ) ) === md5( serialize( $event['args'] ) ) ) { // phpcs:ignore
				unset( $events[ $key ] );
				break;
			}
		}

		return $events;
	}

	// endregion
}
