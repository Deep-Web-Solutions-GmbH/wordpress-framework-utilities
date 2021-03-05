<?php

namespace DeepWebSolutions\Framework\Utilities\CronEvents;

defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the cron-events-service-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\CronEvents
 */
trait CronEventsServiceAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * CRON events service instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     CronEventsService
	 */
	protected CronEventsService $cron_events_service;

	// endregion

	// region GETTERS

	/**
	 * Gets the current cron events service instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  CronEventsService
	 */
	public function get_cron_events_service(): CronEventsService {
		return $this->cron_events_service;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets a cron events service instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   CronEventsService       $service        Cron events service instance to use from now on.
	 */
	public function set_cron_events_service( CronEventsService $service ) {
		$this->cron_events_service = $service;
	}

	// endregion

	// region METHODS

	/**
	 * Wrapper around the service's own method.
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
		return $this->get_cron_events_service()->schedule_single_event( $handler, $hook, $timestamp, $args );
	}

	/**
	 * Wrapper around the service's own method.
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
		return $this->get_cron_events_service()->schedule_recurring_event( $handler, $hook, $recurrence, $timestamp, $args );
	}

	// endregion
}
