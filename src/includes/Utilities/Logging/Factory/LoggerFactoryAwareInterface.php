<?php

namespace DeepWebSolutions\Framework\Utilities\Logging\Factory;

use DeepWebSolutions\Framework\Utilities\Logging\LoggerFactory;

/**
 * Describes a logger-factory-aware instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Logging\Factory
 */
interface LoggerFactoryAwareInterface {
	/**
	 * Gets the current logger factory instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  LoggerFactory
	 */
	public function get_logger_factory(): LoggerFactory;

	/**
	 * Sets a logger factory instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   LoggerFactory     $logger_factory       Logger factory instance to use from now on.
	 */
	public function set_logger_factory( LoggerFactory $logger_factory ): void;
}
