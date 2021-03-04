<?php

namespace DeepWebSolutions\Framework\Utilities\CronEvents;

use DeepWebSolutions\Framework\Foundations\Helpers\HooksHelpersTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the cron events service register interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\CronEvents
 */
trait CronEventsServiceRegisterTrait {
	// region TRAITS

	use HooksHelpersTrait;

	// endregion

	// region METHODS

	/**
	 * Using classes should define their cron events in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   CronEventsService       $cron_service       Instance of the cron events service.
	 */
	abstract public function register_cron_events( CronEventsService $cron_service ): void;

	// endregion
}
