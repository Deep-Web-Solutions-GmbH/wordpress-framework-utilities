<?php

namespace DeepWebSolutions\Framework\Utilities\PluginComponent;

use DeepWebSolutions\Framework\Foundations\Exceptions\InexistentPropertyException;
use DeepWebSolutions\Framework\Foundations\Exceptions\ReadOnlyPropertyException;
use DeepWebSolutions\Framework\Foundations\PluginComponent\AbstractPluginComponent as FoundationsAbstractPluginComponent;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingService;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareTrait;
use Psr\Log\LogLevel;

defined( 'ABSPATH' ) || exit;

/**
 * Template for encapsulating some of the most often required abilities of a plugin component.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\PluginComponent
 */
abstract class AbstractPluginComponent extends FoundationsAbstractPluginComponent implements LoggingServiceAwareInterface {
	// region TRAITS

	use LoggingServiceAwareTrait;

	// endregion

	// region MAGIC METHODS

	/**
	 * AbstractPluginComponent constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   LoggingService  $logging_service    Instance of the logging service.
	 * @param   string|null     $component_id       Unique ID of the class instance. Must be persistent across requests.
	 * @param   string|null     $component_name     The public name of the using class instance. Must be persistent across requests. Mustn't be unique.
	 */
	public function __construct( LoggingService $logging_service, ?string $component_id = null, ?string $component_name = null ) {
		parent::__construct( $component_id, $component_name );
		$this->set_logging_service( $logging_service );
	}

	/**
	 * Shortcut for auto-magically accessing existing getters.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name   Name of the property that should be retrieved.
	 *
	 * @noinspection PhpMissingReturnTypeInspection
	 * @return  InexistentPropertyException|mixed
	 */
	public function __get( string $name ) {
		$result = parent::__get( $name );

		if ( $result instanceof InexistentPropertyException ) {
			$this->log_event_and_doing_it_wrong(
				__FUNCTION__,
				$result->getMessage(),
				'1.0.0',
				LogLevel::ERROR,
				'framework'
			);
		}

		return $result;
	}

	/**
	 * Used for writing data to existent properties that have a setter defined.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name   The name of the property that should be reassigned.
	 * @param   mixed   $value  The value that should be assigned to the property.
	 *
	 * @throws  InexistentPropertyException  Thrown if there are no getters and no setter for the property, and a global variable also doesn't exist already.
	 * @throws  ReadOnlyPropertyException    Thrown if there is a getter for the property, but no setter.
	 *
	 * @return  mixed
	 */
	public function __set( string $name, $value ) {
		try {
			return parent::__set( $name, $value );
		} catch ( InexistentPropertyException | ReadOnlyPropertyException $exception ) {
			$this->log_event_and_doing_it_wrong(
				__FUNCTION__,
				$exception->getMessage(),
				'1.0.0',
				LogLevel::ERROR,
				'framework'
			);

			throw $exception;
		}
	}

	// endregion
}
