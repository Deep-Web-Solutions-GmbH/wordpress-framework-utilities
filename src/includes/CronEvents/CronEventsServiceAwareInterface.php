<?php

namespace DeepWebSolutions\Framework\Utilities\CronEvents;

/**
 * Describes a cron-events-service-aware instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\CronEvents
 */
interface CronEventsServiceAwareInterface {
	/**
	 * Gets the current cron events service instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  CronEventsService
	 */
	public function get_cron_events_service(): CronEventsService;

	/**
	 * Sets a cron events service instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   CronEventsService       $service        Cron events service instance to use from now on.
	 *
	 * @return  mixed
	 */
	public function set_cron_events_service( CronEventsService $service );
}
