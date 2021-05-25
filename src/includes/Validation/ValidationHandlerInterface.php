<?php

namespace DeepWebSolutions\Framework\Utilities\Validation;

use DeepWebSolutions\Framework\Foundations\Exceptions\InexistentPropertyException;
use DeepWebSolutions\Framework\Foundations\Utilities\Handlers\HandlerInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Describes an instance of a validation handler compatible with the validation service.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Validation
 */
interface ValidationHandlerInterface extends HandlerInterface {
	/**
	 * Retrieves the default value for a given key.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $key    The key to retrieve the default value for.
	 *
	 * @return  InexistentPropertyException|mixed
	 */
	public function get_default_value( string $key );

	/**
	 * Retrieves a list of all default values.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_known_default_values(): array;

	/**
	 * Retrieves the supported options for a given key.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key    The key to retrieve the supported options for.
	 *
	 * @return  InexistentPropertyException|array
	 */
	public function get_supported_options( string $key );

	/**
	 * Retrieves a list of all supported options configurations.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_known_supported_options(): array;

	/**
	 * Validates a given value as a boolean.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value  The value to validate.
	 * @param   string  $key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found.
	 *
	 * @return  bool
	 */
	public function validate_boolean_value( $value, string $key ): bool;

	/**
	 * Validates a given value as an int.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value  The value to validate.
	 * @param   string  $key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found.
	 *
	 * @return  int
	 */
	public function validate_integer_value( $value, string $key ): int;

	/**
	 * Validates a given value as a float.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value  The value to validate.
	 * @param   string  $key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found.
	 *
	 * @return  float
	 */
	public function validate_float_value( $value, string $key ): float;

	/**
	 * Validates a given value as a callable.
	 *
	 * @param   mixed   $value  The value to validate.
	 * @param   string  $key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found.
	 *
	 * @return  callable
	 */
	public function validate_callable_value( $value, string $key ): callable;

	/**
	 * Validates a given value as a valid option.
	 *
	 * @param   mixed   $value          The value to validate.
	 * @param   string  $options_key    The composite key to retrieve the supported values.
	 * @param   string  $default_key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value or the supported values were not found.
	 *
	 * @return  mixed
	 */
	public function validate_supported_value( $value, string $options_key, string $default_key );
}
