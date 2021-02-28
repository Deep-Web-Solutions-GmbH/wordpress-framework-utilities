<?php

namespace DeepWebSolutions\Framework\Utilities\Logging\Service;

use DeepWebSolutions\Framework\Utilities\Logging\LoggingService;

/**
 * Describes a logging-service-aware instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Logging\Service
 */
interface LoggingServiceAwareInterface {
	/**
	 * Gets the current logging service instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  LoggingService
	 */
	public function get_logging_service(): LoggingService;

	/**
	 * Sets a logging service instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   LoggingService      $logging_service    Logging service instance to use from now on.
	 */
	public function set_logging_service( LoggingService $logging_service ): void;
}
