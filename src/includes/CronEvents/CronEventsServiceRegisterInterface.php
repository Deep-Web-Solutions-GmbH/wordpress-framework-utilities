<?php

namespace DeepWebSolutions\Framework\Utilities\CronEvents;

defined( 'ABSPATH' ) || exit;

/**
 * Describes a cron events service utility instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\CronEvents
 */
interface CronEventsServiceRegisterInterface {
	/**
	 * Using classes should define their cron events in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   CronEventsService       $cron_service       Instance of the cron events service.
	 */
	public function register_cron_events( CronEventsService $cron_service ): void;
}
