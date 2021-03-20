<?php

namespace DeepWebSolutions\Framework\Utilities\CronEvents;

use DeepWebSolutions\Framework\Foundations\Actions\ResettableInterface;
use DeepWebSolutions\Framework\Foundations\Actions\RunnableInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Describes a handler of CRON events.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\CronEvents
 */
interface CronEventsHandlerInterface extends RunnableInterface, ResettableInterface {
	/**
	 * Returns the handler's type.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_type(): string;

	/**
	 * Schedules a new single cron event.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $hook           Hook to call on cron event.
	 * @param   int         $timestamp      UNIX timestamp of when the event should take place.
	 * @param   array       $args           Arguments to pass on to the event.
	 */
	public function schedule_single_event( string $hook, int $timestamp, array $args ): void;

	/**
	 * Removes a scheduled single cron event.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $hook           The hook which the event calls.
	 * @param   int         $timestamp      The timestamp at which the event should be executed.
	 * @param   array       $args           Arguments to pass on to the event.
	 */
	public function unschedule_single_event( string $hook, int $timestamp, array $args ): void;

	/**
	 * Schedules a new recurring cron event.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $hook           Hook to call on cron event.
	 * @param   mixed       $recurrence     Event recurrence in the format required by the handler.
	 * @param   int         $timestamp      UNIX timestamp of when the first event should take place.
	 * @param   array       $args           Arguments to pass on to the event.
	 */
	public function schedule_recurring_event( string $hook, $recurrence, int $timestamp, array $args ): void;

	/**
	 * Removes a scheduled recurring cron event.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $hook       The hook which the event calls.
	 * @param   int         $timestamp  The timestamp at which the event should be next executed.
	 * @param   array       $args           Arguments to pass on to the event.
	 */
	public function unschedule_recurring_event( string $hook, int $timestamp, array $args ): void;
}
