<?php

namespace DeepWebSolutions\Framework\Utilities\Validation;

use DeepWebSolutions\Framework\Foundations\Exceptions\InexistentPropertyException;
use DeepWebSolutions\Framework\Foundations\Exceptions\NotSupportedException;
use DeepWebSolutions\Framework\Foundations\Utilities\Services\AbstractMultiHandlerService;

\defined( 'ABSPATH' ) || exit;

/**
 * Performs various data validation actions against values defined in various given handlers.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Validation
 */
class ValidationService extends AbstractMultiHandlerService {
	// region INHERITED METHODS

	/**
	 * Returns the instance of a given handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handler_id     The ID of the handler to retrieve.
	 *
	 * @return  ValidationHandlerInterface|null
	 */
	public function get_handler( string $handler_id ): ?ValidationHandlerInterface { // phpcs:ignore
		/* @noinspection PhpIncompatibleReturnTypeInspection */
		return parent::get_handler( $handler_id );
	}

	// endregion

	// region METHODS

	/**
	 * Validates a value based on passed parameters.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value              The value to validate.
	 * @param   string  $default_key        The key of the default value in the within the handler.
	 * @param   string  $validation_type    The type of validation to perform. Valid values are listed in the ValidationTypesEnum class.
	 * @param   array   $params             Additional params needed for the validation type.
	 * @param   string  $handler_id         The ID of the handler to use for validation.
	 *
	 * @throws  NotSupportedException   Thrown if the validation type requested is not supported.
	 *
	 * @return  mixed
	 */
	public function validate( $value, string $default_key, string $validation_type, array $params = array(), string $handler_id = 'default' ) {
		switch ( $validation_type ) {
			case ValidationTypesEnum::BOOLEAN:
				return $this->validate_boolean( $value, $default_key, $handler_id );
			case ValidationTypesEnum::INTEGER:
				return $this->validate_integer( $value, $default_key, $handler_id );
			case ValidationTypesEnum::FLOAT:
				return $this->validate_float( $value, $default_key, $handler_id );
			case ValidationTypesEnum::CALLABLE:
				return $this->validate_callable( $value, $default_key, $handler_id );
			case ValidationTypesEnum::CUSTOM:
				if ( isset( $params['callable'] ) && \is_callable( $params['callable'] ) ) {
					return \call_user_func_array( $params['callable'], array( $value, $default_key, $this->get_handler( $handler_id ) ) + ( $params['args'] ?? array() ) );
				} else {
					throw new NotSupportedException( 'Custom validation requires a valid callable' );
				}
		}

		throw new NotSupportedException( 'Validation type not supported' );
	}

	/**
	 * Validates a given value as a boolean using the given handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value          The value to validate.
	 * @param   string  $key            The composite key to retrieve the default value.
	 * @param   string  $handler_id     The ID of the handler to use for validation.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found inside the handler.
	 *
	 * @return  bool
	 */
	public function validate_boolean( $value, string $key, string $handler_id = 'default' ): bool {
		return $this->get_handler( $handler_id )->validate_boolean( $value, $key );
	}

	/**
	 * Validates a given value as an int using the given handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value          The value to validate.
	 * @param   string  $key            The composite key to retrieve the default value.
	 * @param   string  $handler_id     The ID of the handler to use for validation.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found inside the handler.
	 *
	 * @return  int
	 */
	public function validate_integer( $value, string $key, string $handler_id = 'default' ): int {
		return $this->get_handler( $handler_id )->validate_integer( $value, $key );
	}

	/**
	 * Validates a given value as a float using the given handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value          The value to validate.
	 * @param   string  $key            The composite key to retrieve the default value.
	 * @param   string  $handler_id     The ID of the handler to use for validation.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found inside the handler.
	 *
	 * @return  float
	 */
	public function validate_float( $value, string $key, string $handler_id = 'default' ): float {
		return $this->get_handler( $handler_id )->validate_float( $value, $key );
	}

	/**
	 * Validates a given value as a callable using the given handler.
	 *
	 * @param   mixed   $value          The value to validate.
	 * @param   string  $key            The composite key to retrieve the default value.
	 * @param   string  $handler_id     The ID of the handler to use for validation.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found inside the handler.
	 *
	 * @return  callable
	 */
	public function validate_callable( $value, string $key, string $handler_id = 'default' ): callable {
		return $this->get_handler( $handler_id )->validate_callable( $value, $key );
	}

	// endregion

	// region HELPERS

	/**
	 * Returns the class name of the used handler for better type-checking.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	protected function get_handler_class(): string {
		return ValidationHandlerInterface::class;
	}

	// endregion
}
