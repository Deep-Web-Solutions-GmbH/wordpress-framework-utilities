<?php

namespace DeepWebSolutions\Framework\Utilities\Logging;

use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

defined( 'ABSPATH' ) || exit;

/**
 * Logs messages at all PSR-3 levels. GDPR-appropriate + full logger choice flexibility.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Logging
 */
class LoggingService implements PluginAwareInterface {
	// region TRAITS

	use PluginAwareTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * Loggers that can be used.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     LoggerInterface[]
	 */
	protected array $loggers;

	/**
	 * Whether to include sensitive information in the logs or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     bool
	 */
	protected bool $include_sensitive;

	// endregion

	// region INHERITED FUNCTIONS

	/**
	 * LoggingService constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   PluginInterface     $plugin             Instance of the plugin.
	 * @param   LoggerInterface[]   $loggers            Collection of PSR-3 loggers that can be used.
	 * @param   bool                $include_sensitive  Whether the logs should include sensitive information or not.
	 */
	public function __construct( PluginInterface $plugin, array $loggers = array(), bool $include_sensitive = false ) {
		$this->set_plugin( $plugin );

		$this->set_default_loggers( $loggers );
		$this->include_sensitive = $include_sensitive;
	}

	// endregion

	// region GETTERS

	/**
	 * Returns the list of handlers registered to run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  LoggerInterface[]
	 */
	public function get_loggers(): array {
		return $this->loggers;
	}

	/**
	 * Gets whether the logs will include any sensitive information or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function includes_sensitive_messages(): bool {
		return $this->include_sensitive;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets the list of loggers that can be used.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $loggers    Collection of loggers.
	 *
	 * @return  $this
	 */
	public function set_loggers( array $loggers ): LoggingService {
		$this->loggers = array();

		foreach ( $loggers as $key => $logger ) {
			if ( $logger instanceof LoggerInterface && is_string( $key ) ) {
				$this->register_logger( $key, $logger );
			}
		}

		return $this;
	}

	// endregion

	// region METHODS

	/**
	 * Adds a logger to the list of usable loggers.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string              $name       Internal name of the logger.
	 * @param   LoggerInterface     $logger     Logger to add.
	 *
	 * @return  $this
	 */
	public function register_logger( string $name, LoggerInterface $logger ): LoggingService {
		$this->loggers[ $name ] = $logger;
		return $this;
	}

	/**
	 * Returns a given logger from the list of registered ones.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $name       Unique name of the logger to retrieve.
	 *
	 * @return  LoggerInterface
	 */
	public function get_logger( string $name ): LoggerInterface {
		return $this->get_loggers()[ $name ] ?? $this->get_loggers()['null'];
	}

	/**
	 * Logs an event with the given logger.
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
		$logger = $this->get_logger( $logger );
		if ( ! $is_sensitive || $this->includes_sensitive_messages() ) {
			$logger->log( $log_level, $message, $context );
		}
	}

	/**
	 * Logs an event with an appropriate level and also runs a '_doing_it_wrong' call with the same message.
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
		$this->log_event( $log_level, $message, $logger, $is_sensitive, $context );
		_doing_it_wrong( $function, $message, $since_version ); // phpcs:ignore
	}

	/**
	 * Logs an event with an appropriate level and returns an exception with the same message.
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
		$this->log_event( $log_level, $message, $logger, $is_sensitive, $context );
		return new $exception( $message, $original_exception ? $original_exception->getCode() : 0, $original_exception );
	}

	/**
	 * Logs an event with an appropriate level, runs a '_doing_it_wrong' call, and returns an exception with the same message.
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
		$this->log_event_and_doing_it_wrong( $function, $message, $since_version, $log_level, $logger, $is_sensitive, $context );
		return new $exception( $message, $original_exception ? $original_exception->getCode() : 0, $original_exception );
	}

	// endregion

	// region HELPERS

	/**
	 * Register the loggers passed on in the constructor together with the default loggers.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $loggers    Loggers passed on in the constructor.
	 */
	protected function set_default_loggers( array $loggers ) {
		$plugin = $this->get_plugin();
		if ( $plugin instanceof ContainerAwareInterface ) {
			$container = $plugin->get_container();
			$loggers  += array( 'null' => $container->get( NullLogger::class ) );
		} else {
			$loggers += array( 'null' => new NullLogger() );
		}

		$this->set_loggers( $loggers );
	}

	// endregion
}
