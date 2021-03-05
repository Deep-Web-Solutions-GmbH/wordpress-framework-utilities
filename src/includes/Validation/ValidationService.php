<?php

namespace DeepWebSolutions\Framework\Utilities\Validation;

use DeepWebSolutions\Framework\Foundations\Exceptions\InexistentPropertyException;
use DeepWebSolutions\Framework\Foundations\Exceptions\NotSupportedException;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;
use DeepWebSolutions\Framework\Helpers\Security\Validation;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareTrait;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingService;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Holds a container of default values and valid options (for values in a collection) and holds validation wrappers
 * against those values.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Validation
 */
class ValidationService implements ContainerAwareInterface, LoggingServiceAwareInterface, PluginAwareInterface {
	// region TRAITS

	use ContainerAwareTrait;
	use LoggingServiceAwareTrait;
	use PluginAwareTrait;

	// endregion

	// region MAGIC METHODS

	/**
	 * ValidationService constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   PluginInterface         $plugin             Instance of the plugin.
	 * @param   LoggingService          $logging_service    Instance of the logging service.
	 * @param   ContainerInterface      $container          Defaults and options container.
	 */
	public function __construct( PluginInterface $plugin, LoggingService $logging_service, ContainerInterface $container ) {
		$this->set_plugin( $plugin );
		$this->set_logging_service( $logging_service );
		$this->set_container( $container );
	}

	// endregion

	// region GETTERS

	/**
	 * Retrieves the default value for a given key.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key    The key inside the container.
	 *
	 * @noinspection PhpMissingReturnTypeInspection
	 * @return  InexistentPropertyException|mixed
	 */
	public function get_default_value( string $key ) {
		return $this->get_container_value( 'defaults/' . $key );
	}

	/**
	 * Retrieves a list of all default values.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_known_default_values(): array {
		return array_keys( $this->get_container_value( 'defaults' ) );
	}

	/**
	 * Retrieves the supported options for a given key.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key    The key inside the container.
	 *
	 * @return  InexistentPropertyException|array
	 */
	public function get_supported_options( string $key ) {
		return $this->get_container_value( 'options/' . $key );
	}

	/**
	 * Retrieves a list of all supported options configurations.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_known_supported_options(): array {
		return array_keys( $this->get_container_value( 'options' ) );
	}

	// endregion

	// region METHODS

	/**
	 * Validates a value based on passed parameters.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value              The value to validate.
	 * @param   string  $default_key        The key of the default value in the container.
	 * @param   string  $validation_type    The type of validation to perform. Valid values are listed in the ValidationTypesEnum class.
	 * @param   array   $params             Additional params needed for the validation type.
	 *
	 * @throws  NotSupportedException   Thrown if the validation type requested is not supported.
	 *
	 * @return  mixed
	 */
	public function validate_value( $value, string $default_key, string $validation_type, array $params = array() ) {
		switch ( $validation_type ) {
			case ValidationTypesEnum::BOOLEAN:
				return $this->validate_boolean_value( $value, $default_key );
			case ValidationTypesEnum::INTEGER:
				return $this->validate_integer_value( $value, $default_key );
			case ValidationTypesEnum::FLOAT:
				return $this->validate_float_value( $value, $default_key );
			case ValidationTypesEnum::CALLBACK:
				return $this->validate_callback_value( $value, $default_key );
			case ValidationTypesEnum::OPTION:
				return $this->validate_supported_value( $value, $params['options_key'] ?? '', $default_key );
			case ValidationTypesEnum::CUSTOM:
				if ( isset( $params['callable'] ) && is_callable( $params['callable'] ) ) {
					return call_user_func_array( $params['callable'], array( $value, $default_key ) + ( $params['args'] ?? array() ) );
				} else {
					throw new NotSupportedException( 'Custom validation requires a valid callable' );
				}
		}

		throw new NotSupportedException( 'Validation type not supported' );
	}

	/**
	 * Validates a given value as a boolean.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value  The value to validate.
	 * @param   string  $key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found inside the container.
	 *
	 * @return  bool
	 */
	public function validate_boolean_value( $value, string $key ): bool {
		$default = $this->get_default_value_or_throw( $key );
		return Validation::validate_boolean( $value, $default );
	}

	/**
	 * Validates a given value as an int.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value  The value to validate.
	 * @param   string  $key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found inside the container.
	 *
	 * @return  int
	 */
	public function validate_integer_value( $value, string $key ): int {
		$default = $this->get_default_value_or_throw( $key );
		return Validation::validate_integer( $value, $default );
	}

	/**
	 * Validates a given value as a float.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value  The value to validate.
	 * @param   string  $key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found inside the container.
	 *
	 * @return  float
	 */
	public function validate_float_value( $value, string $key ): float {
		$default = $this->get_default_value_or_throw( $key );
		return Validation::validate_float( $value, $default );
	}

	/**
	 * Validates a given value as a callable.
	 *
	 * @param   mixed   $value  The value to validate.
	 * @param   string  $key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found inside the container.
	 *
	 * @return  callable
	 */
	public function validate_callback_value( $value, string $key ): callable {
		$default = $this->get_default_value_or_throw( $key );
		return Validation::validate_callback( $value, $default );
	}

	/**
	 * Validates a given value as a valid option.
	 *
	 * @param   mixed   $value          The value to validate.
	 * @param   string  $options_key    The composite key to retrieve the supported values.
	 * @param   string  $default_key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value or the supported values were not found inside the containers.
	 *
	 * @return  mixed
	 */
	public function validate_supported_value( $value, string $options_key, string $default_key ) {
		$default          = $this->get_default_value_or_throw( $default_key );
		$supported_values = $this->get_supported_options_or_throw( $options_key );

		if ( Arrays::has_string_keys( $supported_values ) ) {
			$supported_values = array_keys( $supported_values );
		}

		return Validation::validate_allowed_value( $value, $supported_values, $default );
	}

	// endregion

	// region HELPERS

	/**
	 * Retrieves a nested value from the container using a composite key.
	 *
	 * @param   string  $key    Composite key of the value to retrieve.
	 *
	 * @noinspection PhpMissingReturnTypeInspection
	 * @return  InexistentPropertyException|mixed
	 */
	protected function get_container_value( string $key ) {
		$boom = explode( '/', $key );
		$key  = array_shift( $boom );

		$value = $this->get_container_entry( $key );
		if ( is_null( $value ) ) {
			return new InexistentPropertyException();
		}

		foreach ( $boom as $key ) {
			if ( isset( $value[ $key ] ) ) {
				$value = $value[ $key ];
			} else {
				return new InexistentPropertyException();
			}
		}

		return $value;
	}

	/**
	 * Retrieves the default value for a given key or throws the exception if not found.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key    The key inside the container.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value or the supported values were not found inside the containers.
	 *
	 * @return  mixed
	 */
	protected function get_default_value_or_throw( string $key ) {
		$default = $this->get_default_value( $key );
		if ( $default instanceof InexistentPropertyException ) {
			throw $default;
		}

		return $default;
	}

	/**
	 * Retrieves the supported options for a given key or throws the exception if not found.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key    The key inside the container.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value or the supported values were not found inside the containers.
	 *
	 * @return  array
	 */
	protected function get_supported_options_or_throw( string $key ): array {
		$options = $this->get_supported_options( $key );
		if ( $options instanceof InexistentPropertyException ) {
			throw $options;
		}

		return $options;
	}

	// endregion
}
