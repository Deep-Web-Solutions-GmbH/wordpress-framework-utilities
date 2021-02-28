<?php

namespace DeepWebSolutions\Framework\Utilities\Logging\Service;

use DeepWebSolutions\Framework\Utilities\Logging\LoggingService;
use Exception;
use Psr\Log\LogLevel;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for working with the logging service.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Logging\Service
 */
trait LoggingServiceAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Logging service for handling logging.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     LoggingService
	 */
	protected LoggingService $logging_service;

	// endregion

	// region GETTERS

	/**
	 * Gets the logging service instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  LoggingService
	 */
	public function get_logging_service(): LoggingService {
		return $this->logging_service;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets the logging service instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   LoggingService      $logging_service     The logging service instance to use from now on.
	 */
	public function set_logging_service( LoggingService $logging_service ): void {
		$this->logging_service = $logging_service;
	}

	// endregion

	// region METHODS

	/**
	 * Wrapper around the service's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   string  $log_level      The level of the log message.
	 * @param   string  $message        The log message.
	 * @param   string  $logger         The logger to log the event with.
	 * @param   bool    $is_sensitive   Whether the log may contain any GDPR-sensitive information.
	 * @param   array   $context        The context to pass along to the logger.
	 */
	public function log_event( string $log_level, string $message, string $logger = 'plugin', bool $is_sensitive = false, array $context = array() ): void {
		$this->get_logging_service()->log_event( $log_level, $message, $logger, $is_sensitive, $context );
	}

	/**
	 * Wrapper around the service's own method.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   string  $function       The function being used incorrectly.
	 * @param   string  $message        The message to log/return as exception.
	 * @param   string  $since_version  The plugin version that introduced this warning message.
	 * @param   string  $log_level      The PSR3 log level.
	 * @param   string  $logger         The logger to log the event with.
	 * @param   bool    $is_sensitive   Whether the log may contain any GDPR-sensitive information.
	 * @param   array   $context        The PSR3 context.
	 */
	public function log_event_and_doing_it_wrong( string $function, string $message, string $since_version, string $log_level = LogLevel::DEBUG, string $logger = 'plugin', bool $is_sensitive = false, array $context = array() ): void {
		$this->get_logging_service()->log_event_and_doing_it_wrong( $function, $message, $since_version, $log_level, $logger, $is_sensitive, $context );
	}

	/**
	 * Wrapper around the service's own method.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   string          $log_level              The PSR3 log level.
	 * @param   string          $message                The message to log/return as exception.
	 * @param   string          $exception              The exception to instantiate.
	 * @param   Exception|null  $original_exception     The original exception that was thrown. If not applicable, null.
	 * @param   string          $logger                 The logger to log the event with.
	 * @param   bool            $is_sensitive           Whether the log may contain any GDPR-sensitive information.
	 * @param   array           $context                The PSR3 context.
	 *
	 * @return  Exception
	 */
	public function log_event_and_return_exception( string $log_level, string $message, string $exception, Exception $original_exception = null, string $logger = 'plugin', bool $is_sensitive = false, array $context = array() ): Exception {
		return $this->get_logging_service()->log_event_and_return_exception( $log_level, $message, $exception, $original_exception, $logger, $is_sensitive, $context );
	}

	/**
	 * Wrapper around the service's own method.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   string          $function               The function being used incorrectly.
	 * @param   string          $message                The message to log/return as exception.
	 * @param   string          $since_version          The plugin version that introduced this warning message.
	 * @param   string          $exception              The exception to instantiate.
	 * @param   Exception|null  $original_exception     The original exception that was thrown. If not applicable, null.
	 * @param   string          $log_level              The PSR3 log level.
	 * @param   string          $logger                 The logger to log the event with.
	 * @param   bool            $is_sensitive           Whether the log may contain any GDPR-sensitive information.
	 * @param   array           $context                The PSR3 context.
	 *
	 * @return  Exception
	 */
	public function log_event_and_doing_it_wrong_and_return_exception( string $function, string $message, string $since_version, string $exception, Exception $original_exception = null, string $log_level = LogLevel::DEBUG, string $logger = 'plugin', bool $is_sensitive = false, array $context = array() ): Exception {
		return $this->get_logging_service()->log_event_and_doing_it_wrong_and_return_exception( $function, $message, $since_version, $exception, $original_exception, $log_level, $logger, $is_sensitive, $context );
	}

	// endregion
}
